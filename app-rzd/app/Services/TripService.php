<?php

namespace App\Services;

use App\Models\Station;
use App\Models\Route;
use App\Models\Trip;
use App\Models\CarriageType;
use App\Models\Ticket;
use Carbon\Carbon;

class TripService
{
    public function searchTrips(string $fromCity, string $toCity, string $date, int $passengerCount): array
    {
        $startOfDay = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $endOfDay = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();

        $fromStationIds = Station::where('city', $fromCity)->pluck('id');
        $toStationIds = Station::where('city', $toCity)->pluck('id');

        if ($fromStationIds->isEmpty() || $toStationIds->isEmpty()) {
            return [];
        }

        $routeIds = Route::whereIn('start_station_id', $fromStationIds)
            ->whereIn('end_station_id', $toStationIds)
            ->pluck('id');

        if ($routeIds->isEmpty())
            return [];

        $trips = Trip::with(['train.carriages.seats', 'route.startStation', 'route.endStation', 'train'])
            ->whereIn('route_id', $routeIds)
            ->whereBetween('start_timestamp', [$startOfDay, $endOfDay])
            ->where('is_denied', false)
            ->get();

        $tripIds = $trips->pluck('id')->toArray();

        $occupiedRecords = Ticket::query()
            ->whereIn('trip_id', $tripIds)
            ->where('is_canceled', false)
            ->whereExists(function ($query) {
                $query->selectRaw(1)
                    ->from('bookings')
                    ->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')
                    ->whereColumn('booking_passengers.id', 'tickets.booking_passenger_id')
                    ->where(function ($q) {
                        $q->where('bookings.status', 'PAID')
                            ->orWhere(function ($q2) {
                                $q2->where('bookings.status', 'BOOKED')
                                    ->where('bookings.expires_at', '>', now());
                            });
                    });
            })
            ->get()
            ->groupBy('trip_id');

        $result = [];

        foreach ($trips as $trip) {
            $availableSeatsCount = 0;
            $minPriceByType = [];

            $occupiedForTrip = $occupiedRecords->get($trip->id, collect())->pluck('seat_id')->all();

            foreach ($trip->train->carriages as $carriage) {
                $seats = $carriage->seats;
                $totalSeats = $seats->count();

                $occupied = $seats->pluck('id')->intersect($occupiedForTrip)->count();
                $available = $totalSeats - $occupied;
                $availableSeatsCount += $available;

                foreach ($seats as $seat) {
                    $typeId = $carriage->carriage_type_id;
                    $price = (string) $seat->price;
                    if (!isset($minPriceByType[$typeId]) || bccomp($price, (string) $minPriceByType[$typeId], 2) < 0) {
                        $minPriceByType[$typeId] = $price;
                    }
                }
            }

            if ($availableSeatsCount >= $passengerCount) {
                $minPriceArr = [];
                foreach ($minPriceByType as $typeId => $minPrice) {
                    $type = CarriageType::find($typeId);
                    $minPriceArr[] = [
                        'carriage_type_id' => $typeId,
                        'title' => $type ? $type->title : 'Unknown',
                        'min_price' => number_format((float) $minPrice, 2, '.', '')
                    ];
                }

                $result[] = [
                    'trip_id' => $trip->id,
                    'train_title' => $trip->train->title,
                    'route_number' => $trip->route->number,
                    'start_station' => [
                        'id' => $trip->route->startStation->id,
                        'title' => $trip->route->startStation->title,
                        'city' => $trip->route->startStation->city,
                    ],
                    'end_station' => [
                        'id' => $trip->route->endStation->id,
                        'title' => $trip->route->endStation->title,
                        'city' => $trip->route->endStation->city,
                    ],
                    'start_timestamp' => $trip->start_timestamp->toDateTimeString(),
                    'end_timestamp' => $trip->end_timestamp->toDateTimeString(),
                    'available_seats_count' => $availableSeatsCount,
                    'min_price_by_carriage_type' => $minPriceArr,
                ];
            }
        }

        return $result;
    }

    public function getCarriageTypesForTrip(int $tripId): array
    {
        $trip = Trip::with(['train.carriages.type', 'train.carriages.seats', 'route.startStation', 'route.endStation'])
            ->findOrFail($tripId);

        if ($trip->is_denied) {
            abort(404);
        }

        $occupiedTickets = Ticket::query()
            ->where('trip_id', $trip->id)
            ->where('is_canceled', false)
            ->whereExists(function ($query) {
                $query->selectRaw(1)
                    ->from('bookings')
                    ->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')
                    ->whereColumn('booking_passengers.id', 'tickets.booking_passenger_id')
                    ->where(function ($q) {
                        $q->where('bookings.status', 'PAID')
                            ->orWhere(function ($q2) {
                                $q2->where('bookings.status', 'BOOKED')
                                    ->where('bookings.expires_at', '>', now());
                            });
                    });
            })
            ->pluck('seat_id')
            ->toArray();

        $carriagesTypes = [];

        foreach ($trip->train->carriages as $carriage) {
            $seats = $carriage->seats;
            $occupiedCount = $seats->pluck('id')->intersect($occupiedTickets)->count();
            $available = $seats->count() - $occupiedCount;

            if ($available > 0 && $carriage->type) {
                $typeId = $carriage->type->id;
                $title = $carriage->type->title;
                $carriageId = $carriage->id;
                $minPrice = (float) $seats->min('price');
                $maxPrice = (float) $seats->max('price');

                if (isset($carriagesTypes[$typeId])) {
                    $carriagesTypes[$typeId]['seat_number'] += $available;
                    $carriagesTypes[$typeId]['carriage_id'] = min($carriagesTypes[$typeId]['carriage_id'], $carriageId);
                    $carriagesTypes[$typeId]['seat_price_min'] = min($carriagesTypes[$typeId]['seat_price_min'], $minPrice);
                    $carriagesTypes[$typeId]['seat_price_max'] = max($carriagesTypes[$typeId]['seat_price_max'], $maxPrice);
                } else {
                    $carriagesTypes[$typeId] = [
                        'type_id' => $typeId,
                        'type_title' => $title,
                        'seat_number' => $available,
                        'carriage_id' => $carriageId,
                        'seat_price_min' => $minPrice,
                        'seat_price_max' => $maxPrice,
                    ];
                }
            }
        }

        return [
            'trip_id' => $trip->id,
            'train_title' => $trip->train->title,
            'start_timestamp' => $trip->start_timestamp->toDateTimeString(),
            'end_timestamp' => $trip->end_timestamp->toDateTimeString(),
            'route' => [
                'number' => $trip->route->number,
                'start_station' => [
                    'id' => $trip->route->startStation->id,
                    'title' => $trip->route->startStation->title,
                    'city' => $trip->route->startStation->city,
                ],
                'end_station' => [
                    'id' => $trip->route->endStation->id,
                    'title' => $trip->route->endStation->title,
                    'city' => $trip->route->endStation->city,
                ],
            ],
            'carriages_types' => $carriagesTypes,
        ];
    }


    public function getSeatsAndCarriagesForTripByCarriageType(int $tripId, int $carriageTypeId, int $carriageId): array
    {
        $trip = Trip::with(['train.carriages.type', 'train.carriages.seats', 'route.startStation', 'route.endStation'])
            ->findOrFail($tripId);

        if ($trip->is_denied) {
            abort(404, 'Trip is denied.');
        }

        $occupiedSeatIds = Ticket::query()
            ->where('trip_id', $trip->id)
            ->where('is_canceled', false)
            ->whereExists(function ($query) {
                $query->selectRaw(1)
                    ->from('bookings')
                    ->join('booking_passengers', 'bookings.id', '=', 'booking_passengers.booking_id')
                    ->whereColumn('booking_passengers.id', 'tickets.booking_passenger_id')
                    ->where(function ($q) {
                        $q->where('bookings.status', 'PAID')
                            ->orWhere(function ($q2) {
                                $q2->where('bookings.status', 'BOOKED')
                                    ->where('bookings.expires_at', '>', now());
                            });
                    });
            })
            ->pluck('seat_id')
            ->toArray();

        $occupiedSeatIds = collect($occupiedSeatIds);
        $carriagesOut = collect();

        foreach ($trip->train->carriages as $carriage) {
            if ($carriage->type->id !== $carriageTypeId) {
                continue;
            }

            $seats = $carriage->seats;
            $occupiedCount = $occupiedSeatIds->intersect($seats->pluck('id'))->count();
            $availableCount = $seats->count() - $occupiedCount;

            if ($availableCount <= 0) {
                continue;
            }

            $carriagesOut->push([
                'carriage_id' => $carriage->id,
                'carriage_type' => [
                    'id' => $carriage->type->id,
                    'title' => $carriage->type->title,
                    'seats_number' => $carriage->type->seats_number,
                ],
                'number' => $carriage->number,
                'available_seats_count' => $availableCount,
            ]);
        }

        if ($carriagesOut->isEmpty()) {
            abort(404, 'No carriages of this type with available seats found for this trip.');
        }

        $carriage = $trip->train->carriages->firstWhere('id', $carriageId);

        if (!$carriage || $carriage->type->id !== $carriageTypeId) {
            abort(400, 'Specified carriage does not belong to this trip or has wrong type.');
        }

        $seatsOut = [];
        foreach ($carriage->seats as $seat) {
            $isAvailable = !$occupiedSeatIds->contains($seat->id);
            $blockedUntil = null;

            $seatsOut[] = [
                'carriage_id' => $carriage->id,
                'carriage_number' => $carriage->number,
                'seat_id' => $seat->id,
                'number' => $seat->number,
                'price' => number_format((float) $seat->price, 2, '.', ''),
                'is_available' => $isAvailable,
                'reason' => $isAvailable ? null : 'occupied',
                'blocked_until' => $blockedUntil,
            ];
        }

        return [
            'trip_id' => $trip->id,
            'train_title' => $trip->train->title,
            'start_timestamp' => $trip->start_timestamp?->toDateTimeString(),
            'end_timestamp' => $trip->end_timestamp?->toDateTimeString(),
            'route' => [
                'start_station' => [
                    'id' => $trip->route->startStation->id,
                    'title' => $trip->route->startStation->title,
                    'city' => $trip->route->startStation->city,
                ],
                'end_station' => [
                    'id' => $trip->route->endStation->id,
                    'title' => $trip->route->endStation->title,
                    'city' => $trip->route->endStation->city,
                ],
                'number' => $trip->route->number,
            ],
            'carriage_type_id' => $carriageTypeId,
            'carriages' => $carriagesOut->values(),
            'seats' => $seatsOut,
        ];
    }
}

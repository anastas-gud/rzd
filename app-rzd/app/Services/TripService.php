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
        $endOfDay   = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();

        $fromStationIds = Station::where('city', $fromCity)->pluck('id');
        $toStationIds   = Station::where('city', $toCity)->pluck('id');

        if ($fromStationIds->isEmpty() || $toStationIds->isEmpty()) {
            return [];
        }

        $routeIds = Route::whereIn('start_station_id', $fromStationIds)
            ->whereIn('end_station_id', $toStationIds)
            ->pluck('id');

        if ($routeIds->isEmpty()) return [];

        $trips = Trip::with(['train.carriages.seats','route.startStation','route.endStation','train'])
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
                    $price = (string)$seat->price;
                    if (!isset($minPriceByType[$typeId]) || bccomp($price, (string)$minPriceByType[$typeId], 2) < 0) {
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
                        'min_price' => number_format((float)$minPrice, 2, '.', '')
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

    public function getTripDetails(int $tripId): array
    {
        $trip = Trip::with(['train.carriages.type','route.startStation','route.endStation','train'])->findOrFail($tripId);
//        if ($trip->start_timestamp->isPast()) {  //TODO: return this line!!!
//            abort(404);
//        }

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

        $carriagesOut = [];
        foreach ($trip->train->carriages as $carriage) {
            $seats = $carriage->seats;
            $available = $seats->count() - collect($seats->pluck('id'))->intersect($occupiedTickets)->count();

            $minPrice = $seats->min('price');

            $carriagesOut[] = [
                'carriage_id' => $carriage->id,
                'carriage_type' => [
                    'id' => $carriage->type->id ?? null,
                    'title' => $carriage->type->title ?? null,
                    'seats_number' => $carriage->type->seats_number ?? null,
                ],
                'number' => $carriage->number,
                'available_seats_count' => $available,
                'seat_price_min' => number_format((float)$minPrice, 2, '.', ''),
            ];
        }

        return [
            'trip_id' => $trip->id,
            'train_title' => $trip->train->title,
            'start_timestamp' => $trip->start_timestamp->toDateTimeString(),
            'end_timestamp' => $trip->end_timestamp->toDateTimeString(),
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
            'carriages' => $carriagesOut,
        ];
    }

    public function getSeats(int $tripId, ?int $carriageId = null): array
    {
        $trip = Trip::with(['train.carriages.seats'])->findOrFail($tripId);
        //        if ($trip->start_timestamp->isPast()) {  //TODO: return this line!!!
//            abort(404);
//        }

        if ($trip->is_denied) {
            abort(404);
        }

        $carriages = $trip->train->carriages;
        if ($carriageId) {
            $carriages = $carriages->where('id', $carriageId);
            if ($carriages->isEmpty()) abort(400, 'Carriage does not belong to the trip train.');
        }

        $occupied = Ticket::query()
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
            ->get();

        $occupiedBySeat = $occupied->groupBy('seat_id');

        $seatsOut = [];
        $availableCount = 0;

        foreach ($carriages as $carriage) {
            foreach ($carriage->seats as $seat) {
                $isAvailable = true;
                $reason = null;
                $blockedUntil = null;

                $occ = $occupiedBySeat->get($seat->id);
                if ($occ && $occ->isNotEmpty()) {
                    $isAvailable = false;
                    $reason = 'occupied';
                }

                if ($isAvailable) $availableCount++;

                $seatsOut[] = [
                    'seat_id' => $seat->id,
                    'carriage_id' => $carriage->id,
                    'number' => $seat->number,
                    'price' => number_format((float)$seat->price,2,'.',''),
                    'is_available' => $isAvailable,
                    'reason' => $reason,
                    'blocked_until' => $blockedUntil,
                ];
            }
        }

        return [
            'trip_id' => $trip->id,
            'seats' => $seatsOut,
            'available_count' => $availableCount,
        ];
    }
}

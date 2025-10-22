<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\BookingPassengerPrivilege;
use App\Models\Document;
use App\Models\Name;
use App\Models\Contact;
use App\Models\ServiceBooking;
use App\Models\Service;
use App\Models\Privilege;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\Trip;
use App\Helpers\BookingPlaceholders;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function createBooking(int $tripId, array $selectedSeatIds, array $passengers): array
    {
        $trip = Trip::findOrFail($tripId);
        if ($trip->is_denied) throw new \Exception('Trip denied');
        // if ($trip->start_timestamp->isPast()) throw new \Exception('Trip already started'); //TODO: return this line!!!

        if (count($selectedSeatIds) !== count($passengers)) {
            throw new \Exception('Количество пассажиров должно совпадать с количеством выбранных мест');
        }

        $user = Auth::user();
        if (!$user) {
            throw new \Exception('Пользователь не авторизован');
        }

        return DB::transaction(function() use($trip, $selectedSeatIds, $passengers, $user) {
            $now = Carbon::now();
            $ttlMinutes = (int)config('booking.ttl_minutes', 60);
            $expiresAt = $now->copy()->addMinutes($ttlMinutes);

            $seats = Seat::whereIn('id', $selectedSeatIds)->lockForUpdate()->get();
            if ($seats->count() !== count($selectedSeatIds)) {
                throw new \Exception('Some seats not found');
            }

            foreach ($seats as $seat) {
                $carriage = $seat->carriage;
                if ($carriage->train_id !== $trip->train_id) {
                    throw new \Exception("Seat {$seat->id} doesn't belong to this trip's train");
                }
            }

            $conflicting = Ticket::where('trip_id', $trip->id)
                ->whereIn('seat_id', $selectedSeatIds)
                ->where('is_canceled', false)
                ->whereHas('bookingPassenger.booking', function($q) {
                    $q->where(function($q2) {
                        $q2->where('status', 'PAID')
                            ->orWhere(function($q3) {
                                $q3->where('status', 'BOOKED')
                                    ->where('expires_at', '>', Carbon::now());
                            });
                    });
                })
                ->lockForUpdate()
                ->get();

            if ($conflicting->isNotEmpty()) {
                $occupiedSeats = $conflicting->pluck('seat_id')->unique()->values()->all();
                throw new \Exception('Seats already booked: '.implode(', ', $occupiedSeats));
            }

            $booking = Booking::create([
                'user_id' => $user->id,
                'status' => 'BOOKED',
                'expires_at' => $expiresAt,
                'total_price' => 0,
            ]);

            $ticketsCreated = [];
            $total = '0.00';

            foreach ($seats as $index => $seat) {
                $passenger = $passengers[$index];

                // создаем ФИО
                $name = Name::create([
                    'name' => $passenger['name'] ?? 'Имя',
                    'surname' => $passenger['surname'] ?? 'Фамилия',
                    'patronymic' => $passenger['patronymic'] ?? null,
                ]);

                // создаем документ
                $document = Document::create([
                    'date_of_birth' => $passenger['date_of_birth'],
                    'serial' => $passenger['serial'],
                    'number' => $passenger['number'],
                    'type' => $passenger['document_type'] ?? 'PASSPORT',
                ]);

                $bp = BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'document_id' => $document->id,
                    'name_id' => $name->id,
                    'contact_id' => $user->contact_id,
                ]);

                $ticketCode = strtoupper(Str::substr(Str::uuid()->toString(), 0, 8));
                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'trip_id' => $trip->id,
                    'final_price' => $seat->price,
                    'seat_id' => $seat->id,
                    'booking_passenger_id' => $bp->id,
                    'ticket_code' => $ticketCode,
                    'is_canceled' => false,
                ]);

                $ticketsCreated[] = $ticket;
                $total = bcadd((string)$total, (string)$seat->price, 2);
            }

            $booking->total_price = $total;
            $booking->save();

            return [
                'booking_id' => $booking->id,
                'expires_at' => $expiresAt->toDateTimeString(),
                'tickets' => array_map(function($t){
                    return [
                        'ticket_id' => $t->id,
                        'seat_id' => $t->seat_id,
                        'booking_passenger_id' => $t->booking_passenger_id,
                        'ticket_code' => $t->ticket_code,
                    ];
                }, $ticketsCreated),
                'total_price' => number_format((float)$total, 2, '.', ''),
            ];
        });
    }

    public function getOptions(int $bookingId): array
    {
        $booking = Booking::findOrFail($bookingId);
        if ($booking->status !== 'BOOKED' || $booking->expires_at <= Carbon::now()) {
            throw new \RuntimeException('Need to select seats and create booking first');
        }
        if ($booking->expires_at <= Carbon::now()) {
            throw new \RuntimeException('Booking is expired, need to send user to books seats again');
        }
        $privileges = Privilege::all(['id','title','description','discount']);
        $services = Service::all(['id','title','description','base_price']);
        return [
            'booking_id' => $booking->id,
            'privileges' => $privileges,
            'services' => $services,
        ];
    }

    public function applyOptions(int $bookingId, array $privilegesInput, array $servicesInput): array
    {
        return DB::transaction(function() use($bookingId, $privilegesInput, $servicesInput) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);
            if ($booking->status !== 'BOOKED' || $booking->expires_at <= Carbon::now()) {
                throw new \RuntimeException('Need to select seats and create booking first');
            }
            if ($booking->expires_at <= Carbon::now()) {
                throw new \RuntimeException('Booking is expired, need to send user to book seats again');
            }

            $servicesSaved = [];
            foreach ($servicesInput as $s) {
                $service = Service::findOrFail($s['service_id']);
                $count = (int)$s['count'];
                $sb = ServiceBooking::create([
                    'service_id' => $service->id,
                    'booking_id' => $booking->id,
                    'count' => $count,
                    'current_price' => $service->base_price,
                ]);
                $servicesSaved[] = [
                    'id' => $sb->id,
                    'service_id' => $service->id,
                    'count' => $count,
                    'current_price' => number_format((float)$service->base_price,2,'.',''),
                ];
            }

            $tickets = Ticket::whereHas('bookingPassenger', function($q) use ($bookingId) {
                $q->where('booking_id', $bookingId);
            })->get();
            $totalTicketsSum = '0.00';
            foreach ($tickets as $ticket) {
                $bpId = $ticket->booking_passenger_id;
                $privRecord = BookingPassengerPrivilege::where('booking_passenger_id', $bpId)->first();
                $seatPrice = (string)$ticket->seat->price;
                $final = $seatPrice;
                if ($privRecord) {
                    $priv = Privilege::find($privRecord->privilege_id);
                    if ($priv && $priv->discount) {
                        // interpret discount as percent
                        $discountPct = (string)$priv->discount;
                        $multiplier = bcsub('1', bcdiv($discountPct, '100', 4), 4);
                        $final = bcmul($seatPrice, $multiplier, 2);
                    }
                }
                $ticket->final_price = $final;
                $ticket->save();
                $totalTicketsSum = bcadd($totalTicketsSum, (string)$final, 2);
            }

            $totalServices = '0.00';
            $serviceBookings = ServiceBooking::where('booking_id', $booking->id)->get();
            foreach ($serviceBookings as $sb) {
                $line = bcmul((string)$sb->current_price, (string)$sb->count, 2);
                $totalServices = bcadd($totalServices, $line, 2);
            }

            $booking->total_price = bcadd($totalTicketsSum, $totalServices, 2);
            $booking->save();

            $ticketsOut = Ticket::whereHas('bookingPassenger', function($q) use ($bookingId) {
                $q->where('booking_id', $bookingId);
            })->get()->map(function($t) {
                return [
                    'ticket_id' => $t->id,
                    'final_price' => number_format((float)$t->final_price, 2, '.', ''),
                ];
            });

            return [
                'booking_id' => $booking->id,
                'total_price' => number_format((float)$booking->total_price,2,'.',''),
                'tickets' => $ticketsOut,
                'services_booking' => $servicesSaved,
            ];
        });
    }

    public function passengersForm(int $bookingId): array
    {
        $booking = Booking::findOrFail($bookingId);
        if ($booking->status !== 'BOOKED' || $booking->expires_at <= Carbon::now()) {
            throw new \RuntimeException('Need to select seats and create booking first');
        }
        if ($booking->expires_at <= Carbon::now()) {
            throw new \RuntimeException('Booking is expired, need to send user to book seats again');
        }

        $tickets = Ticket::whereHas('bookingPassenger', function($q) use ($bookingId) {
            $q->where('booking_id', $bookingId);
        })->with(['seat.carriage','bookingPassenger'])->get();

        $passengers = [];
        foreach ($tickets as $ticket) {
            $bp = $ticket->bookingPassenger;
            $passengers[] = [
                'booking_passenger_id' => $bp->id,
                'seat_id' => $ticket->seat->id,
                'seat_number' => $ticket->seat->number,
                'carriage_number' => $ticket->seat->carriage->number,
                'required_fields' => [
                    'name' => true,
                    'surname' => true,
                    'patronymic' => false,
                    'document_type' => ['PASSPORT','BIRTH CERTIFICATE'],
                    'serial' => true,
                    'number' => true,
                    'date_of_birth' => true,
                    'contact_phone' => true,
                    'contact_email' => false,
                ],
            ];
        }

        return [
            'booking_id' => $booking->id,
            'passengers' => $passengers,
            'booking_expires_at' => $booking->expires_at->toDateTimeString(),
        ];
    }

    public function updatePassengers(int $bookingId, array $passengersInput): array
    {
        return DB::transaction(function() use($bookingId, $passengersInput) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);
            $booking = Booking::findOrFail($bookingId);
            if ($booking->status !== 'BOOKED' || $booking->expires_at <= Carbon::now()) {
                throw new \RuntimeException('Need to select seats and create booking first');
            }
            if ($booking->expires_at <= Carbon::now()) {
                throw new \RuntimeException('Booking is expired, need to send user to book seats again');
            }

            $updated = [];

            foreach ($passengersInput as $p) {
                $bpId = $p['booking_passenger_id'];
                $bp = BookingPassenger::where('id', $bpId)->where('booking_id', $bookingId)->first();
                if (!$bp) throw new \RuntimeException("booking_passenger {$bpId} does not belong to booking {$bookingId}");

                $nameData = $p['name'];
                $name = $bp->name;
                if ($name) {
                    $name->update([
                        'name' => $nameData['name'],
                        'surname' => $nameData['surname'],
                        'patronymic' => $nameData['patronymic'] ?? null,
                        'updated_at' => Carbon::now(),
                    ]);
                } else {
                    $name = Name::create([
                        'name' => $nameData['name'],
                        'surname' => $nameData['surname'],
                        'patronymic' => $nameData['patronymic'] ?? null,
                    ]);
                    $bp->name_id = $name->id;
                }

                // update document
                $docData = $p['document'];
                $doc = $bp->document;
                if ($doc) {
                    $doc->update([
                        'type_of_document' => $docData['type_of_document'],
                        'serial' => $docData['serial'],
                        'number' => $docData['number'],
                        'date_of_birth' => $docData['date_of_birth'],
                        'updated_at' => Carbon::now(),
                    ]);
                } else {
                    $doc = Document::create([
                        'type_of_document' => $docData['type_of_document'],
                        'serial' => $docData['serial'],
                        'number' => $docData['number'],
                        'date_of_birth' => $docData['date_of_birth'],
                    ]);
                    $bp->document_id = $doc->id;
                }

                $contactData = $p['contact'] ?? [];
                if ($bp->contact) {
                    $bp->contact->update([
                        'phone' => $contactData['phone'] ?? $bp->contact->phone,
                        'email' => $contactData['email'] ?? $bp->contact->email,
                        'updated_at' => Carbon::now(),
                    ]);
                } else {
                    $contact = Contact::create([
                        'phone' => $contactData['phone'] ?? null,
                        'email' => $contactData['email'] ?? null,
                    ]);
                    $bp->contact_id = $contact->id;
                }

                $bp->save();

                $updated[] = [
                    'booking_passenger_id' => $bp->id,
                    'name_id' => $bp->name_id,
                    'document_id' => $bp->document_id,
                    'contact_id' => $bp->contact_id,
                ];
            }

            return [
                'booking_id' => $bookingId,
                'updated_passengers' => $updated,
            ];
        });
    }

    public function summary(int $bookingId): array
    {
        $booking = Booking::with(['passengers.name','passengers.document','passengers.contact','servicesBooking','passengers.privileges','passengers.tickets'])->findOrFail($bookingId);
        if ($booking->status !== 'BOOKED' || $booking->expires_at <= Carbon::now()) {
            throw new \RuntimeException('Need to select seats and create booking first');
        }
        if ($booking->expires_at <= Carbon::now()) {
            throw new \RuntimeException('Booking is expired, need to send user to book seats again');
        }

        $tickets = Ticket::whereHas('bookingPassenger', function($q) use ($bookingId) {
            $q->where('booking_id', $bookingId);
        })->with(['seat.carriage','trip.route.startStation','trip.route.endStation','bookingPassenger.name','bookingPassenger.document','bookingPassenger.contact'])
            ->get();

        $ticketsOut = [];
        foreach ($tickets as $t) {
            $bp = $t->bookingPassenger;
            $ticketsOut[] = [
                'ticket_id' => $t->id,
                'ticket_code' => $t->ticket_code,
                'seat' => [
                    'seat_id' => $t->seat->id,
                    'number' => $t->seat->number,
                    'carriage_number' => $t->seat->carriage->number,
                    'carriage_type' => $t->seat->carriage->type->title ?? null,
                ],
                'trip' => [
                    'trip_id' => $t->trip->id,
                    'start_timestamp' => $t->trip->start_timestamp->toDateTimeString(),
                    'end_timestamp' => $t->trip->end_timestamp->toDateTimeString(),
                    'start_station' => [
                        'id' => $t->trip->route->startStation->id,
                        'title' => $t->trip->route->startStation->title,
                        'city' => $t->trip->route->startStation->city,
                    ],
                    'end_station' => [
                        'id' => $t->trip->route->endStation->id,
                        'title' => $t->trip->route->endStation->title,
                        'city' => $t->trip->route->endStation->city,
                    ],
                ],
                'passenger' => [
                    'name' => ($bp->name ? ($bp->name->name . ' ' . $bp->name->surname) : null),
                    'document' => ($bp->document ? ($bp->document->type_of_document . ' ' . $bp->document->serial . ' ' . $bp->document->number) : null),
                    'contact' => [
                        'phone' => $bp->contact->phone ?? null,
                        'email' => $bp->contact->email ?? null,
                    ],
                ],
                'final_price' => number_format((float)$t->final_price,2,'.',''),
            ];
        }

        $servicesOut = [];
        foreach ($booking->servicesBooking as $sb) {
            $service = Service::find($sb->service_id);
            $servicesOut[] = [
                'service_id' => $service->id,
                'title' => $service->title,
                'count' => $sb->count,
                'price_per' => number_format((float)$sb->current_price,2,'.',''),
            ];
        }

        $privilegesOut = [];
        $privRecs = BookingPassengerPrivilege::whereIn('booking_passenger_id',$booking->passengers->pluck('id'))->get();
        foreach ($privRecs as $pr) {
            $priv = Privilege::find($pr->privilege_id);
            $privilegesOut[] = [
                'booking_passenger_id' => $pr->booking_passenger_id,
                'title' => $priv->title ?? null,
                'discount' => number_format((float)$priv->discount,2,'.',''),
            ];
        }

        return [
            'booking_id' => $booking->id,
            'status' => $booking->status,
            'expires_at' => $booking->expires_at->toDateTimeString(),
            'total_price' => number_format((float)$booking->total_price,2,'.',''),
            'tickets' => $ticketsOut,
            'services' => $servicesOut,
            'privileges' => $privilegesOut,
        ];
    }
}

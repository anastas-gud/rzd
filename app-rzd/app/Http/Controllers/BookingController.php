<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Http\Requests\BookingOptionsRequest;
use App\Http\Requests\BookingPassengersUpdateRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    protected BookingService $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;
    }

    // POST /api/bookings
    public function create(CreateBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $res = $this->service->createBooking(
            (int)$data['trip_id'],
            $data['selected_seat_ids'],
        );

        return response()->json($res, 201);
    }

    // GET /api/bookings/{booking}/options
    public function options(Booking $booking): JsonResponse
    {
        try {
            $res = $this->service->getOptions($booking->id);
            return response()->json($res);
        } catch (\RuntimeException $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }

    // POST /api/bookings/{booking}/options
    public function applyOptions(BookingOptionsRequest $request, Booking $booking): JsonResponse
    {
        $data = $request->validated();
        try {
            $res = $this->service->applyOptions($booking->id, $data['privileges'] ?? [], $data['services'] ?? []);
            return response()->json($res);
        } catch (\RuntimeException $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }

    // GET /api/bookings/{booking}/passengers/form
    public function passengersForm(Booking $booking): JsonResponse
    {
        try {
            $res = $this->service->passengersForm($booking->id);
            return response()->json($res);
        } catch (\RuntimeException $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }

    // POST /api/bookings/{booking}/passengers
    public function updatePassengers(BookingPassengersUpdateRequest $request, Booking $booking): JsonResponse
    {
        $data = $request->validated();
        try {
            $res = $this->service->updatePassengers($booking->id, $data['passengers']);
            return response()->json($res);
        } catch (\RuntimeException $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }

    // GET /api/bookings/{booking}/summary
    public function summary(Booking $booking): JsonResponse
    {
        try {
            $res = $this->service->summary($booking->id);
            return response()->json($res);
        } catch (\RuntimeException $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }
    // GET /api/bookings/active
    public function active(): JsonResponse
    {
        try {
            $res = $this->service->getUserBookings();
            return response()->json($res);
        } catch (\RuntimeException $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }
    // POST /api/bookings/{booking}/pay
    public function pay(Booking $booking): JsonResponse
    {
        try {
            $res = $this->service->pay($booking->id);
            return response()->json($res);
        } catch (\RuntimeException $ex) {
            return response()->json(['message' => $ex->getMessage()], 400);
        }
    }
}

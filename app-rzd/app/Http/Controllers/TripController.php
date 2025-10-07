<?php
namespace App\Http\Controllers;

use App\Http\Requests\SearchTripsRequest;
use App\Http\Requests\TripSeatsRequest;
use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Http\JsonResponse;

class TripController extends Controller
{
    protected TripService $service;

    public function __construct(TripService $service)
    {
        $this->service = $service;
    }

    // GET /api/search-trips
    public function search(SearchTripsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $res = $this->service->searchTrips(
            $data['from_city'],
            $data['to_city'],
            $data['date'],
            (int)$data['passenger_count']
        );
        return response()->json($res);
    }

    // GET /api/trips/{trip}
    public function show(Trip $trip): JsonResponse
    {
//        if ($trip->start_timestamp->isPast()) {   //TODO: return this line!!!
//            return response()->json(['message' => 'not found'], 404);
//        }

        if ($trip->is_denied) {
            return response()->json(['message' => 'not found'], 404);
        }

        $details = $this->service->getTripDetails($trip->id);
        return response()->json($details);
    }

    // GET /api/trips/{trip}/seats
    public function seats(TripSeatsRequest $request, Trip $trip): JsonResponse
    {
        $params = $request->validated();
        $carriageId = $params['carriage_id'] ?? null;
        $res = $this->service->getSeats($trip->id, $carriageId);
        return response()->json($res);
    }
}

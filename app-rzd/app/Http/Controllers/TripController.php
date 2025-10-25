<?php
namespace App\Http\Controllers;

use App\Http\Requests\SearchTripsRequest;
use App\Http\Requests\TripSeatsRequest;
use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Http\JsonResponse;
use Log;

class TripController extends Controller
{
    protected TripService $service;

    public function __construct(TripService $service)
    {
        $this->service = $service;
    }

    // GET /api/search-trips
    public function search(SearchTripsRequest $request)
    {
        $data = $request->validated();
        $fromCity = $data['from_city'];
        $toCity = $data['to_city'];
        $date = $data['date'];
        $passengerCount = (int)$data['passenger_count'];

        $trips = $this->service->searchTrips(
            $fromCity,
            $toCity,
            $date,
            $passengerCount            
        );
        return view('search-trips', [
            'trips' => $trips,
            'search' => compact('fromCity', 'toCity', 'date', 'passengerCount'),
        ]);
    }

    // GET /api/trips/{trip}
    public function show(Trip $trip)
    {
    //    if ($trip->start_timestamp->isPast()) {   //TODO: return this line!!!
    //        return response()->json(['message' => 'not found'], 404);
    //    }

        abort_if($trip->is_denied, 404);

        $details = $this->service->getTripDetails($trip->id);
        return view('trips', compact('details'));
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

<?php
namespace App\Http\Controllers;

use App\Http\Requests\SearchTripsRequest;
use App\Http\Requests\TripSeatsRequest;
use App\Models\Carriage;
use App\Models\CarriageType;
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
        $passengerCount = (int) $data['passenger_count'];

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

    // GET /api/trips/{trip}/service
    public function show(Trip $trip)
    {
        //    if ($trip->start_timestamp->isPast()) {   //TODO: return this line!!!
        //        return response()->json(['message' => 'not found'], 404);
        //    }
        abort_if($trip->is_denied, 404);
        $tripCarriageTypes = $this->service->getCarriageTypesForTrip($trip->id);
        return view('trip-type-carriage', compact('tripCarriageTypes'));
    }

    // GET /api/trips/{trip}/{carriage_type}/{carriage}/seats
    public function seats(Trip $trip, CarriageType $carriageType, Carriage $carriage)
    {
        abort_if($trip->is_denied, 404);        
        abort_if($carriageType->is_denied, 404);
        abort_if($carriage->is_denied, 404);
        $tripId = $trip->id;
        $carriageTypeId = $carriageType->id;
        $carriageId = $carriage->id;

        $seatsAndCarriages = $this->service->getSeatsAndCarriagesForTripByCarriageType($tripId, $carriageTypeId, $carriageId);
        return view('trip-seats', compact('seatsAndCarriages'));
    }
}

<?php
namespace App\Http\Requests;

class TripSeatsRequest extends ApiRequest
{
    public function authorize() { return true; }
    public function rules()
    {
        return [
            'trip_id' => 'required|integer|exists:trips,id',
            'carriage_type_id' => 'required|integer|exists:carriage_types,id',
            'carriage_id' => 'nullable|integer|exists:carriages,id',
        ];
    }
}

<?php
namespace App\Http\Requests;

class TripSeatsRequest extends ApiRequest
{
    public function authorize() { return true; }
    public function rules()
    {
        return [
            'trip_id' => 'required|integer|exists:trips,id',
            'carriage_id' => 'nullable|integer|exists:carriages,id',
            'requested_count' => 'nullable|integer|min:1|max:10',
        ];
    }
}

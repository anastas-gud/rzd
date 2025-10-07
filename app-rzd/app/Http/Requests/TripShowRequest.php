<?php
namespace App\Http\Requests;

class TripShowRequest extends ApiRequest
{
    public function authorize() { return true; }
    public function rules() { return ['trip_id' => 'required|integer|exists:trips,id']; }
}

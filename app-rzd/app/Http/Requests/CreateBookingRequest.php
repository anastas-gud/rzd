<?php
namespace App\Http\Requests;

class CreateBookingRequest extends ApiRequest
{
    public function authorize() { return true; }
    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'selected_seat_ids' => ['required', 'array', 'min:1'],
            'selected_seat_ids.*' => ['integer', 'exists:seats,id'],
        ];
    }
}

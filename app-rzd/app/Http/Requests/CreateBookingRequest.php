<?php
namespace App\Http\Requests;

class CreateBookingRequest extends ApiRequest
{
    public function authorize() { return true; }
    public function rules()
    {
        return [
            'trip_id' => 'required|integer|exists:trips,id',
            'selected_seat_ids' => 'required|array|min:1',
            'selected_seat_ids.*' => 'integer|distinct|exists:seats,id',
            'contact' => 'nullable|array',
            'contact.phone' => 'nullable|string',
            'contact.email' => 'nullable|email',
        ];
    }
}

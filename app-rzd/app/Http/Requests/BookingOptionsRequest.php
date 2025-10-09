<?php
namespace App\Http\Requests;

class BookingOptionsRequest extends ApiRequest
{
    public function authorize() { return true; }
    public function rules()
    {
        return [
            'booking_id' => 'required|integer|exists:bookings,id',
            'privileges' => 'nullable|array',
            'privileges.*.booking_passenger_id' => 'required|integer|exists:booking_passengers,id',
            'privileges.*.privilege_id' => 'required|integer|exists:privileges,id',
            'services' => 'nullable|array',
            'services.*.service_id' => 'required|integer|exists:services,id',
            'services.*.count' => 'required|integer|min:1',
        ];
    }
}

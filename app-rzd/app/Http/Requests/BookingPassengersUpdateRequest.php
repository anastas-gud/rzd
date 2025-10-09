<?php
namespace App\Http\Requests;

class BookingPassengersUpdateRequest extends ApiRequest
{
    public function authorize() { return true; }
    public function rules()
    {
        return [
            'booking_id' => 'required|integer|exists:bookings,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.booking_passenger_id' => 'required|integer|exists:booking_passengers,id',
            'passengers.*.name.name' => 'required|string|min:1',
            'passengers.*.name.surname' => 'required|string|min:1',
            'passengers.*.name.patronymic' => 'nullable|string',
            'passengers.*.document.type_of_document' => 'required|in:PASSPORT,BIRTH CERTIFICATE',
            'passengers.*.document.serial' => 'required|string',
            'passengers.*.document.number' => 'required|string',
            'passengers.*.document.date_of_birth' => 'required|date|before:today',
            'passengers.*.contact.phone' => 'nullable|string',
            'passengers.*.contact.email' => 'nullable|email',
        ];
    }
}

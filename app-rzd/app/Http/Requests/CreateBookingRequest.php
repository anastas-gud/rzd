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

            'passengers' => ['required', 'array'],
            'passengers.*.name' => ['required', 'string', 'max:255'],
            'passengers.*.surname' => ['required', 'string', 'max:255'],
            'passengers.*.patronymic' => ['nullable', 'string', 'max:255'],
            'passengers.*.document_type' => ['required', 'string', 'in:PASSPORT,BIRTH CERTIFICATE'],
            'passengers.*.number' => ['required', 'string', 'max:50'],
            'passengers.*.serial' => ['required', 'string', 'max:50'],
            'passengers.*.date_of_birth' => ['required', 'date', 'before:today'],
        ];
    }
}

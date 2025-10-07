<?php
namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SearchTripsRequest extends ApiRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $today = now()->format('Y-m-d');
        return [
            'from_city' => 'required|string|min:2',
            'to_city' => 'required|string|min:2|different:from_city',
//            'date' => ['required','date_format:Y-m-d','after_or_equal:'.$today],   //TODO: return this line!!!
            'date' => ['required','date_format:Y-m-d'],
            'passenger_count' => 'required|integer|min:1|max:10',
        ];
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Http;

class SearchTripsForm extends Component
{
    public $from_city = '';
    public $to_city = '';
    public $date = '';
    public $passenger_count = 1;



    protected $rules = [
        'from_city' => 'required|string|min:2',
        'to_city' => 'required|string|min:2',
        'date' => 'required|date|after:today',
        'passenger_count' => 'required|integer|min:1|max:10'
    ];

    public function mount()
    {
        $this->from_city = request('from_city', $this->from_city);
        $this->to_city = request('to_city', $this->to_city);
        $this->date = request('date', $this->date);
        $this->passenger_count = request('passenger_count', $this->passenger_count);
    }

    public function search()
    {
        // $this->validate();

        return redirect()->route('search-trips', [
            'from_city' => $this->from_city,
            'to_city' => $this->to_city,
            'date' => $this->date,
            'passenger_count' => $this->passenger_count,
        ]);
    }

    public function incrementPassengers()
    {
        if ($this->passenger_count < 10) {
            $this->passenger_count++;
        }
    }

    public function decrementPassengers()
    {
        if ($this->passenger_count > 1) {
            $this->passenger_count--;
        }
    }

    public function render()
    {
        return view('livewire.search-trips-form');
    }
}
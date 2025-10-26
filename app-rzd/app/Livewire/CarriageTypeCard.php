<?php

namespace App\Livewire;

use Livewire\Component;

class CarriageTypeCard extends Component
{
    public $trip_id;
    public $type_id;
    public $type_title;
    public $carriage_id;
    public $seat_number;
    public $seat_price_min;
    public $seat_price_max;

    public function chooseSeats()
    {
        return redirect()->route('trip-seats', [
            'trip' => $this->trip_id,
            'carriage_type' => $this->type_id,
            'carriage' => $this->carriage_id,
        ]);
    }

    public function render()
    {
        return view('livewire.carriage-type-card');
    }
}


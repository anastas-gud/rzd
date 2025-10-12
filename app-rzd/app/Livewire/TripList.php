<?php

namespace App\Livewire;

use Livewire\Component;

class TripList extends Component
{
    public $trips = [];

    public function mount($trips = [])
    {
        $this->trips = $trips;
    }

    public function render()
    {
        return view('livewire.trip-list');
    }
}
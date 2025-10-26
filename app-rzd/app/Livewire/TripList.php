<?php

namespace App\Livewire;

use Livewire\Component;

class TripList extends Component
{
    public $trips;

    public function render()
    {
        return view('livewire.trip-list');
    }
}
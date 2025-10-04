<?php

namespace App\Livewire;

use Livewire\Component;

class SearchForm extends Component
{
    public $from = '';
    public $to = '';
    public $date = '';
    public $passengers = 1;

    protected $rules = [
        'from' => 'required|string|min:2',
        'to' => 'required|string|min:2',
        'date' => 'required|date|after:today',
        'passengers' => 'required|integer|min:1|max:10'
    ];

    public function mount($from = '', $to = '', $date = '', $passengers = 1)
    {
        $this->from = $from;
        $this->to = $to;
        $this->date = $date;
        $this->passengers = $passengers;
    }

    public function search()
    {
        $this->validate();

        return redirect()->route('routes.index', [
            'from' => $this->from,
            'to' => $this->to,
            'date' => $this->date,
            'passengers' => $this->passengers
        ]);
    }

    public function incrementPassengers()
    {
        if ($this->passengers < 10) {
            $this->passengers++;
        }
    }

    public function decrementPassengers()
    {
        if ($this->passengers > 1) {
            $this->passengers--;
        }
    }

    public function render()
    {
        return view('livewire.search-form');
    }
}
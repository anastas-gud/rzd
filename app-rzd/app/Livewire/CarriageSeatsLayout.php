<?php

namespace App\Livewire;

use App\Models\CarriageType;
use Livewire\Component;

class CarriageSeatsLayout extends Component
{
    public $tripId;
    public $carriageId;
    public $carriageTypeId;
    public $layout = [];
    public $seats = [];
    public $selectedSeatIds = [];

    public function mount(int $tripId, int $carriageTypeId, int $carriageId, array $seats)
    {
        $this->tripId = $tripId;
        $this->carriageId = $carriageId;
        $this->carriageTypeId = $carriageTypeId;
        $this->seats = $seats;

        $carriageType = CarriageType::findOrFail($carriageTypeId);
        $this->layout = json_decode($carriageType->layout_json, true)['seats'] ?? [];
    }

    public function toggleSeat(int $seatId)
    {
        $seat = collect($this->seats)->firstWhere('seat_id', $seatId);

        if (!$seat || !$seat['is_available']) {
            return;
        }

        if (in_array($seatId, $this->selectedSeatIds)) {
            $this->selectedSeatIds = array_values(array_diff($this->selectedSeatIds, [$seatId]));
        } else {
            if (count($this->selectedSeatIds) < 10) {
                $this->selectedSeatIds[] = $seatId;
            }
        }
    }

    public function render()
    {
        return view('livewire.carriage-seats-layout');
    }
}

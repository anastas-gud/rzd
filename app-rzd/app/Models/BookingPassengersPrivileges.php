<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingPassengersPrivileges extends Pivot
{
    protected $table = 'booking_passenger_privileges';

    /**
     * Получить сумму скидки для этого применения льготы
     */
    public function getDiscountAmount($basePrice)
    {
        return $basePrice * ($this->privilege->discount / 100);
    }
}

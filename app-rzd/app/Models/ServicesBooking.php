<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ServicesBooking extends Pivot
{
    protected $table = 'services_booking';
    protected $casts = [
        'count' => 'integer',
        'current_price' => 'decimal:2',
    ];

    /**
     * Получить общую стоимость для этой услуги
     */
    public function getTotalPriceAttribute()
    {
        return $this->current_price * $this->count;
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    use HasFactory;
    protected $table = 'services_booking';
    protected $fillable = ['service_id','booking_id','count','current_price','created_at','updated_at'];
    protected $casts = ['current_price' => 'decimal:2'];

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Получить общую стоимость для этой услуги
     */
    public function getTotalPriceAttribute()
    {
        return $this->current_price * $this->count;
    }
}

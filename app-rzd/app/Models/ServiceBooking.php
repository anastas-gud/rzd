<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    protected $table = 'services_booking';
    protected $fillable = ['service_id','booking_id','count','current_price','created_at','updated_at'];
    protected $casts = ['current_price' => 'decimal:2'];
}

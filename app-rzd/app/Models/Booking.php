<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = ['user_id','status','expires_at','total_price','created_at','updated_at'];
    protected $casts = [
        'expires_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    public function passengers(): HasMany { return $this->hasMany(BookingPassenger::class); }
    public function servicesBooking() { return $this->hasMany(ServiceBooking::class, 'booking_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

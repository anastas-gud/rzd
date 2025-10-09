<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
        'user_id','trip_id','final_price','seat_id','booking_passenger_id','ticket_code','is_canceled','created_at','updated_at','booking_id'
    ];
    protected $casts = [
        'final_price' => 'decimal:2',
        'is_canceled' => 'boolean',
    ];

    public function bookingPassenger(): BelongsTo { return $this->belongsTo(BookingPassenger::class, 'booking_passenger_id'); }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class, 'booking_id'); }
    public function seat(): BelongsTo { return $this->belongsTo(Seat::class); }
    public function trip(): BelongsTo { return $this->belongsTo(Trip::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

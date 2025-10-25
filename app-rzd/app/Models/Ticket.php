<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';
    protected $fillable = [
        'user_id','trip_id','final_price','seat_id','booking_passenger_id','ticket_code','is_canceled','created_at','updated_at','booking_id'
    ];
    protected $casts = [
        'final_price' => 'decimal:2',
        'is_canceled' => 'boolean',
    ];

    public function bookingPassenger(): BelongsTo { return $this->belongsTo(BookingPassenger::class, 'booking_passenger_id'); }
    public function booking()
    {
        return $this->hasOneThrough(
            \App\Models\Booking::class,
            \App\Models\BookingPassenger::class,
            'id',                // Foreign key on booking_passengers (local key for this relation)
            'id',                // Foreign key on bookings table
            'booking_passenger_id', // Local key on tickets table
            'booking_id'            // Local key on booking_passengers table
        );
    }
    public function seat(): BelongsTo { return $this->belongsTo(Seat::class); }
    public function trip(): BelongsTo { return $this->belongsTo(Trip::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }


    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Пассажир
     */
    public function passenger()
    {
        return $this->belongsTo(BookingPassenger::class, 'booking_passenger_id');
    }

    /**
     * Получить QR-код для билета
     */
    public function getQrCodeAttribute()
    {
        return "TICKET:{$this->ticket_code}:{$this->passenger->full_name}";
    }

    /**
     * Получить полную информацию о билете
     */
    public function getTicketInfoAttribute()
    {
        return [
            'ticket_code' => $this->ticket_code,
            'passenger' => $this->passenger->full_name,
            'trip' => $this->trip->trip_name,
            'seat' => $this->seat->full_number,
            'price' => $this->final_price,
            'status' => $this->status_display,
        ];
    }

    /**
     * Получить отображаемый статус
     */
    public function getStatusDisplayAttribute()
    {
        return $this->is_canceled ? 'Отменен' : 'Активен';
    }

    /**
     * Проверить, активен ли билет
     */
    public function getIsActiveAttribute()
    {
        return !$this->is_canceled;
    }

    /**
     * Проверить, можно ли отменить билет
     */
    public function getCanBeCanceledAttribute()
    {
        return !$this->is_canceled && $this->trip->start_timestamp->gt(now()->addHours(2));
    }

    /**
     * Отменить билет
     */
    public function cancel()
    {
        if ($this->can_be_canceled) {
            $this->update(['is_canceled' => true]);
            return true;
        }
        return false;
    }

    /**
     * Активировать билет
     */
    public function activate()
    {
        $this->update(['is_canceled' => false]);
    }

    /**
     * Генерация уникального кода билета
     */
    // public static function generateTicketCode()
    // {
    //     do {
    //         $code = 'TK' . strtoupper(Str::random(8));
    //     } while (self::where('ticket_code', $code)->exists());

    //     return $code;
    // }

    /**
     * Scope для активных билетов
     */
    public function scopeActive($query)
    {
        return $query->where('is_canceled', false);
    }

    /**
     * Scope для отмененных билетов
     */
    public function scopeCanceled($query)
    {
        return $query->where('is_canceled', true);
    }

    /**
     * Scope для билетов пользователя
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для билетов по поездке
     */
    public function scopeByTrip($query, $tripId)
    {
        return $query->where('trip_id', $tripId);
    }

    /**
     * Scope для билетов по коду
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('ticket_code', $code);
    }

    /**
     * Создать билет из бронирования
     */
    public static function createFromBooking($bookingPassenger, $seatId, $tripId, $userId, $finalPrice)
    {
        return static::create([
            'user_id' => $userId,
            'trip_id' => $tripId,
            'seat_id' => $seatId,
            'booking_passenger_id' => $bookingPassenger->id,
            'final_price' => $finalPrice,
            'ticket_code' => self::generateTicketCode(),
            'is_canceled' => false,
        ]);
    }
}

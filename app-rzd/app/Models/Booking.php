<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';

    const STATUS_BOOKED = 'BOOKED';
    const STATUS_PAID = 'PAID';
    const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'user_id',
        'status',
        'expires_at',
        'total_price',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Пользователь, создавший бронь
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Пассажиры в бронировании
     */
    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    /**
     * Услуги в бронировании
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'services_booking')
            ->using(ServicesBooking::class)
            ->withPivot('count', 'current_price')
            ->withTimestamps();
    }

    /**
     * Билеты этого бронирования
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Проверить, истекла ли бронь
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Проверить, активна ли бронь
     */
    public function getIsActiveAttribute()
    {
        return $this->status === self::STATUS_BOOKED && !$this->is_expired;
    }

    /**
     * Проверить, оплачена ли бронь
     */
    public function getIsPaidAttribute()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Проверить, отменена ли бронь
     */
    public function getIsCancelledAttribute()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Получить общую стоимость услуг
     */
    public function getServicesTotalAttribute()
    {
        return $this->services->sum(function ($service) {
            return $service->pivot->current_price * $service->pivot->count;
        });
    }

    /**
     * Получить итоговую стоимость (основная цена + услуги)
     */
    public function getFinalTotalAttribute()
    {
        return $this->total_price + $this->services_total;
    }

    /**
     * Получить количество пассажиров
     */
    public function getPassengersCountAttribute()
    {
        return $this->passengers()->count();
    }

    /**
     * Оплатить бронь
     */
    public function markAsPaid()
    {
        $this->update(['status' => self::STATUS_PAID]);
    }

    /**
     * Отменить бронь
     */
    public function cancel()
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Добавить услугу к бронированию
     */
    public function addService($serviceId, $count = 1)
    {
        $service = Service::findOrFail($serviceId);

        $this->services()->attach($serviceId, [
            'count' => $count,
            'current_price' => $service->base_price,
        ]);

        // Обновляем общую стоимость
        $this->updateTotalPrice();
    }

    /**
     * Обновить общую стоимость
     */
    public function updateTotalPrice()
    {
        $this->update([
            'total_price' => $this->services_total
        ]);
    }

    /**
     * Scope для активных бронирований
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_BOOKED)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope для оплаченных бронирований
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope для отмененных бронирований
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope для бронирований пользователя
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для просроченных бронирований
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_BOOKED)
            ->where('expires_at', '<', now());
    }

    /**
     * Получить все доступные статусы
     */
    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_BOOKED => 'Забронировано',
            self::STATUS_PAID => 'Оплачено',
            self::STATUS_CANCELLED => 'Отменено',
        ];
    }

    /**
     * Получить отображаемое название статуса
     */
    public function getStatusDisplayAttribute()
    {
        return self::getAvailableStatuses()[$this->status] ?? $this->status;
    }
}

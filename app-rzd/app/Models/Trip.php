<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;
    protected $table = 'trips';
    protected $fillable = ['train_id','route_id','start_timestamp','end_timestamp','is_denied','created_at','updated_at'];
    protected $casts = [
        'start_timestamp' => 'datetime',
        'end_timestamp' => 'datetime',
        'is_denied' => 'boolean',
    ];

    public function train(): BelongsTo { return $this->belongsTo(Train::class); }
    public function route(): BelongsTo { return $this->belongsTo(Route::class); }
    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Бронирования на этот рейс
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Получить продолжительность поездки в минутах
     */
    public function getDurationMinutesAttribute()
    {
        return $this->start_timestamp->diffInMinutes($this->end_timestamp);
    }

    /**
     * Получить отформатированную продолжительность
     */
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}ч {$minutes}м";
        }

        return "{$minutes}м";
    }

    /**
     * Проверить, началась ли поездка
     */
    public function getHasStartedAttribute()
    {
        return $this->start_timestamp->isPast();
    }

    /**
     * Проверить, завершилась ли поездка
     */
    public function getHasEndedAttribute()
    {
        return $this->end_timestamp->isPast();
    }

    /**
     * Проверить, активна ли поездка сейчас
     */
    public function getIsActiveAttribute()
    {
        return $this->has_started && !$this->has_ended;
    }

    /**
     * Получить название поездки
     */
    public function getTripNameAttribute()
    {
        return $this->route->route_name . ' (' . $this->start_timestamp->format('d.m.Y H:i') . ')';
    }

    /**
     * Получить количество свободных мест
     */
    public function getAvailableSeatsCountAttribute()
    {
        $bookedSeats = $this->bookings()
            ->where('status', 'confirmed')
            ->count();

        return $this->train->total_seats - $bookedSeats;
    }

    /**
     * Scope для активных поездок
     */
    public function scopeActive($query)
    {
        return $query->where('is_denied', false);
    }

    /**
     * Scope для предстоящих поездок
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_timestamp', '>', now())->active();
    }

    /**
     * Scope для текущих поездок
     */
    public function scopeCurrent($query)
    {
        return $query->where('start_timestamp', '<=', now())
            ->where('end_timestamp', '>=', now())
            ->active();
    }

    /**
     * Scope для завершенных поездок
     */
    public function scopeCompleted($query)
    {
        return $query->where('end_timestamp', '<', now())->active();
    }

    /**
     * Scope для поиска по дате
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('start_timestamp', $date);
    }

    /**
     * Scope для поиска по маршруту
     */
    public function scopeByRoute($query, $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    /**
     * Scope для поиска по поезду
     */
    public function scopeByTrain($query, $trainId)
    {
        return $query->where('train_id', $trainId);
    }

    /**
     * Отменить поездку
     */
    public function cancel()
    {
        $this->update(['is_denied' => true]);

        // Можно добавить логику отмены бронирований
        $this->bookings()->update(['status' => 'canceled']);
    }
}

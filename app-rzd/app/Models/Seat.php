<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;
    protected $table = 'seats';
    protected $fillable = [
        'carriage_id',
        'number',
        'price',
    ];
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Вагон, к которому принадлежит место
     */
    public function carriage()
    {
        return $this->belongsTo(Carriage::class);
    }

    /**
     * Бронирования этого места
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Билеты на это место
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Получить полный номер места
     */
    public function getFullNumberAttribute()
    {
        return $this->carriage->full_number . '-' . $this->number;
    }

    /**
     * Получить описание места
     */
    public function getDescriptionAttribute()
    {
        return "Место №{$this->number} в вагоне №{$this->carriage->number}";
    }

    /**
     * Проверить, свободно ли место на определенную поездку
     */
    public function isAvailableForTrip($tripId)
    {
        return !$this->bookings()
            ->where('trip_id', $tripId)
            ->where('status', 'confirmed')
            ->exists();
    }

    /**
     * Получить тип вагона через связь
     */
    public function getCarriageTypeAttribute()
    {
        return $this->carriage->type;
    }

    /**
     * Получить поезд через связи
     */
    public function getTrainAttribute()
    {
        return $this->carriage->train;
    }

    /**
     * Scope для поиска по номеру места
     */
    public function scopeByNumber($query, $number)
    {
        return $query->where('number', $number);
    }

    /**
     * Scope для мест в определенном вагоне
     */
    public function scopeByCarriage($query, $carriageId)
    {
        return $query->where('carriage_id', $carriageId);
    }

    /**
     * Scope для мест с ценой в диапазоне
     */
    public function scopePriceBetween($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Создать места для вагона
     */
    public static function createForCarriage($carriageId, $seatsCount, $basePrice)
    {
        $seats = [];

        for ($i = 1; $i <= $seatsCount; $i++) {
            // Можно добавить логику расчета цены в зависимости от номера места
            $price = $basePrice + ($i * 10); // Пример: цена увеличивается с номером места

            $seats[] = [
                'carriage_id' => $carriageId,
                'number' => $i,
                'price' => $price,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return static::insert($seats);
    }
}

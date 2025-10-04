<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = [
        'title',
        'description',
        'base_price',
    ];
    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    /**
     * Бронирования, в которых есть эта услуга
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'services_booking')
            ->using(ServicesBooking::class)
            ->withPivot('count', 'current_price')
            ->withTimestamps();
    }

    /**
     * Получить отформатированную цену
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->base_price, 2, '.', ' ') . ' ₽';
    }

    /**
     * Получить описание услуги с ценой
     */
    public function getDescriptionWithPriceAttribute()
    {
        return $this->title . ' - ' . $this->formatted_price;
    }

    /**
     * Проверить, доступна ли услуга
     */
    public function getIsAvailableAttribute()
    {
        return true; // Можно добавить логику доступности
    }

    /**
     * Scope для поиска по названию
     */
    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }

    /**
     * Scope для услуг с ценой в диапазоне
     */
    public function scopePriceBetween($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('base_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope для услуг с описанием
     */
    public function scopeWithDescription($query)
    {
        return $query->whereNotNull('description');
    }

    /**
     * Создать или найти услугу
     */
    public static function firstOrCreateByTitle($title, $basePrice, $description = null)
    {
        return static::firstOrCreate(
            ['title' => $title],
            [
                'base_price' => $basePrice,
                'description' => $description,
            ]
        );
    }

    /**
     * Получить правила валидации для услуги
     */
    public static function getValidationRules($serviceId = null)
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:services,title,' . $serviceId,
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'base_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }
}

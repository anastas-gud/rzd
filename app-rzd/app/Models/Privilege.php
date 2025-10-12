<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    use HasFactory;
    protected $table = 'privileges';
    protected $fillable = ['title','description','discount','created_at','updated_at'];
    protected $casts = ['discount' => 'decimal:2'];

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Пассажиры с этой льготой
     */
    public function bookingPassengers()
    {
        return $this->belongsToMany(BookingPassenger::class, 'booking_passenger_privileges')
            ->using(BookingPassengerPrivilege::class)
            ->withTimestamps();
    }

    /**
     * Получить отформатированную скидку
     */
    public function getFormattedDiscountAttribute()
    {
        return $this->discount . '%';
    }

    /**
     * Получить описание льготы со скидкой
     */
    public function getDescriptionWithDiscountAttribute()
    {
        return $this->title . ' - ' . $this->formatted_discount;
    }

    /**
     * Проверить, является ли льгота активной
     */
    public function getIsActiveAttribute()
    {
        return $this->discount > 0;
    }

    /**
     * Scope для поиска по названию
     */
    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }

    /**
     * Scope для льгот со скидкой больше указанной
     */
    public function scopeWithDiscountMoreThan($query, $discount)
    {
        return $query->where('discount', '>', $discount);
    }

    /**
     * Scope для льгот со скидкой меньше указанной
     */
    public function scopeWithDiscountLessThan($query, $discount)
    {
        return $query->where('discount', '<', $discount);
    }

    /**
     * Scope для активных льгот
     */
    public function scopeActive($query)
    {
        return $query->where('discount', '>', 0);
    }

    /**
     * Создать или найти льготу
     */
    public static function firstOrCreateByTitle($title, $discount, $description = null)
    {
        return static::firstOrCreate(
            ['title' => $title],
            [
                'discount' => $discount,
                'description' => $description,
            ]
        );
    }

    /**
     * Получить правила валидации для льготы
     */
    public static function getValidationRules($privilegeId = null)
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:privileges,title,' . $privilegeId,
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'discount' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }

    /**
     * Применить льготу к цене
     */
    public function applyToPrice($price)
    {
        return $price * (1 - $this->discount / 100);
    }

    /**
     * Получить сумму скидки для указанной цены
     */
    public function getDiscountAmount($price)
    {
        return $price * ($this->discount / 100);
    }
}

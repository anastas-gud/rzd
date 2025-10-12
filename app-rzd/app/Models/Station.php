<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
    use HasFactory;
    protected $table = 'stations';
    protected $fillable = ['title','city','address','photo_path','phone','created_at','updated_at'];

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Маршруты, где станция является начальной
     */
    public function startRoutes()
    {
        return $this->hasMany(Route::class, 'start_station_id');
    }

    /**
     * Маршруты, где станция является конечной
     */
    public function endRoutes()
    {
        return $this->hasMany(Route::class, 'end_station_id');
    }

    /**
     * Все маршруты, связанные со станцией
     */
    public function allRoutes()
    {
        return $this->startRoutes->merge($this->endRoutes);
    }

    /**
     * Поездки, начинающиеся на этой станции
     */
    public function startingTrips()
    {
        return $this->hasManyThrough(Trip::class, Route::class, 'start_station_id', 'route_id');
    }

    /**
     * Поездки, заканчивающиеся на этой станции
     */
    public function endingTrips()
    {
        return $this->hasManyThrough(Trip::class, Route::class, 'end_station_id', 'route_id');
    }

    /**
     * Проверить, есть ли фото станции
     */
    public function getHasPhotoAttribute()
    {
        return !empty($this->photo_path);
    }

    /**
     * Получить отформатированный телефон
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (strlen($phone) === 11 && $phone[0] === '7') {
            return '+7 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9, 2);
        }

        return $this->phone;
    }

    /**
     * Получить URL фото станции
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }

        return asset('storage/' . $this->photo_path);
    }

    /**
     * Scope для поиска по названию
     */
    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }

    /**
     * Scope для поиска по городу
     */
    public function scopeWhereCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope для станций в определенном городе
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope для поиска по телефону
     */
    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }


    /**
     * Найти станции по городу
     */
    public static function whereCity($city)
    {
        return static::where('city', $city);
    }

    /**
     * Создать или найти станцию
     */
    public static function firstOrCreateByTitle($title, $city, $address = null, $phone = null)
    {
        return static::firstOrCreate(
            [
                'title' => $title,
                'city' => $city,
            ],
            [
                'address' => $address,
                'phone' => $phone,
            ]
        );
    }

    /**
     * Получить список всех городов, где есть станции
     */
    public static function getCities()
    {
        return static::select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }
}

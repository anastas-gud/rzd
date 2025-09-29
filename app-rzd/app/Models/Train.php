<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    use HasFactory;
    protected $table = 'trains';
    protected $fillable = [
        'title',
        'carriage_count',
    ];

    /**
     * Вагоны поезда
     */
    public function carriages()
    {
        return $this->hasMany(Carriage::class);
    }

    /**
     * Поездки поезда
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Активные поездки
     */
    public function activeTrips()
    {
        return $this->trips()->where('is_denied', false);
    }

    /**
     * Предстоящие поездки
     */
    public function upcomingTrips()
    {
        return $this->trips()
            ->where('start_timestamp', '>', now())
            ->where('is_denied', false);
    }

    /**
     * Получить доступные места для поездки
     */
    public function getAvailableSeatsForTrip($tripId)
    {
        return $this->carriages->sum(function ($carriage) use ($tripId) {
            return $carriage->getAvailableSeatsForTrip($tripId);
        });
    }

    /**
     * Scope для поиска по названию
     */
    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }
}

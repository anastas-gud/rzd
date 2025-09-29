<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;
    protected $table = 'routes';
    protected $fillable = [
        'start_station_id',
        'end_station_id',
        'number',
    ];

    /**
     * Станция отправления
     */
    public function startStation()
    {
        return $this->belongsTo(Station::class, 'start_station_id');
    }

    /**
     * Станция назначения
     */
    public function endStation()
    {
        return $this->belongsTo(Station::class, 'end_station_id');
    }

    /**
     * Поездки по этому маршруту
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Активные поездки по маршруту
     */
    public function activeTrips()
    {
        return $this->trips()->where('is_denied', false);
    }

    /**
     * Получить название маршрута
     */
    public function getRouteNameAttribute()
    {
        return $this->startStation->title . ' - ' . $this->endStation->title;
    }

    /**
     * Проверить, активен ли маршрут (есть активные поездки)
     */
    public function getIsActiveAttribute()
    {
        return $this->activeTrips()->exists();
    }

    /**
     * Scope для поиска по номеру маршрута
     */
    public function scopeByNumber($query, $number)
    {
        return $query->where('number', 'like', "%{$number}%");
    }

    /**
     * Scope для маршрутов из определенной станции
     */
    public function scopeFromStation($query, $stationId)
    {
        return $query->where('start_station_id', $stationId);
    }

    /**
     * Scope для маршрутов до определенной станции
     */
    public function scopeToStation($query, $stationId)
    {
        return $query->where('end_station_id', $stationId);
    }

    /**
     * Scope для маршрутов между станциями
     */
    public function scopeBetweenStations($query, $startStationId, $endStationId)
    {
        return $query->where('start_station_id', $startStationId)
            ->where('end_station_id', $endStationId);
    }
}

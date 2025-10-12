<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carriage extends Model
{
    use HasFactory;
    protected $table = 'carriages';
    protected $fillable = ['train_id','carriage_type_id','number','created_at','updated_at'];

    public function train(): BelongsTo { return $this->belongsTo(Train::class); }
    public function type(): BelongsTo { return $this->belongsTo(CarriageType::class, 'carriage_type_id'); }
    public function seats(): HasMany { return $this->hasMany(Seat::class); }

    // РАЗОБРАТЬСЯ
    /**
     * Получить полный номер вагона
     */
    public function getFullNumberAttribute()
    {
        return $this->train->title . '-' . $this->number;
    }

    /**
     * Получить доступные места для поездки
     */
    public function getAvailableSeatsForTrip($tripId)
    {
        $bookedSeatIds = Booking::where('trip_id', $tripId)
            ->where('status', 'confirmed')
            ->pluck('seat_id');

        return $this->seats()
            ->whereNotIn('id', $bookedSeatIds)
            ->count();
    }

    /**
     * Проверить, есть ли свободные места для поездки
     */
    public function hasAvailableSeatsForTrip($tripId)
    {
        return $this->getAvailableSeatsForTrip($tripId) > 0;
    }

    /**
     * Scope для вагонов определенного поезда
     */
    public function scopeByTrain($query, $trainId)
    {
        return $query->where('train_id', $trainId);
    }

    /**
     * Scope для вагонов определенного типа
     */
    public function scopeByType($query, $typeId)
    {
        return $query->where('carriage_type_id', $typeId);
    }

    /**
     * Scope для вагона по номеру
     */
    public function scopeByNumber($query, $number)
    {
        return $query->where('number', $number);
    }
}

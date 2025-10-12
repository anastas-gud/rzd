<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarriageType extends Model
{
    use HasFactory;
    protected $table = 'carriage_types';
    protected $fillable = ['title','seats_number','created_at','updated_at'];

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Вагоны этого типа
     */
    public function carriages()
    {
        return $this->hasMany(Carriage::class);
    }

    /**
     * Активные вагоны этого типа
     */
    public function activeCarriages()
    {
        return $this->carriages()->where('is_active', true);
    }

    /**
     * Билеты для этого типа вагона
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Проверить, есть ли вагоны этого типа
     */
    public function getHasCarriagesAttribute()
    {
        return $this->carriages()->exists();
    }

    /**
     * Scope для поиска по названию
     */
    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carriage extends Model
{
    protected $table = 'carriages';
    protected $fillable = ['train_id','carriage_type_id','number','created_at','updated_at'];

    public function train(): BelongsTo { return $this->belongsTo(Train::class); }
    public function type(): BelongsTo { return $this->belongsTo(CarriageType::class, 'carriage_type_id'); }
    public function seats(): HasMany { return $this->hasMany(Seat::class); }
}

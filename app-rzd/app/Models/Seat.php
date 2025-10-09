<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seat extends Model
{
    protected $table = 'seats';
    protected $fillable = ['carriage_id','number','price','created_at','updated_at'];
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function carriage(): BelongsTo { return $this->belongsTo(Carriage::class); }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $table = 'trips';
    protected $fillable = ['train_id','route_id','start_timestamp','end_timestamp','is_denied','created_at','updated_at'];
    protected $casts = [
        'start_timestamp' => 'datetime',
        'end_timestamp' => 'datetime',
        'is_denied' => 'boolean',
    ];

    public function train(): BelongsTo { return $this->belongsTo(Train::class); }
    public function route(): BelongsTo { return $this->belongsTo(Route::class); }
    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }
}

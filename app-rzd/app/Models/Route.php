<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Route extends Model
{
    protected $table = 'routes';
    protected $fillable = ['start_station_id','end_station_id','number','created_at','updated_at'];

    public function startStation(): BelongsTo { return $this->belongsTo(Station::class, 'start_station_id'); }
    public function endStation(): BelongsTo { return $this->belongsTo(Station::class, 'end_station_id'); }
}

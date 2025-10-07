<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Train extends Model
{
    protected $table = 'trains';
    protected $fillable = ['title','carriage_count','created_at','updated_at'];

    public function carriages(): HasMany { return $this->hasMany(Carriage::class); }
}

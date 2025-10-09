<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    protected $table = 'privileges';
    protected $fillable = ['title','description','discount','created_at','updated_at'];
    protected $casts = ['discount' => 'decimal:2'];
}

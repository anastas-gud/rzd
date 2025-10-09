<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    protected $fillable = ['title','description','base_price','created_at','updated_at'];
    protected $casts = ['base_price' => 'decimal:2'];
}

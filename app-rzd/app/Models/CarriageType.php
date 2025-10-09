<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarriageType extends Model
{
    protected $table = 'carriage_types';
    protected $fillable = ['title','seats_number','created_at','updated_at'];
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    protected $table = 'names';
    protected $fillable = ['name','surname','patronymic','created_at','updated_at'];
}

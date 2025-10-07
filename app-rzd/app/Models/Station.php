<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'stations';
    protected $fillable = ['title','city','address','photo_path','phone','created_at','updated_at'];
}

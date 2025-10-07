<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['login','password','role_id','contact_id','name_id','created_at','updated_at'];
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPassengerPrivilege extends Model
{
    use HasFactory;
    protected $table = 'booking_passenger_privileges';
    protected $fillable = ['booking_passenger_id','privilege_id','created_at','updated_at'];
    public $timestamps = true;
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPassenger extends Model
{
    protected $table = 'booking_passengers';
    protected $fillable = ['booking_id','document_id','name_id','contact_id','created_at','updated_at'];

    public function booking() : BelongsTo { return $this->belongsTo(Booking::class); }
    public function privileges() : HasMany { return $this->hasMany(BookingPassengerPrivilege::class, 'booking_passenger_id'); }
    public function name() : BelongsTo { return $this->belongsTo(Name::class, 'name_id'); }
    public function document() : BelongsTo { return $this->belongsTo(Document::class, 'document_id'); }
    public function contact() : BelongsTo { return $this->belongsTo(Contact::class, 'contact_id'); }
    public function tickets() : HasMany { return $this->hasMany(Ticket::class, 'booking_passenger_id'); }
}

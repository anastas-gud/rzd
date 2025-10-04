<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPassenger extends Model
{
    use HasFactory;
    protected $table = 'booking_passengers';
    protected $fillable = [
        'booking_id',
        'document_id',
        'name_id',
        'contact_id',
    ];

    /**
     * Бронирование
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Паспортные данные
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * ФИО пассажира
     */
    public function name()
    {
        return $this->belongsTo(Name::class);
    }

    /**
     * Контакты пассажира
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Билеты этого пассажира
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Льготы пассажира
     */
    public function privileges()
    {
        return $this->belongsToMany(Privilege::class, 'booking_passenger_privileges')
            ->using(BookingPassengersPrivileges::class)
            ->withTimestamps();
    }

    /**
     * Получить полное имя пассажира
     */
    public function getFullNameAttribute()
    {
        return $this->name->full_name ?? null;
    }

    /**
     * Получить телефон пассажира
     */
    public function getPhoneAttribute()
    {
        return $this->contact->phone ?? null;
    }

    /**
     * Получить email пассажира
     */
    public function getEmailAttribute()
    {
        return $this->contact->email ?? null;
    }

    /**
     * Получить данные паспорта
     */
    public function getDocumentNumberAttribute()
    {
        return $this->document()->full_number ?? null;
    }

    /**
     * Проверить, является ли пассажир ребенком
     */
    public function getIsChildAttribute()
    {
        return $this->age < 18;
    }

    /**
     * Проверить, является ли пассажир взрослым
     */
    public function getIsAdultAttribute()
    {
        return $this->age >= 18;
    }

    /**
     * Проверить, является ли пассажир пенсионером
     */
    public function getIsSeniorAttribute()
    {
        return $this->age >= 60;
    }

    /**
     * Получить одобренные льготы
     */
    public function approvedPrivileges()
    {
        return $this->privileges()->wherePivot('status', 'approved');
    }

    /**
     * Получить общую скидку по льготам
     */
    public function getTotalDiscountAttribute()
    {
        return $this->approvedPrivileges()->sum('pivot.applied_discount');
    }

    /**
     * Scope для поиска по бронированию
     */
    public function scopeByBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    /**
     * Создать пассажира для бронирования
     */
    public static function createForBooking($bookingId, $nameData, $contactData, $documentData)
    {
        // Создаем или находим имя
        $name = Name::firstOrCreateByFullName(
            $nameData['surname'],
            $nameData['name'],
            $nameData['patronymic'] ?? null
        );

        // Создаем или находим контакт
        $contact = Contact::firstOrCreateByContact(
            $contactData['phone'] ?? null,
            $contactData['email'] ?? null
        );

        // Создаем или находим паспорт
        $document = Document::firstOrCreateByNumber(
            $documentData['serial'],
            $documentData['number'],
            $documentData['date_of_birth'],
            $documentData['type_of_document'],
        );

        // Создаем пассажира
        return static::create([
            'booking_id' => $bookingId,
            'name_id' => $name->id,
            'contact_id' => $contact->id,
            'document_id' => $document->id,
        ]);
    }
}

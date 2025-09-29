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
        'date_of_birth',
        'passport_id',
        'name_id',
        'contact_id',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
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
    public function passport()
    {
        return $this->belongsTo(Passport::class);
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
     * Получить возраст пассажира
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth->age;
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
    public function getPassportNumberAttribute()
    {
        return $this->passport->full_number ?? null;
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
     * Scope для поиска по дате рождения
     */
    public function scopeByBirthDate($query, $date)
    {
        return $query->where('date_of_birth', $date);
    }

    /**
     * Scope для детей
     */
    public function scopeChildren($query)
    {
        return $query->where('date_of_birth', '>', now()->subYears(18));
    }

    /**
     * Scope для взрослых
     */
    public function scopeAdults($query)
    {
        return $query->where('date_of_birth', '<=', now()->subYears(18));
    }

    /**
     * Создать пассажира для бронирования
     */
    public static function createForBooking($bookingId, $nameData, $contactData, $passportData, $dateOfBirth)
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
        $passport = Passport::firstOrCreateByNumber(
            $passportData['serial'],
            $passportData['number']
        );

        // Создаем пассажира
        return static::create([
            'booking_id' => $bookingId,
            'date_of_birth' => $dateOfBirth,
            'name_id' => $name->id,
            'contact_id' => $contact->id,
            'passport_id' => $passport->id,
        ]);
    }
}

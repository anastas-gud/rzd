<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';
    protected $fillable = ['phone','email','created_at','updated_at'];

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Получить пользователей с этими контактами
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Получить пассажиров с этими контактами
     */
    public function bookingPassengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    /**
     * Проверить, есть ли телефон
     */
    public function getHasPhoneAttribute()
    {
        return !empty($this->phone);
    }

    /**
     * Проверить, есть ли email
     */
    public function getHasEmailAttribute()
    {
        return !empty($this->email);
    }

    /**
     * Получить отформатированный телефон
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return null;
        }

        // Форматирование российского номера телефона
        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (strlen($phone) === 11 && $phone[0] === '7') {
            return '+7 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9, 2);
        }

        return $this->phone;
    }

    /**
     * Scope для поиска по телефону
     */
    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }

    /**
     * Scope для поиска по email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', 'like', "%{$email}%");
    }

    /**
     * Валидация телефона
     */
    public static function validatePhone($phone)
    {
        if (!$phone) {
            return true;
        }

        // Простая валидация российского номера
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        return strlen($cleaned) === 11 && in_array($cleaned[0], ['7', '8']);
    }

    /**
     * Валидация email
     */
    public static function validateEmail($email)
    {
        if (!$email) {
            return true;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    use HasFactory;
    protected $table = 'names';
    protected $fillable = [
        'name',
        'surname',
        'patronymic',
    ];

    /**
     * Получить пользователей с этим именем
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Получить пассажиров с этим именем
     */
    public function bookingPassengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    /**
     * Получить полное ФИО
     */
    public function getFullNameAttribute()
    {
        return trim($this->surname . ' ' . $this->name . ' ' . ($this->patronymic ?? ''));
    }

    /**
     * Получить фамилию с инициалами
     */
    public function getInitialsAttribute()
    {
        $initials = $this->surname . ' ' . mb_substr($this->name, 0, 1) . '.';

        if ($this->patronymic) {
            $initials .= mb_substr($this->patronymic, 0, 1) . '.';
        }

        return $initials;
    }

    /**
     * Получить только имя и отчество
     */
    public function getNameWithPatronymicAttribute()
    {
        return trim($this->name . ' ' . ($this->patronymic ?? ''));
    }

    /**
     * Scope для поиска по фамилии
     */
    public function scopeBySurname($query, $surname)
    {
        return $query->where('surname', 'like', "%{$surname}%");
    }

    /**
     * Scope для поиска по имени
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope для поиска по ФИО
     */
    public function scopeByFullName($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('surname', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('patronymic', 'like', "%{$search}%");
        });
    }

    /**
     * Scope для поиска по точному ФИО
     */
    public function scopeExactFullName($query, $surname, $name, $patronymic = null)
    {
        return $query->where('surname', $surname)
            ->where('name', $name)
            ->when($patronymic, function ($q) use ($patronymic) {
                $q->where('patronymic', $patronymic);
            });
    }

    /**
     * Проверить, есть ли отчество
     */
    public function getHasPatronymicAttribute()
    {
        return !empty($this->patronymic);
    }
}

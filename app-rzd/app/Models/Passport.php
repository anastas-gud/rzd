<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passport extends Model
{
    use HasFactory;
    protected $table = 'passports';
    protected $fillable = [
        'serial',
        'number',
    ];
    protected $casts = [
        // может быть кешировать поля
    ];

    /**
     * Пассажиры с этим паспортом
     */
    public function bookingPassengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    /**
     * Получить полный номер паспорта
     */
    public function getFullNumberAttribute()
    {
        return $this->serial . ' ' . $this->number;
    }

    /**
     * Получить серию и номер раздельно
     */
    public function getSeriesAttribute()
    {
        return $this->serial;
    }

    /**
     * Scope для поиска по серии
     */
    public function scopeBySerial($query, $serial)
    {
        return $query->where('serial', 'like', "%{$serial}%");
    }

    /**
     * Scope для поиска по номеру
     */
    public function scopeByNumber($query, $number)
    {
        return $query->where('number', 'like', "%{$number}%");
    }

    /**
     * Scope для поиска по полному номеру
     */
    public function scopeByFullNumber($query, $fullNumber)
    {
        $parts = explode(' ', $fullNumber);
        if (count($parts) === 2) {
            return $query->where('serial', $parts[0])
                ->where('number', $parts[1]);
        }

        return $query->where('serial', 'like', "%{$fullNumber}%")
            ->orWhere('number', 'like', "%{$fullNumber}%");
    }

    /**
     * Создать или найти паспорт
     */
    public static function firstOrCreateByNumber($serial, $number)
    {
        return static::firstOrCreate(
            [
                'serial' => $serial,
                'number' => $number,
            ]
        );
    }

    /**
     * Валидация серии и номера паспорта
     */
    public static function validatePassport($serial, $number)
    {
        // Проверка формата серии (4 цифры)
        if (!preg_match('/^\d{4}$/', $serial)) {
            return false;
        }

        // Проверка формата номера (6 цифр)
        if (!preg_match('/^\d{6}$/', $number)) {
            return false;
        }

        return true;
    }

    /**
     * Получить правила валидации для паспорта
     */
    public static function getValidationRules($passportId = null)
    {
        return [
            'serial' => [
                'required',
                'string',
                'size:4',
                'regex:/^\d{4}$/',
            ],
            'number' => [
                'required',
                'string',
                'size:6',
                'regex:/^\d{6}$/',
                function ($attribute, $value, $fail) use ($passportId) {
                    $exists = static::where('serial', request('serial'))
                        ->where('number', $value)
                        ->when($passportId, function ($q) use ($passportId) {
                            $q->where('id', '!=', $passportId);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('Паспорт с таким серией и номером уже существует.');
                    }
                },
            ],
        ];
    }
}

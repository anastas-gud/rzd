<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $table = 'documents';
    protected $fillable = ['date_of_birth','serial','number','type_of_document','created_at','updated_at'];
    protected $casts = ['date_of_birth' => 'date'];

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    const TYPE_PASSPORT = 'PASSPORT';
    const TYPE_BIRTH_CERTIFICATE = 'BIRTH_CERTIFICATE';

    /**
     * Пассажиры с этим документом
     */
    public function bookingPassengers()
    {
        return $this->hasMany(BookingPassenger::class, 'document_id');
    }

    /**
     * Получить полный номер документа
     */
    public function getFullNumberAttribute()
    {
        return $this->serial . ' ' . $this->number;
    }

    /**
     * Получить возраст владельца документа
     */
    // public function getAgeAttribute()
    // {
    //     return $this->date_of_birth->age;
    // }

    /**
     * Проверить, является ли владелец ребенком
     */
    public function getIsChildAttribute()
    {
        return $this->age < 18;
    }

    /**
     * Проверить, является ли владелец взрослым
     */
    public function getIsAdultAttribute()
    {
        return $this->age >= 18;
    }

    /**
     * Проверить, является ли владелец пенсионером
     */
    public function getIsSeniorAttribute()
    {
        return $this->age >= 60;
    }

    /**
     * Получить дату рождения в формате d.m.Y
     */
    // public function getFormattedDateOfBirthAttribute()
    // {
    //     return $this->date_of_birth->format('d.m.Y');
    // }

    /**
     * Проверить, является ли документ паспортом
     */
    public function getIsPassportAttribute()
    {
        return $this->type_of_document === self::TYPE_PASSPORT;
    }

    /**
     * Проверить, является ли документ свидетельством о рождении
     */
    public function getIsBirthCertificateAttribute()
    {
        return $this->type_of_document === self::TYPE_BIRTH_CERTIFICATE;
    }

    /**
     * Получить название типа документа
     */
    public function getTypeDisplayAttribute()
    {
        return match($this->type_of_document) {
            self::TYPE_PASSPORT => 'Паспорт',
            self::TYPE_BIRTH_CERTIFICATE => 'Свидетельство о рождении',
            default => $this->type_of_document,
        };
    }

    /**
     * Получить описание документа
     */
    public function getDescriptionAttribute()
    {
        return $this->type_display . ' ' . $this->full_number;
    }

    /**
     * Проверить, действителен ли документ
     */
    public function getIsValidAttribute()
    {
        if ($this->is_passport) {
            return $this->age >= 14 && $this->age <= 100;
        }

        if ($this->is_birth_certificate) {
            return $this->age < 18; // Свидетельство о рождении действительно до 18 лет
        }

        return true;
    }

    /**
     * Scope для поиска по серии
     */
    public function scopeWhereSerial($query, $serial)
    {
        return $query->where('serial', 'like', "%{$serial}%");
    }

    /**
     * Scope для поиска по номеру
     */
    public function scopeWhereNumber($query, $number)
    {
        return $query->where('number', 'like', "%{$number}%");
    }

    /**
     * Scope для поиска по полному номеру
     */
    public function scopeWhereFullNumber($query, $fullNumber)
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
     * Scope для поиска по дате рождения
     */
    public function scopeWhereDateOfBirth($query, $date)
    {
        return $query->where('date_of_birth', $date);
    }

    /**
     * Scope для поиска по типу документа
     */
    public function scopeWhereType($query, $type)
    {
        return $query->where('type_of_document', $type);
    }

    /**
     * Scope для паспортов
     */
    public function scopePassports($query)
    {
        return $query->where('type_of_document', self::TYPE_PASSPORT);
    }

    /**
     * Scope для свидетельств о рождении
     */
    public function scopeBirthCertificates($query)
    {
        return $query->where('type_of_document', self::TYPE_BIRTH_CERTIFICATE);
    }

    /**
     * Scope для документов детей
     */
    public function scopeChildren($query)
    {
        $minDate = now()->subYears(18);
        return $query->where('date_of_birth', '>', $minDate);
    }

    /**
     * Scope для документов взрослых
     */
    public function scopeAdults($query)
    {
        $maxDate = now()->subYears(18);
        return $query->where('date_of_birth', '<=', $maxDate);
    }

    /**
     * Scope для документов пенсионеров
     */
    public function scopeSeniors($query)
    {
        $maxDate = now()->subYears(60);
        return $query->where('date_of_birth', '<=', $maxDate);
    }

    /**
     * Найти документ по серии и номеру
     */
    public static function whereSerialAndNumber($serial, $number)
    {
        return static::where('serial', $serial)
            ->where('number', $number);
    }

    /**
     * Найти документ по полному номеру
     */
    public static function whereFullNumber($fullNumber)
    {
        $parts = explode(' ', $fullNumber);
        if (count($parts) === 2) {
            return static::whereSerialAndNumber($parts[0], $parts[1]);
        }

        return static::where('serial', 'like', "%{$fullNumber}%")
            ->orWhere('number', 'like', "%{$fullNumber}%");
    }

    /**
     * Создать или найти документ
     */
    public static function firstOrCreateByNumber($serial, $number, $dateOfBirth = null, $type = self::TYPE_PASSPORT)
    {
        return static::firstOrCreate(
            [
                'serial' => $serial,
                'number' => $number,
            ],
            [
                'date_of_birth' => $dateOfBirth ?? now()->subYears(25),
                'type_of_document' => $type,
            ]
        );
    }

    /**
     * Валидация документа
     */
    public static function validateDocument($serial, $number, $type)
    {
        if ($type === self::TYPE_PASSPORT) {
            // Проверка формата паспорта (4 цифры серия, 6 цифр номер)
            if (!preg_match('/^\d{4}$/', $serial)) {
                return false;
            }
            if (!preg_match('/^\d{6}$/', $number)) {
                return false;
            }
        } elseif ($type === self::TYPE_BIRTH_CERTIFICATE) {
            // Проверка формата свидетельства о рождении (римские цифры и буквы)
            if (!preg_match('/^[IVXLCDM]+-[А-Я]{2}$/u', $serial)) {
                return false;
            }
            if (!preg_match('/^\d{6,7}$/', $number)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Получить правила валидации для документа
     */
    public static function getValidationRules($documentId = null)
    {
        return [
            'serial' => [
                'required',
                'string',
                'max:20',
            ],
            'number' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($documentId) {
                    $exists = static::where('serial', request('serial'))
                        ->where('number', $value)
                        ->when($documentId, function ($q) use ($documentId) {
                            $q->where('id', '!=', $documentId);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('Документ с таким серией и номером уже существует.');
                    }
                },
            ],
            'date_of_birth' => [
                'required',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'type_of_document' => [
                'required',
                'in:' . implode(',', self::getAvailableTypes()),
            ],
        ];
    }

    /**
     * Получить все доступные типы документов
     */
    public static function getAvailableTypes()
    {
        return [
            self::TYPE_PASSPORT,
            self::TYPE_BIRTH_CERTIFICATE,
        ];
    }

    /**
     * Получить отображаемые названия типов документов
     */
    public static function getTypeDisplayNames()
    {
        return [
            self::TYPE_PASSPORT => 'Паспорт',
            self::TYPE_BIRTH_CERTIFICATE => 'Свидетельство о рождении',
        ];
    }

    /**
     * Генерация случайных данных для документа
     */
    public static function generateRandomDocumentData($type = self::TYPE_PASSPORT)
    {
        if ($type === self::TYPE_PASSPORT) {
            return [
                'serial' => sprintf('%04d', rand(1000, 9999)),
                'number' => sprintf('%06d', rand(100000, 999999)),
                'date_of_birth' => now()->subYears(rand(18, 70))->subDays(rand(0, 365)),
                'type_of_document' => self::TYPE_PASSPORT,
            ];
        } else {
            $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
            $regions = ['МО', 'СП', 'НН', 'ЕК', 'КЗ', 'СМ', 'РО', 'ЧЛ', 'ОМ', 'НС'];

            return [
                'serial' => $romanNumerals[array_rand($romanNumerals)] . '-' . $regions[array_rand($regions)],
                'number' => sprintf('%06d', rand(100000, 999999)),
                'date_of_birth' => now()->subYears(rand(1, 17))->subDays(rand(0, 365)),
                'type_of_document' => self::TYPE_BIRTH_CERTIFICATE,
            ];
        }
    }

    /**
     * Получить информацию о документе для отображения
     */
    public function getDocumentInfoAttribute()
    {
        return [
            'full_number' => $this->full_number,
            'type' => $this->type_display,
            'date_of_birth' => $this->formatted_date_of_birth,
            'age' => $this->age,
            'is_adult' => $this->is_adult,
            'is_child' => $this->is_child,
            'is_senior' => $this->is_senior,
            'is_valid' => $this->is_valid,
            'description' => $this->description,
        ];
    }

    /**
     * Создать паспорт
     */
    public static function createPassport($serial, $number, $dateOfBirth)
    {
        return static::create([
            'serial' => $serial,
            'number' => $number,
            'date_of_birth' => $dateOfBirth,
            'type_of_document' => self::TYPE_PASSPORT,
        ]);
    }

    /**
     * Создать свидетельство о рождении
     */
    public static function createBirthCertificate($serial, $number, $dateOfBirth)
    {
        return static::create([
            'serial' => $serial,
            'number' => $number,
            'date_of_birth' => $dateOfBirth,
            'type_of_document' => self::TYPE_BIRTH_CERTIFICATE,
        ]);
    }
}

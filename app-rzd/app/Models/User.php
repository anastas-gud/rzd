<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Authenticatable implements AuthenticatableContract
{
    use HasFactory, HasApiTokens, Notifiable;
    protected $table = 'users';
    protected $fillable = ['login','password','role_id','contact_id','name_id','created_at','updated_at'];

    protected $hidden = [
        'password',
    ];

    public function username(): string
    {
        return 'login';
    }

    public function isAdmin(): bool
    {
        return $this->role->title === 'ADMIN';
    }

    public function isManager(): bool
    {
        return $this->role->title === 'MANAGER';
    }

    public function isUser(): bool
    {
        return $this->role->title === 'USER';
    }

    public function hasRole(string $role): bool
    {
        return $this->role->title === strtoupper($role);
    }

    // РАЗОБРАТЬСЯ С ЭТОГО МОМЕНТА
    /**
     * Получить роль пользователя
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Получить контакты пользователя
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Получить ФИО пользователя
     */
    public function name()
    {
        return $this->belongsTo(Name::class);
    }

    /**
     * Получить бронирования пользователя
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Получить билеты пользователя
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    /**
     * Создать пользователя с полными данными
     */
    public static function createWithDetails($login, $password, $nameData, $contactData, $roleTitle = Role::USER)
    {
        // Находим или создаем имя
        $name = Name::firstOrCreate(
            [
                'surname' => $nameData['surname'],
                'name' => $nameData['name'],
                'patronymic' => $nameData['patronymic'] ?? null,
            ]
        );

        // Находим или создаем контакт
        $contact = Contact::firstOrCreate(
            [
                'phone' => $contactData['phone'] ?? null,
                'email' => $contactData['email'] ?? null,
            ]
        );

        // Находим роль
        $role = Role::whereTitle($roleTitle)->first();

        if (!$role) {
            throw new \Exception("Роль {$roleTitle} не найдена");
        }

        // Создаем пользователя
        return static::create([
            'login' => $login,
            'password' => Hash::make($password),
            'name_id' => $name->id,
            'contact_id' => $contact->id,
            'role_id' => $role->id,
        ]);
    }

    /**
     * Создать пользователя (переопределение стандартного create)
     */
    public static function create(array $attributes = [])
    {
        // Хешируем пароль, если он передан
        if (isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        return static::query()->create($attributes);
    }

    /**
     * Получить полное имя пользователя
     */
    public function getFullNameAttribute()
    {
        return $this->name->full_name ?? null;
    }

    /**
     * Получить инициалы пользователя
     */
    public function getInitialsAttribute()
    {
        return $this->name->initials ?? null;
    }

    /**
     * Получить телефон пользователя
     */
    public function getPhoneAttribute()
    {
        return $this->contact->phone ?? null;
    }

    /**
     * Получить email пользователя
     */
    public function getEmailAttribute()
    {
        return $this->contact->email ?? null;
    }

    /**
     * Получить отформатированный телефон пользователя
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->contact->formatted_phone ?? null;
    }

    /**
     * Проверить, является ли пользователь администратором
     */
    public function getIsAdminAttribute()
    {
        return $this->hasRole(Role::ADMIN);
    }

    /**
     * Проверить, является ли пользователь менеджером
     */
    public function getIsManagerAttribute()
    {
        return $this->hasRole(Role::MANAGER);
    }

    /**
     * Проверить, является ли пользователь обычным пользователем
     */
    public function getIsUserAttribute()
    {
        return $this->hasRole(Role::USER);
    }

    /**
     * Назначить роль пользователю
     */
    public function assignRole($roleTitle)
    {
        $role = Role::where('title', $roleTitle)->first();
        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }
        return $this;
    }

    /**
     * Scope для поиска по логину
     */
    public function scopeByLogin($query, $login)
    {
        return $query->where('login', 'like', "%{$login}%");
    }

    /**
     * Scope для администраторов
     */
    public function scopeAdmins($query)
    {
        return $query->byRole(Role::ADMIN);
    }

    /**
     * Scope для менеджеров
     */
    public function scopeManagers($query)
    {
        return $query->byRole(Role::MANAGER);
    }

    /**
     * Scope для обычных пользователей
     */
    public function scopeUsers($query)
    {
        return $query->byRole(Role::USER);
    }

    /**
     * Scope для поиска по имени или фамилии
     */
    public function scopeByName($query, $search)
    {
        return $query->whereHas('name', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('surname', 'like', "%{$search}%")
                ->orWhere('patronymic', 'like', "%{$search}%");
        });
    }

    /**
     * Scope для поиска по контактам
     */
    public function scopeByContact($query, $search)
    {
        return $query->whereHas('contact', function ($q) use ($search) {
            $q->where('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }
}

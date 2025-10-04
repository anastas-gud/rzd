<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';

    /**
     * Доступные роли
     */
    const ADMIN = 'ADMIN';
    const USER = 'USER';
    const MANAGER = 'MANAGER';
    protected $fillable = [
        'title',
    ];

    /**
     * Получить пользователей с этой ролью
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope для поиска по названию роли
     */
    public function scopeByTitle($query, $title)
    {
        return $query->where('title', $title);
    }

    /**
     * Scope для администраторов
     */
    public function scopeAdmins($query)
    {
        return $query->where('title', self::ADMIN);
    }

    /**
     * Scope для пользователей
     */
    public function scopeUsers($query)
    {
        return $query->where('title', self::USER);
    }

    /**
     * Scope для менеджеров
     */
    public function scopeManagers($query)
    {
        return $query->where('title', self::MANAGER);
    }

    /**
     * Проверить, является ли роль административной
     */
    public function getIsAdminAttribute()
    {
        return $this->title === self::ADMIN;
    }

    /**
     * Проверить, является ли роль пользовательской
     */
    public function getIsUserAttribute()
    {
        return $this->title === self::USER;
    }

    /**
     * Проверить, является ли роль менеджерской
     */
    public function getIsManagerAttribute()
    {
        return $this->title === self::MANAGER;
    }

    /**
     * Получить все доступные роли
     */
    public static function getAvailableRoles()
    {
        return [
            self::ADMIN,
            self::USER,
            self::MANAGER,
        ];
    }

    /**
     * Получить отображаемое название роли
     */
    public function getDisplayNameAttribute()
    {
        return match($this->title) {
            self::ADMIN => 'Администратор',
            self::USER => 'Пользователь',
            self::MANAGER => 'Менеджер',
            default => $this->title,
        };
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Проверяем, есть ли необходимые роли
        if (Role::count() === 0) {
            $this->call(RoleSeeder::class);
        }

        $users = [
            [
                'login' => 'admin',
                'password' => 'admin123',
                'role_title' => Role::ADMIN,
                'name_data' => [
                    'surname' => 'Админов',
                    'name' => 'Админ',
                    'patronymic' => 'Админович'
                ],
                'contact_data' => [
                    'phone' => '79160000001',
                    'email' => 'admin@rzd.ru'
                ]
            ],
            [
                'login' => 'manager',
                'password' => 'manager123',
                'role_title' => Role::MANAGER,
                'name_data' => [
                    'surname' => 'Менеджеров',
                    'name' => 'Менеджер',
                    'patronymic' => 'Менеджерович'
                ],
                'contact_data' => [
                    'phone' => '79160000002',
                    'email' => 'manager@rzd.ru'
                ]
            ],
            [
                'login' => 'user1',
                'password' => 'user123',
                'role_title' => Role::USER,
                'name_data' => [
                    'surname' => 'Иванов',
                    'name' => 'Иван',
                    'patronymic' => 'Иванович'
                ],
                'contact_data' => [
                    'phone' => '79160000003',
                    'email' => 'ivanov@example.com'
                ]
            ],
        ];

        foreach ($users as $userData) {
            try {
                User::createWithDetails(
                    $userData['login'],
                    $userData['password'],
                    $userData['name_data'],
                    $userData['contact_data'],
                    $userData['role_title']
                );

                $this->command->info("Создан пользователь: {$userData['login']}");
            } catch (\Exception $e) {
                $this->command->error("Ошибка при создании пользователя {$userData['login']}: {$e->getMessage()}");
            }
        }

        $this->command->info('Тестовые пользователи созданы успешно!');
    }
}

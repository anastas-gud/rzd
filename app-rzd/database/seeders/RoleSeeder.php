<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'title' => Role::ADMIN,
            ],
            [
                'title' => Role::USER,
            ],
            [
                'title' => Role::MANAGER,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['title' => $role['title']],
                $role
            );
        }

        $this->command->info('Роли созданы успешно!');
        $this->command->info('Доступные роли: ' . implode(', ', Role::getAvailableRoles()));
    }
}

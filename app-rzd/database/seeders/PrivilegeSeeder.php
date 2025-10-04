<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Privilege;

class PrivilegeSeeder extends Seeder
{
    public function run(): void
    {
        $privileges = [
            [
                'title' => 'Детская льгота',
                'description' => 'Льгота для детей до 10 лет',
                'discount' => 50.00,
            ],
            [
                'title' => 'Школьная льгота',
                'description' => 'Льгота для школьников от 10 до 17 лет',
                'discount' => 30.00,
            ],
            [
                'title' => 'Пенсионная льгота',
                'description' => 'Льгота для пенсионеров от 60 лет',
                'discount' => 40.00,
            ],
            [
                'title' => 'Инвалидность',
                'description' => 'Льгота для людей с инвалидностью',
                'discount' => 60.00,
            ],
            [
                'title' => 'Многодетная семья',
                'description' => 'Льгота для членов многодетных семей',
                'discount' => 25.00,
            ],
            [
                'title' => 'Студенческая льгота',
                'description' => 'Льгота для студентов очной формы обучения',
                'discount' => 35.00,
            ],
            [
                'title' => 'Ветеран труда',
                'description' => 'Льгота для ветеранов труда',
                'discount' => 45.00,
            ],
        ];

        foreach ($privileges as $privilege) {
            Privilege::firstOrCreate(
                ['title' => $privilege['title']],
                $privilege
            );
        }

        // Создаем дополнительные случайные льготы
        Privilege::factory()->count(2)->create();

        $this->command->info('Льготы созданы успешно!');
        $this->command->info('Всего льгот: ' . Privilege::count());
    }
}

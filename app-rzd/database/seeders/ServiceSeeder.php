<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Питание в поезде',
                'description' => 'Полноценное трехразовое питание',
                'base_price' => 1500.00,
            ],
            [
                'title' => 'Постельное белье',
                'description' => 'Комплект чистого постельного белья',
                'base_price' => 500.00,
            ],
            [
                'title' => 'Страхование',
                'description' => 'Страхование жизни и здоровья на время поездки',
                'base_price' => 300.00,
            ],
            [
                'title' => 'Дополнительный багаж',
                'description' => 'Дополнительные 10 кг багажа',
                'base_price' => 800.00,
            ],
            [
                'title' => 'Встреча на вокзале',
                'description' => 'Персональная встреча и помощь с багажом',
                'base_price' => 2000.00,
            ],
            [
                'title' => 'Бизнес-зал',
                'description' => 'Доступ в бизнес-зал ожидания',
                'base_price' => 1000.00,
            ],
            [
                'title' => 'Детское питание',
                'description' => 'Специальное питание для детей',
                'base_price' => 800.00,
            ],
            [
                'title' => 'Вегетарианское питание',
                'description' => 'Специальное вегетарианское меню',
                'base_price' => 1200.00,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['title' => $service['title']],
                $service
            );
        }

        // Создаем дополнительные случайные услуги
        Service::factory()->count(3)->create();

        $this->command->info('Услуги созданы успешно!');
        $this->command->info('Всего услуг: ' . Service::count());
    }
}

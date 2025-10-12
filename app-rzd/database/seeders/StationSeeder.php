<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Station;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            [
                'title' => 'Москва (Казанский вокзал)',
                'city' => 'Москва',
                'address' => 'Комсомольская пл., 2',
                'phone' => '84952668300',
            ],
            [
                'title' => 'Москва (Курский вокзал)',
                'city' => 'Москва',
                'address' => 'ул. Земляной Вал, 29',
                'phone' => '84952662839',
            ],
            [
                'title' => 'Санкт-Петербург (Главный вокзал)',
                'city' => 'Санкт-Петербург',
                'address' => 'Невский пр-т, 85',
                'phone' => '88124574444',
            ],
            [
                'title' => 'Нижний Новгород (Московский вокзал)',
                'city' => 'Нижний Новгород',
                'address' => 'пл. Революции, 2а',
                'phone' => '88312483800',
            ],
            [
                'title' => 'Екатеринбург (Главный вокзал)',
                'city' => 'Екатеринбург',
                'address' => 'ул. Вокзальная, 22',
                'phone' => '83433765555',
            ],
            [
                'title' => 'Новосибирск (Главный вокзал)',
                'city' => 'Новосибирск',
                'address' => 'ул. Шамшурина, 43',
                'phone' => '73832204444',
            ],
            [
                'title' => 'Казань (Главный вокзал)',
                'city' => 'Казань',
                'address' => 'ул. Привокзальная, 1',
                'phone' => '88432393700',
            ],
            [
                'title' => 'Самара (Главный вокзал)',
                'city' => 'Самара',
                'address' => 'ул. Алексея Толстого, 3',
                'phone' => '88462330303',
            ],
            [
                'title' => 'Саратов (Главный вокзал)',
                'city' => 'Саратов',
                'address' => 'пл. Привокзальная',
                'phone' => '83812303030',
            ],
            [
                'title' => 'Челябинск (Главный вокзал)',
                'city' => 'Челябинск',
                'address' => 'пл. Революции, 1',
                'phone' => '83512655555',
            ],
        ];

        foreach ($stations as $station) {
            Station::firstOrCreate(
                ['title' => $station['title']],
                $station
            );
        }

        // Создаем дополнительные случайные станции
        Station::factory()->count(5)->create();

        $this->command->info('Станции созданы успешно!');
        $this->command->info('Всего станций: ' . Station::count());
        $this->command->info('Города: ' . Station::getCities()->implode(', '));
    }
}
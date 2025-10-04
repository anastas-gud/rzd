<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Train;

class TrainSeeder extends Seeder
{
    public function run(): void
    {
        $trains = [
            [
                'title' => 'Сапсан-001',
                'carriage_count' => 10,
            ],
            [
                'title' => 'Ласточка-002',
                'carriage_count' => 8,
            ],
            [
                'title' => 'Невский экспресс',
                'carriage_count' => 12,
            ],
            [
                'title' => 'Восточный экспресс',
                'carriage_count' => 9,
            ],
            [
                'title' => 'Волга-005',
                'carriage_count' => 11,
            ],
        ];

        foreach ($trains as $train) {
            Train::firstOrCreate(
                $train
            );
        }

        $this->command->info('Поезда созданы успешно!');
        $this->command->info('Всего поездов: ' . Train::count());
    }
}

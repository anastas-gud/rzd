<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarriageType;

class CarriageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $carriageTypes = [
            [
                'title' => 'Плацкарт',
                'seats_number' => 54,
            ],
            [
                'title' => 'Купе',
                'seats_number' => 36,
            ],
            [
                'title' => 'СВ',
                'seats_number' => 18,
            ],
            [
                'title' => 'Сидячий',
                'seats_number' => 68,
            ],
        ];

        foreach ($carriageTypes as $type) {
            CarriageType::firstOrCreate(
                ['title' => $type['title']],
                $type
            );
        }

        $this->command->info('Типы вагонов созданы успешно!');
        $this->command->info('Всего типов: ' . CarriageType::count());
    }
}

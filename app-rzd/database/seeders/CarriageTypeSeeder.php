<?php

namespace Database\Seeders;

use File;
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
                'layout_json' => File::get(database_path('seeders/data/plaz.json')),
            ],
            [
                'title' => 'Купе',
                'seats_number' => 36,
                'layout_json' => File::get(database_path('seeders/data/kype.json')),
            ],
            [
                'title' => 'СВ',
                'seats_number' => 18,
                'layout_json' => File::get(database_path('seeders/data/sv.json')),
            ],
            [
                'title' => 'Сидячий',
                'seats_number' => 68,
                'layout_json' => File::get(database_path('seeders/data/sid.json')),
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

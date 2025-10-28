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
                'layout_json' => $this->generateSeatsLayout(54),
            ],
            [
                'title' => 'Купе',
                'seats_number' => 36,
                'layout_json' => $this->generateSeatsLayout(36, 2),
            ],
            [
                'title' => 'СВ',
                'seats_number' => 18,
                'layout_json' => $this->generateSeatsLayout(18, 2, 20, 60, 15, 40),
            ],
            [
                'title' => 'Сидячий',
                'seats_number' => 68,
                'layout_json' => $this->generateSeatsLayout(68),
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

    private function generateSeatsLayout($totalSeats, $columns = 2, $startX = 10, $columnSpacing = 40, $startY = 10, $rowSpacing = 30)
    {
        $seats = [];
        for ($i = 0; $i < $totalSeats; $i++) {
            $row = floor($i / $columns);
            $column = $i % $columns;
            $x = $startX + $column * $columnSpacing;
            $y = $startY + $row * $rowSpacing;
            $seats[] = ['id' => strval($i + 1), 'x' => $x, 'y' => $y];
        }
        return json_encode(['seats' => $seats]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carriage;
use App\Models\Train;
use App\Models\CarriageType;

class CarriageSeeder extends Seeder
{
    public function run(): void
    {
        $trains = Train::all();
        $carriageTypes = CarriageType::all();

        if ($trains->isEmpty() || $carriageTypes->isEmpty()) {
            $this->command->warn('Необходимо сначала создать поезда и типы вагонов!');
            return;
        }

        $carriagesCreated = 0;

        foreach ($trains as $train) {
            $this->createCarriagesForTrain($train, $carriageTypes, $carriagesCreated);
        }

        $this->command->info('Вагоны созданы успешно!');
        $this->command->info('Всего вагонов: ' . $carriagesCreated);
    }

    private function createCarriagesForTrain(Train $train, $carriageTypes, &$carriagesCreated): void
    {
        $carriageCount = $train->carriage_count;

        // Простое распределение: по порядку типов вагонов
        $typeIndex = 0;
        $typesCount = $carriageTypes->count();

        for ($carriageNumber = 1; $carriageNumber <= $carriageCount; $carriageNumber++) {
            $carriageType = $carriageTypes[$typeIndex];

            Carriage::firstOrCreate(
                [
                    'train_id' => $train->id,
                    'number' => $carriageNumber,
                ],
                [
                    'carriage_type_id' => $carriageType->id,
                ]
            );

            $carriagesCreated++;
            $typeIndex = ($typeIndex + 1) % $typesCount; // Циклическое переключение типов
        }
    }
}

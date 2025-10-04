<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seat;
use App\Models\Carriage;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $carriages = Carriage::with('type')->get();

        if ($carriages->isEmpty()) {
            $this->command->warn('Необходимо сначала создать вагоны!');
            return;
        }

        $seatsCreated = 0;

        foreach ($carriages as $carriage) {
            $seatsCount = $carriage->type->seats_number;
            $basePrice = $this->getBasePriceByCarriageType($carriage->type->title);

            // Создаем места для вагона
            for ($seatNumber = 1; $seatNumber <= $seatsCount; $seatNumber++) {
                $price = $this->calculateSeatPrice($basePrice, $seatNumber, $carriage->type->title);
                $position = $this->getSeatPosition($seatNumber, $seatsCount);
                $type = $this->getSeatType($position);

                Seat::firstOrCreate(
                    [
                        'carriage_id' => $carriage->id,
                        'number' => $seatNumber,
                    ],
                    [
                        'price' => $price,
                    ]
                );

                $seatsCreated++;
            }
        }

        $this->command->info('Места созданы успешно!');
        $this->command->info('Всего мест: ' . $seatsCreated);
    }

    private function getBasePriceByCarriageType(string $type): float
    {
        return match($type) {
            'СВ' => 4000,
            'Купе' => 2500,
            'Плацкарт' => 1500,
            'Сидячий' => 800,
            default => 1000,
        };
    }

    private function calculateSeatPrice(float $basePrice, int $seatNumber, string $carriageType): float
    {
        $price = $basePrice;

        // Добавляем надбавку за лучшие места
        if ($carriageType === 'Плацкарт' || $carriageType === 'Купе') {
            if ($this->isLowerSeat($seatNumber)) {
                $price += 200; // Нижние места дороже
            }
        }

        // Места у окна дороже
        if ($this->isWindowSeat($seatNumber, $carriageType)) {
            $price += 100;
        }

        return $price;
    }

    private function getSeatPosition(int $seatNumber, int $totalSeats): string
    {
        if ($totalSeats <= 36) { // Купе
            return $this->getCoupeSeatPosition($seatNumber);
        } elseif ($totalSeats <= 54) { // Плацкарт
            return $this->getPlatzkartSeatPosition($seatNumber);
        } else { // Сидячий
            return $this->getSeatedSeatPosition($seatNumber);
        }
    }

    private function getCoupeSeatPosition(int $seatNumber): string
    {
        $positions = ['нижнее', 'верхнее', 'нижнее', 'верхнее'];
        return $positions[($seatNumber - 1) % 4] ?? 'нижнее';
    }

    private function getPlatzkartSeatPosition(int $seatNumber): string
    {
        if ($seatNumber <= 36) {
            $positions = ['нижнее', 'верхнее', 'нижнее', 'верхнее'];
            return $positions[($seatNumber - 1) % 4] ?? 'нижнее';
        } else {
            return $seatNumber % 2 === 0 ? 'боковое верхнее' : 'боковое нижнее';
        }
    }

    private function getSeatedSeatPosition(int $seatNumber): string
    {
        return $seatNumber % 2 === 0 ? 'у прохода' : 'у окна';
    }

    private function getSeatType(string $position): string
    {
        return match($position) {
            'нижнее', 'боковое нижнее' => 'lower',
            'верхнее', 'боковое верхнее' => 'upper',
            'у окна' => 'window',
            'у прохода' => 'aisle',
            default => 'standard',
        };
    }

    private function isLowerSeat(int $seatNumber): bool
    {
        return $seatNumber % 4 === 1 || $seatNumber % 4 === 3;
    }

    private function isWindowSeat(int $seatNumber, string $carriageType): bool
    {
        if ($carriageType === 'Сидячий') {
            return $seatNumber % 2 === 1;
        }
        return false;
    }

    private function isLuxurySeat(int $seatNumber, string $carriageType): bool
    {
        return $carriageType === 'СВ';
    }
}

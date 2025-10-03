<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Train;

class TrainFactory extends Factory
{
    protected $model = Train::class;

    public function definition(): array
    {
        $trainNames = [
            'Сапсан', 'Ласточка', 'Невский экспресс', 'Урал', 'Сибиряк',
            'Волга', 'Дон', 'Байкал', 'Алтай', 'Кавказ'
        ];

        return [
            'title' => $this->faker->randomElement($trainNames) . '-' . $this->faker->numberBetween(100, 999),
            'carriage_count' => $this->faker->numberBetween(5, 15),
        ];
    }

    public function withCarriages(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'carriage_count' => $count,
        ]);
    }
}

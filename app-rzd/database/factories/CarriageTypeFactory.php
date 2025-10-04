<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CarriageType;

class CarriageTypeFactory extends Factory
{
    protected $model = CarriageType::class;

    public function definition(): array
    {
        $types = [
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

        $type = $this->faker->randomElement($types);

        return [
            'title' => $type['title'],
            'seats_number' => $type['seats_number'],
        ];
    }

    public function platzkart(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Плацкарт',
            'seats_number' => 54,
        ]);
    }

    public function coupe(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Купе',
            'seats_number' => 36,
        ]);
    }

    public function sv(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'СВ (Люкс)',
            'seats_number' => 18,
        ]);
    }

    public function seated(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Сидячий',
            'seats_number' => 68,
            'price_coefficient' => 0.7,
        ]);
    }
}

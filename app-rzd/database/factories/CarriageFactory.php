<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Carriage;
use App\Models\Train;
use App\Models\CarriageType;

class CarriageFactory extends Factory
{
    protected $model = Carriage::class;

    public function definition(): array
    {
        return [
            'train_id' => Train::factory(),
            'carriage_type_id' => CarriageType::factory(),
            'number' => $this->faker->numberBetween(1, 20),
        ];
    }

    public function forTrain(Train $train): static
    {
        return $this->state(fn (array $attributes) => [
            'train_id' => $train->id,
        ]);
    }

    public function forCarriageType(CarriageType $carriageType): static
    {
        return $this->state(fn (array $attributes) => [
            'carriage_type_id' => $carriageType->id,
        ]);
    }

    public function withNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'number' => $number,
        ]);
    }

    public function platzkart(): static
    {
        return $this->state(function (array $attributes) {
            $carriageType = CarriageType::where('title', 'Плацкарт')->first();
            if (!$carriageType) {
                $carriageType = CarriageType::factory()->platzkart()->create();
            }

            return [
                'carriage_type_id' => $carriageType->id,
            ];
        });
    }

    public function coupe(): static
    {
        return $this->state(function (array $attributes) {
            $carriageType = CarriageType::where('title', 'Купе')->first();
            if (!$carriageType) {
                $carriageType = CarriageType::factory()->coupe()->create();
            }

            return [
                'carriage_type_id' => $carriageType->id,
            ];
        });
    }

    public function luxury(): static
    {
        return $this->state(function (array $attributes) {
            $carriageType = CarriageType::where('title', 'СВ (Люкс)')->first();
            if (!$carriageType) {
                $carriageType = CarriageType::factory()->luxury()->create();
            }

            return [
                'carriage_type_id' => $carriageType->id,
            ];
        });
    }

    public function seated(): static
    {
        return $this->state(function (array $attributes) {
            $carriageType = CarriageType::where('title', 'Сидячий')->first();
            if (!$carriageType) {
                $carriageType = CarriageType::factory()->seated()->create();
            }

            return [
                'carriage_type_id' => $carriageType->id,
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

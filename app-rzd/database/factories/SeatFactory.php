<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seat;
use App\Models\Carriage;

class SeatFactory extends Factory
{
    protected $model = Seat::class;

    public function definition(): array
    {
        $carriage = Carriage::inRandomOrder()->first() ?? Carriage::factory()->create();
        $basePrice = $this->faker->numberBetween(500, 3000);

        return [
            'carriage_id' => $carriage->id,
            'number' => $this->faker->numberBetween(1, $carriage->type->seats_number ?? 54),
            'price' => $basePrice + ($this->faker->numberBetween(0, 20) * 10), // Добавляем вариативность цены
        ];
    }

    public function forCarriage(Carriage $carriage): static
    {
        return $this->state(fn (array $attributes) => [
            'carriage_id' => $carriage->id,
        ]);
    }

    public function window(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => '1000',
        ]);
    }

    public function aisle(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => '800',
        ]);
    }

    public function lower(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => '2000',
        ]);
    }

    public function upper(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => '1500',
        ]);
    }

    public function withNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'number' => $number,
        ]);
    }

    public function withPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
        ]);
    }
}

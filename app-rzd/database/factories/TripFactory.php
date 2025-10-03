<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Trip;
use App\Models\Train;
use App\Models\Route;
use Carbon\Carbon;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        $train = Train::inRandomOrder()->first() ?? Train::factory()->create();
        $route = Route::inRandomOrder()->first() ?? Route::factory()->create();

        $startDate = $this->faker->dateTimeBetween('+1 days', '+30 days');
        $durationMinutes = $route->duration_minutes ?? $this->faker->numberBetween(60, 600);

        return [
            'train_id' => $train->id,
            'route_id' => $route->id,
            'start_timestamp' => $startDate,
            'end_timestamp' => Carbon::instance($startDate)->addMinutes($durationMinutes),
            'is_denied' => false,
        ];
    }

    public function forTrain(Train $train): static
    {
        return $this->state(fn (array $attributes) => [
            'train_id' => $train->id,
        ]);
    }

    public function forRoute(Route $route): static
    {
        return $this->state(fn (array $attributes) => [
            'route_id' => $route->id,
            'base_price' => $route->price,
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_timestamp' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
            'is_denied' => false,
        ]);
    }

    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_timestamp' => $this->faker->dateTimeBetween('-2 hours', '-30 minutes'),
            'end_timestamp' => $this->faker->dateTimeBetween('+1 hours', '+5 hours'),
            'is_denied' => false,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_timestamp' => $this->faker->dateTimeBetween('-10 days', '-1 days'),
            'end_timestamp' => $this->faker->dateTimeBetween('-9 days', '-1 hours'),
            'is_denied' => false,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_denied' => true,
        ]);
    }

    public function denied(): static
    {
        return $this->cancelled();
    }

    public function onDate(Carbon $date): static
    {
        $startTime = $date->copy()->setTime(
            $this->faker->numberBetween(6, 23),
            $this->faker->randomElement([0, 15, 30, 45])
        );

        return $this->state(fn (array $attributes) => [
            'start_timestamp' => $startTime,
            'end_timestamp' => $startTime->copy()->addMinutes($attributes['duration_minutes'] ?? 180),
        ]);
    }

    private function generateTripNumber(): string
    {
        return 'TR' . $this->faker->unique()->numberBetween(1000, 9999);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Route;
use App\Models\Station;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        $startStation = Station::inRandomOrder()->first() ?? Station::factory()->create();
        $endStation = Station::where('id', '!=', $startStation->id)->inRandomOrder()->first() ?? Station::factory()->create();

        return [
            'start_station_id' => $startStation->id,
            'end_station_id' => $endStation->id,
            'number' => $this->generateRouteNumber(),
        ];
    }

    public function betweenStations(Station $start, Station $end): static
    {
        return $this->state(fn (array $attributes) => [
            'start_station_id' => $start->id,
            'end_station_id' => $end->id,
        ]);
    }

    public function fromMoscow(): static
    {
        $moscowStation = Station::where('city', 'Москва')->inRandomOrder()->first() ?? Station::factory()->moscow()->create();
        $otherStation = Station::where('city', '!=', 'Москва')->inRandomOrder()->first() ?? Station::factory()->create();

        return $this->state(fn (array $attributes) => [
            'start_station_id' => $moscowStation->id,
            'end_station_id' => $otherStation->id,
        ]);
    }

    public function toSpb(): static
    {
        $spbStation = Station::where('city', 'Санкт-Петербург')->inRandomOrder()->first() ?? Station::factory()->spb()->create();
        $otherStation = Station::where('city', '!=', 'Санкт-Петербург')->inRandomOrder()->first() ?? Station::factory()->create();

        return $this->state(fn (array $attributes) => [
            'start_station_id' => $otherStation->id,
            'end_station_id' => $spbStation->id,
        ]);
    }

    private function generateRouteNumber(): string
    {
        $letters = ['А', 'Б', 'В', 'Г', 'Д', 'Е'];
        return sprintf('%03d', $this->faker->numberBetween(1, 999)) . $this->faker->randomElement($letters);
    }
}

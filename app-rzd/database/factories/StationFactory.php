<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Station;

class StationFactory extends Factory
{
    protected $model = Station::class;

    public function definition(): array
    {
        $cities = ['Москва', 'Санкт-Петербург', 'Нижний Новгород', 'Екатеринбург', 'Новосибирск', 'Казань', 'Самара', 'Саратов', 'Челябинск', 'Ростов-на-Дону'];
        $stationTypes = ['Центральный вокзал', 'Железнодорожный вокзал', 'Северный вокзал', 'Южный вокзал', 'Главный вокзал'];

        return [
            'title' => $this->faker->randomElement($stationTypes) . ' ' . $this->faker->city(),
            'city' => $this->faker->randomElement($cities),
            'address' => $this->faker->address(),
            'phone' => '79' . $this->faker->numerify('#######'),
            'photo_path' => $this->faker->boolean(30) ? 'stations/' . $this->faker->uuid() . '.jpg' : null,
        ];
    }

    public function moscow(): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => 'Москва',
            'title' => 'Москва (' . $this->faker->randomElement(['Казанский', 'Курский', 'Ленинградский', 'Ярославский']) . ' вокзал)',
        ]);
    }

    public function spb(): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => 'Санкт-Петербург',
            'title' => 'Санкт-Петербург (' . $this->faker->randomElement(['Главный', 'Витебский', 'Балтийский', 'Московский']) . ' вокзал)',
        ]);
    }

    public function withoutPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => null,
        ]);
    }

    public function withoutPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => null,
        ]);
    }

    public function inCity(string $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => $city,
        ]);
    }
}

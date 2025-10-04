<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $serviceTypes = [
            [
                'title' => 'Питание в поезде',
                'description' => 'Полноценное трехразовое питание',
                'base_price' => 1500.00,
            ],
            [
                'title' => 'Постельное белье',
                'description' => 'Комплект чистого постельного белья',
                'base_price' => 500.00,
            ],
            [
                'title' => 'Страхование',
                'description' => 'Страхование жизни и здоровья на время поездки',
                'base_price' => 300.00,
            ],
            [
                'title' => 'Дополнительный багаж',
                'description' => 'Дополнительные 10 кг багажа',
                'base_price' => 800.00,
            ],
            [
                'title' => 'Встреча на вокзале',
                'description' => 'Персональная встреча и помощь с багажом',
                'base_price' => 2000.00,
            ],
            [
                'title' => 'Бизнес-зал',
                'description' => 'Доступ в бизнес-зал ожидания',
                'base_price' => 1000.00,
            ],
            [
                'title' => 'Экскурсия по городу',
                'description' => 'Обзорная экскурсия по городу прибытия',
                'base_price' => 2500.00,
            ],
        ];

        $service = $this->faker->randomElement($serviceTypes);

        return [
            'title' => $service['title'],
            'description' => $service['description'],
            'base_price' => $service['base_price'],
        ];
    }

    public function food(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Питание в поезде',
            'description' => 'Полноценное трехразовое питание',
            'base_price' => 1500.00,
        ]);
    }

    public function bedding(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Постельное белье',
            'description' => 'Комплект чистого постельного белья',
            'base_price' => 500.00,
        ]);
    }

    public function insurance(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Страхование',
            'description' => 'Страхование жизни и здоровья на время поездки',
            'base_price' => 300.00,
        ]);
    }

    public function luggage(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Дополнительный багаж',
            'description' => 'Дополнительные 10 кг багажа',
            'base_price' => 800.00,
        ]);
    }

    public function withPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'base_price' => $price,
        ]);
    }
}

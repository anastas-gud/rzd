<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Privilege;

class PrivilegeFactory extends Factory
{
    protected $model = Privilege::class;

    public function definition(): array
    {
        $privilegeTypes = [
            [
                'title' => 'Детская льгота',
                'description' => 'Льгота для детей до 10 лет',
                'discount' => 50.00,
            ],
            [
                'title' => 'Школьная льгота',
                'description' => 'Льгота для школьников от 10 до 17 лет',
                'discount' => 30.00,
            ],
            [
                'title' => 'Пенсионная льгота',
                'description' => 'Льгота для пенсионеров от 60 лет',
                'discount' => 40.00,
            ],
            [
                'title' => 'Инвалидность',
                'description' => 'Льгота для людей с инвалидностью',
                'discount' => 60.00,
            ],
            [
                'title' => 'Многодетная семья',
                'description' => 'Льгота для членов многодетных семей',
                'discount' => 25.00,
            ],
            [
                'title' => 'Студенческая льгота',
                'description' => 'Льгота для студентов очной формы обучения',
                'discount' => 35.00,
            ],
        ];

        $privilege = $this->faker->randomElement($privilegeTypes);

        return [
            'title' => $privilege['title'],
            'description' => $privilege['description'],
            'discount' => $privilege['discount'],
        ];
    }

    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Детская льгота',
            'description' => 'Льгота для детей до 10 лет',
            'discount' => 50.00,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Школьная льгота',
            'description' => 'Льгота для школьников от 10 до 17 лет',
            'discount' => 30.00,
        ]);
    }

    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Пенсионная льгота',
            'description' => 'Льгота для пенсионеров от 60 лет',
            'discount' => 40.00,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Инвалидность',
            'description' => 'Льгота для людей с инвалидностью',
            'discount' => 60.00,
        ]);
    }

    public function family(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Многодетная семья',
            'description' => 'Льгота для членов многодетных семей',
            'discount' => 25.00,
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->randomElement([
                Role::ADMIN,
                Role::USER,
                Role::MANAGER
            ]),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => Role::ADMIN,
        ]);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => Role::USER,
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => Role::MANAGER,
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'phone' => '79' . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}

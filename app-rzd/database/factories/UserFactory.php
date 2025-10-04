<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Name;
use App\Models\Contact;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Создаем связанные записи
        $name = Name::factory()->create();
        $contact = Contact::factory()->create();
        $role = Role::where('title', Role::USER)->first();

        return [
            'login' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'),
            'role_id' => $role ? $role->id : Role::factory()->create(['title' => Role::USER])->id,
            'name_id' => $name->id,
            'contact_id' => $contact->id,
        ];
    }

    public function admin(): static
    {
        $role = Role::where('title', Role::ADMIN)->first();

        return $this->state(fn (array $attributes) => [
            'role_id' => $role ? $role->id : Role::factory()->create(['title' => Role::ADMIN])->id,
        ]);
    }

    public function manager(): static
    {
        $role = Role::where('title', Role::MANAGER)->first();

        return $this->state(fn (array $attributes) => [
            'role_id' => $role ? $role->id : Role::factory()->create(['title' => Role::MANAGER])->id,
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Name;

class NameFactory extends Factory
{
    protected $model = Name::class;

    public function definition(): array
    {
        // Генерация мужских и женских отчеств
        $malePatronymics = ['Александрович', 'Сергеевич', 'Дмитриевич', 'Андреевич', 'Михайлович', 'Иванович', 'Петрович'];
        $femalePatronymics = ['Александровна', 'Сергеевна', 'Дмитриевна', 'Андреевна', 'Михайловна', 'Ивановна', 'Петровна'];

        // Случайно выбираем пол
        $isMale = $this->faker->boolean();

        if ($isMale) {
            $name = $this->faker->firstNameMale();
            $patronymic = $this->faker->randomElement($malePatronymics);
        } else {
            $name = $this->faker->firstNameFemale();
            $patronymic = $this->faker->randomElement($femalePatronymics);
        }

        return [
            'surname' => $this->faker->lastName(),
            'name' => $name,
            'patronymic' => $this->faker->boolean(70) ? $patronymic : null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Materia;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Materia>
 */
class MateriaFactory extends Factory
{
    protected $model = Materia::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'clave' => strtoupper($this->faker->unique()->bothify('MAT-###')),
            'nombre' => $this->faker->unique()->randomElement([
                'Matemáticas', 'Español', 'Ciencias', 'Historia', 'Geografía',
                'Inglés', 'Educación Física', 'Artes', 'Formación Cívica',
            ]),
        ];
    }
}

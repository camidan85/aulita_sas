<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\ExpedienteMedico;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpedienteMedico>
 */
class ExpedienteMedicoFactory extends Factory
{
    protected $model = ExpedienteMedico::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'alumno_id' => Alumno::factory(),
            'tipo_sangre' => $this->faker->randomElement(['O+', 'O-', 'A+', 'A-', 'B+', 'AB+']),
            'alergias' => $this->faker->optional()->word(),
            'medicamentos' => $this->faker->optional()->word(),
            'contacto_emergencia_nombre' => $this->faker->name(),
            'contacto_emergencia_telefono' => $this->faker->numerify('55########'),
        ];
    }
}

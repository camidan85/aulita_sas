<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Calificacion>
 */
class CalificacionFactory extends Factory
{
    protected $model = Calificacion::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'alumno_id' => Alumno::factory(),
            'materia_id' => Materia::factory(),
            'periodo_id' => Periodo::factory(),
            'calificacion' => $this->faker->randomFloat(2, 5, 10),
        ];
    }
}

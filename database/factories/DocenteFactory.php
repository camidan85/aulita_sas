<?php

namespace Database\Factories;

use App\Models\Docente;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Docente>
 */
class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'numero_empleado' => $this->faker->unique()->numerify('EMP###'),
            'nombre' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
            'telefono' => $this->faker->numerify('55########'),
            'estatus' => 'activo',
        ];
    }
}

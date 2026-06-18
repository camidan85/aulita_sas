<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alumno>
 */
class AlumnoFactory extends Factory
{
    protected $model = Alumno::class;

    public function definition(): array
    {
        $sexo = $this->faker->randomElement(['M', 'F']);

        return [
            'school_id' => School::factory(),
            'grupo_id' => null,
            'matricula' => $this->faker->unique()->numerify('A#######'),
            'nombre' => $this->faker->firstName($sexo === 'M' ? 'male' : 'female'),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
            'curp' => strtoupper($this->faker->unique()->bothify('????######??????##')),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-15 years', '-12 years')->format('Y-m-d'),
            'sexo' => $sexo,
            'correo' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->numerify('55########'),
            'estatus' => 'activo',
        ];
    }
}

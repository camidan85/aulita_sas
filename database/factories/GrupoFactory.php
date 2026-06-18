<?php

namespace Database\Factories;

use App\Models\CicloEscolar;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grupo>
 */
class GrupoFactory extends Factory
{
    protected $model = Grupo::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'grado_id' => Grado::factory(),
            'nombre' => $this->faker->randomElement(['A', 'B', 'C']),
            'ciclo_id' => CicloEscolar::factory(),
            'docente_titular_id' => null,
        ];
    }
}

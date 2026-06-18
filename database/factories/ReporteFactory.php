<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Reporte;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reporte>
 */
class ReporteFactory extends Factory
{
    protected $model = Reporte::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'alumno_id' => Alumno::factory(),
            'profesor_id' => User::factory(),
            'tipo' => 'mala_conducta',
            'descripcion' => $this->faker->sentence(12),
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'requiere_firma' => false,
        ];
    }
}

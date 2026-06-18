<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asistencia>
 */
class AsistenciaFactory extends Factory
{
    protected $model = Asistencia::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'alumno_id' => Alumno::factory(),
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'estatus' => 'presente',
            'origen' => 'qr',
        ];
    }
}

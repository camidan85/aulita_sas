<?php

namespace Database\Factories;

use App\Models\AlertaRiesgo;
use App\Models\Alumno;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlertaRiesgo>
 */
class AlertaRiesgoFactory extends Factory
{
    protected $model = AlertaRiesgo::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'alumno_id' => Alumno::factory(),
            'tipo' => AlertaRiesgo::TIPO_3_FALTAS,
            'detalle' => null,
            'atendida' => false,
            'generada_en' => now(),
        ];
    }
}

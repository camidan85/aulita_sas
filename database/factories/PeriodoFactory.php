<?php

namespace Database\Factories;

use App\Models\CicloEscolar;
use App\Models\Periodo;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Periodo>
 */
class PeriodoFactory extends Factory
{
    protected $model = Periodo::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'nombre' => 'trimestre_1',
            'ciclo_id' => CicloEscolar::factory(),
            'fecha_inicio' => '2025-08-01',
            'fecha_fin' => '2025-11-15',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\CicloEscolar;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CicloEscolar>
 */
class CicloEscolarFactory extends Factory
{
    protected $model = CicloEscolar::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'nombre' => '2025-2026',
            'fecha_inicio' => '2025-08-01',
            'fecha_fin' => '2026-07-15',
            'vigente' => true,
        ];
    }
}

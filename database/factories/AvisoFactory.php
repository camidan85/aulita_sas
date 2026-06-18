<?php

namespace Database\Factories;

use App\Models\Aviso;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Aviso>
 */
class AvisoFactory extends Factory
{
    protected $model = Aviso::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'titulo' => $this->faker->sentence(4),
            'contenido' => $this->faker->paragraph(),
            'alcance' => 'escuela',
            'target_id' => null,
            'requiere_firma' => false,
            'publicado_por' => User::factory(),
            'fecha_publicacion' => now(),
        ];
    }
}

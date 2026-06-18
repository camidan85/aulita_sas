<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        $nombre = 'Secundaria '.$this->faker->unique()->lastName();

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'cct' => strtoupper($this->faker->bothify('##???####?')),
            'telefono' => $this->faker->numerify('55########'),
            'correo' => $this->faker->companyEmail(),
            'hora_corte_faltas' => '07:15:00',
            'timezone' => 'America/Mexico_City',
            'umbral_riesgo_calif' => 6.00,
            'estatus' => 'activa',
        ];
    }
}

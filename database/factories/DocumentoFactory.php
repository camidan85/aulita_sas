<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\Documento;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Documento>
 */
class DocumentoFactory extends Factory
{
    protected $model = Documento::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'alumno_id' => Alumno::factory(),
            'tipo' => $this->faker->randomElement(['curp', 'acta', 'certificado_primaria', 'comprobante_domicilio', 'otro']),
            'path' => 'documentos/'.$this->faker->uuid().'.pdf',
            'nombre_original' => $this->faker->word().'.pdf',
            'mime' => 'application/pdf',
            'size' => $this->faker->numberBetween(10000, 2000000),
        ];
    }
}

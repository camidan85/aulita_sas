<?php

namespace Database\Factories;

use App\Models\Grado;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grado>
 */
class GradoFactory extends Factory
{
    protected $model = Grado::class;

    public function definition(): array
    {
        $nivel = $this->faker->numberBetween(1, 3);

        return [
            'school_id' => School::factory(),
            'nombre' => "{$nivel}°",
            'nivel' => $nivel,
        ];
    }
}

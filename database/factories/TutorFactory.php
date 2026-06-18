<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tutor>
 */
class TutorFactory extends Factory
{
    protected $model = Tutor::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'nombre' => $this->faker->name(),
            'correo' => $this->faker->unique()->safeEmail(),
            'telefono' => '521'.$this->faker->numerify('##########'),
            'parentesco' => $this->faker->randomElement(['Padre', 'Madre', 'Tutor']),
        ];
    }
}

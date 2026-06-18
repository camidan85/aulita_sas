<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\QrToken;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<QrToken>
 */
class QrTokenFactory extends Factory
{
    protected $model = QrToken::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'alumno_id' => Alumno::factory(),
            'token' => Str::random(64),
            'activo' => true,
        ];
    }
}

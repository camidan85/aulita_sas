<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CatalogosSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $director;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $school = School::factory()->create();
        $this->director = User::factory()->create([
            'school_id' => $school->id,
            'email_verified_at' => now(),
        ]);
        $this->director->assignRole('director');
    }

    public static function rutasIndice(): array
    {
        return [
            ['alumnos.index'], ['alumnos.create'],
            ['grados.index'], ['grados.create'],
            ['grupos.index'], ['grupos.create'],
            ['materias.index'], ['materias.create'],
            ['docentes.index'], ['docentes.create'],
            ['tutores.index'], ['tutores.create'],
        ];
    }

    #[DataProvider('rutasIndice')]
    public function test_las_vistas_renderizan(string $ruta): void
    {
        $this->actingAs($this->director)
            ->get(route($ruta))
            ->assertOk();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumnoCrudTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private User $director;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        $this->director = User::factory()->create([
            'school_id' => $this->school->id,
            'email_verified_at' => now(),
        ]);
        $this->director->assignRole('director');
    }

    public function test_director_ve_la_lista_de_alumnos(): void
    {
        Alumno::factory()->create(['school_id' => $this->school->id]);

        $this->actingAs($this->director)
            ->get(route('alumnos.index'))
            ->assertOk()
            ->assertSee('Alumnos');
    }

    public function test_director_crea_un_alumno(): void
    {
        $payload = [
            'matricula' => 'A0001',
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'López',
            'curp' => 'PELJ100101HDFXXX01',
            'sexo' => 'M',
            'estatus' => 'activo',
        ];

        $this->actingAs($this->director)
            ->post(route('alumnos.store'), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('alumnos', [
            'matricula' => 'A0001',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_usuario_sin_permiso_no_accede(): void
    {
        $padre = User::factory()->create([
            'school_id' => $this->school->id,
            'email_verified_at' => now(),
        ]);
        $padre->assignRole('padre');

        $this->actingAs($padre)
            ->get(route('alumnos.index'))
            ->assertForbidden();
    }

    public function test_no_se_listan_alumnos_de_otra_escuela(): void
    {
        Alumno::factory()->create(['school_id' => $this->school->id, 'matricula' => 'MIA-1']);

        $otra = School::factory()->create();
        Alumno::factory()->create(['school_id' => $otra->id, 'matricula' => 'AJENA-1']);

        $this->actingAs($this->director)
            ->get(route('alumnos.index'))
            ->assertOk()
            ->assertSee('MIA-1')
            ->assertDontSee('AJENA-1');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\School;
use App\Models\User;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsistenciaVistasTest extends TestCase
{
    use RefreshDatabase;

    private User $director;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($school->id);

        $this->director = User::factory()->create(['school_id' => $school->id, 'email_verified_at' => now()]);
        $this->director->assignRole('director');

        $this->alumno = Alumno::factory()->create(['school_id' => $school->id]);
    }

    public function test_vista_de_escaneo_renderiza(): void
    {
        $this->actingAs($this->director)->get(route('asistencias.escanear'))->assertOk();
    }

    public function test_vista_de_asistencias_renderiza(): void
    {
        $this->actingAs($this->director)->get(route('asistencias.index'))->assertOk();
    }

    public function test_vista_de_alertas_renderiza(): void
    {
        $this->actingAs($this->director)->get(route('alertas.index'))->assertOk();
    }

    public function test_genera_y_muestra_el_qr_del_alumno(): void
    {
        $this->actingAs($this->director)
            ->get(route('alumnos.qr', $this->alumno))
            ->assertOk()
            ->assertSee('</svg>', false);

        $this->assertDatabaseHas('qr_tokens', [
            'alumno_id' => $this->alumno->id,
            'activo' => true,
        ]);
    }
}

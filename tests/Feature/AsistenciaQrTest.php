<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\School;
use App\Models\User;
use App\Services\QrTokenService;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AsistenciaQrTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_un_docente_registra_asistencia_escaneando_qr(): void
    {
        $school = School::factory()->create(['timezone' => 'UTC', 'hora_corte_faltas' => '07:15:00']);
        app(TenantManager::class)->setSchoolId($school->id);

        $alumno = Alumno::factory()->create(['school_id' => $school->id]);

        $docente = User::factory()->create(['school_id' => $school->id, 'email_verified_at' => now()]);
        $docente->assignRole('docente');

        // El codigo_qr se genera solo según la plantilla de la escuela.
        $contenido = app(QrTokenService::class)->contenido($alumno->fresh());

        $this->actingAs($docente)
            ->postJson(route('asistencias.registrar'), ['contenido' => $contenido])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('asistencias', [
            'alumno_id' => $alumno->id,
            'origen' => 'qr',
            'registrado_por' => $docente->id,
        ]);
    }

    public function test_un_qr_de_otra_escuela_es_rechazado(): void
    {
        $escuelaA = School::factory()->create();
        $escuelaB = School::factory()->create();

        app(TenantManager::class)->setSchoolId($escuelaB->id);
        $alumnoB = Alumno::factory()->create(['school_id' => $escuelaB->id]);

        // Código QR del alumno de la escuela B.
        $contenido = app(QrTokenService::class)->contenido($alumnoB->fresh());

        // Un docente de la escuela A intenta usarlo.
        $docenteA = User::factory()->create(['school_id' => $escuelaA->id, 'email_verified_at' => now()]);
        $docenteA->assignRole('docente');

        $this->actingAs($docenteA)
            ->postJson(route('asistencias.registrar'), ['contenido' => $contenido])
            ->assertStatus(422)
            ->assertJson(['ok' => false]);
    }
}

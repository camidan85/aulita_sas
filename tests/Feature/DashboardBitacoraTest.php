<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Bitacora;
use App\Models\School;
use App\Models\User;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardBitacoraTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private User $director;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);

        $this->director = User::factory()->create(['school_id' => $this->school->id, 'email_verified_at' => now()]);
        $this->director->assignRole('director');
    }

    public function test_dashboard_ejecutivo_muestra_kpis_y_graficas(): void
    {
        Alumno::factory()->count(3)->create(['school_id' => $this->school->id]);

        $this->actingAs($this->director)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Alumnos')
            ->assertSee('Asistencia semanal')
            ->assertSee('Rendimiento por grupo');
    }

    private function registrarBitacora(string $modulo, string $accion = 'crear'): void
    {
        Bitacora::create([
            'school_id' => $this->school->id,
            'user_id' => $this->director->id,
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => "Acción de prueba en {$modulo}",
            'ip' => '127.0.0.1',
            'created_at' => now(),
        ]);
    }

    public function test_bitacora_filtra_por_modulo(): void
    {
        $this->registrarBitacora('alumnos');
        $this->registrarBitacora('asistencias');

        $this->actingAs($this->director)
            ->get(route('bitacora.index', ['modulo' => 'alumnos']))
            ->assertOk()
            ->assertSee('Acción de prueba en alumnos')
            ->assertDontSee('Acción de prueba en asistencias');
    }

    public function test_no_se_ve_bitacora_de_otra_escuela(): void
    {
        $this->registrarBitacora('alumnos');

        $otra = School::factory()->create();
        Bitacora::create([
            'school_id' => $otra->id, 'user_id' => null, 'accion' => 'crear',
            'modulo' => 'alumnos', 'descripcion' => 'Movimiento ajeno', 'ip' => '1.1.1.1', 'created_at' => now(),
        ]);

        $this->actingAs($this->director)
            ->get(route('bitacora.index'))
            ->assertOk()
            ->assertDontSee('Movimiento ajeno');
    }

    public function test_exportaciones_de_bitacora(): void
    {
        $this->registrarBitacora('alumnos');

        $xlsx = $this->actingAs($this->director)->get(route('bitacora.exportar', ['formato' => 'xlsx']));
        $xlsx->assertOk();
        $this->assertStringContainsString('spreadsheetml', (string) $xlsx->headers->get('content-type'));

        $csv = $this->actingAs($this->director)->get(route('bitacora.exportar', ['formato' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('bitacora.csv', (string) $csv->headers->get('content-disposition'));

        $pdf = $this->actingAs($this->director)->get(route('bitacora.exportar', ['formato' => 'pdf']));
        $pdf->assertOk();
        $this->assertSame('application/pdf', $pdf->headers->get('content-type'));
    }
}

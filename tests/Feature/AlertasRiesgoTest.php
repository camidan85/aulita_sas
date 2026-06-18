<?php

namespace Tests\Feature;

use App\Models\AlertaRiesgo;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\School;
use App\Models\Tutor;
use App\Notifications\AlertaRiesgoNotification;
use App\Services\RiesgoService;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AlertasRiesgoTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed(RolesAndPermissionsSeeder::class);
        Carbon::setTestNow(Carbon::parse('2026-06-18 08:00:00', 'UTC'));

        $this->school = School::factory()->create(['timezone' => 'UTC']);
        app(TenantManager::class)->setSchoolId($this->school->id);
        $this->alumno = Alumno::factory()->create(['school_id' => $this->school->id]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function asistencia(string $fecha, string $estatus): void
    {
        Asistencia::factory()->create([
            'school_id' => $this->school->id,
            'alumno_id' => $this->alumno->id,
            'fecha' => $fecha,
            'estatus' => $estatus,
            'origen' => 'automatico',
        ]);
    }

    private function riesgo(): RiesgoService
    {
        return app(RiesgoService::class);
    }

    public function test_tres_faltas_consecutivas_generan_alerta(): void
    {
        $this->asistencia('2026-06-16', 'falta');
        $this->asistencia('2026-06-17', 'falta');
        $this->asistencia('2026-06-18', 'falta_pendiente');

        $this->riesgo()->evaluar($this->alumno);

        $this->assertDatabaseHas('alertas_riesgo', [
            'alumno_id' => $this->alumno->id,
            'tipo' => AlertaRiesgo::TIPO_3_FALTAS,
        ]);
    }

    public function test_cinco_faltas_en_el_mes_generan_alerta(): void
    {
        foreach (['01', '05', '09', '12', '15'] as $dia) {
            $this->asistencia("2026-06-{$dia}", 'falta');
        }

        $this->riesgo()->evaluar($this->alumno);

        $this->assertDatabaseHas('alertas_riesgo', [
            'alumno_id' => $this->alumno->id,
            'tipo' => AlertaRiesgo::TIPO_5_FALTAS_MES,
        ]);
    }

    public function test_diez_retardos_generan_alerta(): void
    {
        for ($d = 1; $d <= 10; $d++) {
            $this->asistencia(Carbon::parse('2026-05-01')->addDays($d)->toDateString(), 'retardo');
        }

        $this->riesgo()->evaluar($this->alumno);

        $this->assertDatabaseHas('alertas_riesgo', [
            'alumno_id' => $this->alumno->id,
            'tipo' => AlertaRiesgo::TIPO_10_RETARDOS,
        ]);
    }

    public function test_no_se_duplica_la_alerta(): void
    {
        $this->asistencia('2026-06-16', 'falta');
        $this->asistencia('2026-06-17', 'falta');
        $this->asistencia('2026-06-18', 'falta');

        $this->riesgo()->evaluar($this->alumno);
        $this->riesgo()->evaluar($this->alumno);

        $this->assertSame(1, AlertaRiesgo::where('tipo', AlertaRiesgo::TIPO_3_FALTAS)->count());
    }

    public function test_la_alerta_se_notifica(): void
    {
        $tutor = Tutor::factory()->create([
            'school_id' => $this->school->id,
            'correo' => 'tutor@demo.test',
        ]);
        $this->alumno->tutores()->attach($tutor->id, ['school_id' => $this->school->id, 'tipo' => 'principal']);

        $this->asistencia('2026-06-16', 'falta');
        $this->asistencia('2026-06-17', 'falta');
        $this->asistencia('2026-06-18', 'falta');

        $this->riesgo()->evaluar($this->alumno);

        Notification::assertSentTo($tutor, AlertaRiesgoNotification::class);
    }
}

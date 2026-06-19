<?php

namespace Tests\Feature;

use App\Events\AsistenciaRegistrada;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\School;
use App\Services\AsistenciaService;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AsistenciaServiceTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake([AsistenciaRegistrada::class]);

        $this->school = School::factory()->create([
            'timezone' => 'UTC',
            'hora_corte_faltas' => '07:15:00',
        ]);
        app(TenantManager::class)->setSchoolId($this->school->id);
        $this->alumno = Alumno::factory()->create(['school_id' => $this->school->id]);
    }

    private function service(): AsistenciaService
    {
        return app(AsistenciaService::class);
    }

    public function test_antes_del_corte_es_presente(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-18 07:00:00', 'UTC'));

        $r = $this->service()->registrar($this->alumno);

        $this->assertSame('presente', $r['asistencia']->estatus);
        $this->assertSame('creado', $r['estado']);
    }

    public function test_despues_del_corte_es_retardo(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-18 07:30:00', 'UTC'));

        $r = $this->service()->registrar($this->alumno);

        $this->assertSame('retardo', $r['asistencia']->estatus);
    }

    public function test_solo_un_registro_por_dia(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-18 07:00:00', 'UTC'));

        $this->service()->registrar($this->alumno);
        $segundo = $this->service()->registrar($this->alumno);

        $this->assertSame('duplicado', $segundo['estado']);
        $this->assertSame(1, Asistencia::count());

        // Un re-escaneo NO debe disparar el evento otra vez → el correo sale 1 sola vez.
        Event::assertDispatchedTimes(AsistenciaRegistrada::class, 1);
    }

    public function test_falta_pendiente_se_convierte_en_retardo(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-18 07:30:00', 'UTC'));

        Asistencia::factory()->create([
            'school_id' => $this->school->id,
            'alumno_id' => $this->alumno->id,
            'fecha' => '2026-06-18',
            'estatus' => 'falta_pendiente',
            'origen' => 'automatico',
            'hora' => null,
        ]);

        $r = $this->service()->registrar($this->alumno);

        $this->assertSame('actualizado', $r['estado']);
        $this->assertSame('retardo', $r['asistencia']->fresh()->estatus);
        $this->assertSame(1, Asistencia::count());
    }

    public function test_se_dispara_el_evento(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-18 07:00:00', 'UTC'));

        $this->service()->registrar($this->alumno);

        Event::assertDispatched(AsistenciaRegistrada::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\FirmaEnterado;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Reporte;
use App\Models\School;
use App\Models\Tutor;
use App\Models\User;
use App\Notifications\AvisoNotification;
use App\Services\AvisoService;
use App\Services\FirmaService;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AvisoFirmaTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);
    }

    public function test_aviso_de_grupo_notifica_a_los_tutores_del_grupo(): void
    {
        $ciclo = CicloEscolar::factory()->create(['school_id' => $this->school->id]);
        $grado = Grado::factory()->create(['school_id' => $this->school->id, 'nivel' => 1]);
        $grupo = Grupo::factory()->create(['school_id' => $this->school->id, 'grado_id' => $grado->id, 'ciclo_id' => $ciclo->id]);

        $alumnoDentro = Alumno::factory()->create(['school_id' => $this->school->id, 'grupo_id' => $grupo->id]);
        $tutorDentro = Tutor::factory()->create(['school_id' => $this->school->id, 'correo' => 'in@demo.test']);
        $alumnoDentro->tutores()->attach($tutorDentro->id, ['school_id' => $this->school->id, 'tipo' => 'principal']);

        $alumnoFuera = Alumno::factory()->create(['school_id' => $this->school->id]); // sin grupo
        $tutorFuera = Tutor::factory()->create(['school_id' => $this->school->id, 'correo' => 'out@demo.test']);
        $alumnoFuera->tutores()->attach($tutorFuera->id, ['school_id' => $this->school->id, 'tipo' => 'principal']);

        $publicador = User::factory()->create(['school_id' => $this->school->id]);

        app(AvisoService::class)->publicar([
            'titulo' => 'Junta de grupo',
            'contenido' => 'Los esperamos.',
            'alcance' => 'grupo',
            'target_id' => $grupo->id,
            'requiere_firma' => true,
            'publicado_por' => $publicador->id,
            'fecha_publicacion' => now(),
        ]);

        Notification::assertSentTo($tutorDentro, AvisoNotification::class);
        Notification::assertNotSentTo($tutorFuera, AvisoNotification::class);
    }

    public function test_firma_de_enterado_es_idempotente_y_guarda_ip(): void
    {
        $alumno = Alumno::factory()->create(['school_id' => $this->school->id]);
        $profesor = User::factory()->create(['school_id' => $this->school->id]);
        $padre = User::factory()->create(['school_id' => $this->school->id]);

        $reporte = Reporte::factory()->create([
            'school_id' => $this->school->id,
            'alumno_id' => $alumno->id,
            'profesor_id' => $profesor->id,
            'requiere_firma' => true,
        ]);

        $service = app(FirmaService::class);
        $service->firmar($reporte, $padre, '10.0.0.1');
        $service->firmar($reporte, $padre, '10.0.0.1');

        $this->assertSame(1, FirmaEnterado::count());
        $this->assertDatabaseHas('firmas_enterado', [
            'firmable_id' => $reporte->id,
            'user_id' => $padre->id,
            'ip' => '10.0.0.1',
        ]);
        $this->assertTrue($reporte->firmadoPor($padre->id));
    }
}

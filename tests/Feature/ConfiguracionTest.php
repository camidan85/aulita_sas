<?php

namespace Tests\Feature;

use App\Imports\AlumnosImport;
use App\Models\Alumno;
use App\Models\School;
use App\Models\User;
use App\Services\QrTokenService;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ConfiguracionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function escuela(array $attrs = []): School
    {
        $school = School::factory()->create($attrs);
        app(TenantManager::class)->setSchoolId($school->id);

        return $school;
    }

    public function test_el_codigo_qr_se_genera_segun_la_plantilla_de_la_escuela(): void
    {
        $school = $this->escuela(['qr_formato' => 'ESC-{matricula}']);
        $alumno = Alumno::factory()->create(['school_id' => $school->id, 'matricula' => 'A1']);

        $this->assertSame('ESC-A1', $alumno->fresh()->codigo_qr);
    }

    public function test_cambiar_la_plantilla_regenera_los_codigos(): void
    {
        $school = $this->escuela(['qr_formato' => '{matricula}']);
        $alumno = Alumno::factory()->create(['school_id' => $school->id, 'matricula' => 'B2']);
        $this->assertSame('B2', $alumno->fresh()->codigo_qr);

        $school->update(['qr_formato' => 'X-{matricula}']);
        app(QrTokenService::class)->regenerarParaEscuela($school->fresh());

        $this->assertSame('X-B2', $alumno->fresh()->codigo_qr);
    }

    public function test_importacion_de_alumnos_crea_registros(): void
    {
        $school = $this->escuela();

        $import = new AlumnosImport;
        $import->collection(new Collection([
            ['matricula' => 'IMP1', 'nombre' => 'Ana', 'apellido_paterno' => 'Gómez', 'apellido_materno' => 'Ruiz', 'curp' => 'GOMA100101MDFXXX01'],
            ['matricula' => '', 'nombre' => 'SinDatos', 'apellido_paterno' => '', 'curp' => ''], // omitida
        ]));

        $this->assertSame(1, $import->importados);
        $this->assertSame(1, $import->omitidos);
        $this->assertDatabaseHas('alumnos', ['matricula' => 'IMP1', 'school_id' => $school->id]);
    }

    public function test_un_modulo_oculto_bloquea_su_ruta(): void
    {
        $school = $this->escuela(['modulos_ocultos' => ['calificaciones']]);
        $director = User::factory()->create(['school_id' => $school->id, 'email_verified_at' => now()]);
        $director->assignRole('director');

        $this->actingAs($director)->get(route('calificaciones.index'))->assertNotFound();
        // Un módulo activo sí responde.
        $this->actingAs($director)->get(route('reportes.index'))->assertOk();
    }

    public function test_super_admin_gestiona_escuelas_y_modulos(): void
    {
        $super = User::factory()->create(['school_id' => null, 'email_verified_at' => now()]);
        $super->assignRole('super_admin');

        $this->actingAs($super)->get(route('admin.escuelas.index'))->assertOk();

        $this->actingAs($super)->post(route('admin.escuelas.store'), [
            'nombre' => 'Colegio Nuevo',
            'hora_corte_faltas' => '07:30',
            'timezone' => 'America/Mexico_City',
            'umbral_riesgo_calif' => '6.00',
            'qr_formato' => '{curp}',
            'estatus' => 'activa',
            'modulos' => ['asistencia' => '1', 'calificaciones' => '1'], // solo estos activos
        ])->assertRedirect(route('admin.escuelas.index'));

        $escuela = School::where('nombre', 'Colegio Nuevo')->first();
        $this->assertNotNull($escuela);
        $this->assertSame('{curp}', $escuela->qr_formato);
        $this->assertContains('reportes', $escuela->modulos_ocultos); // no marcado = oculto
        $this->assertNotContains('asistencia', $escuela->modulos_ocultos);
    }
}

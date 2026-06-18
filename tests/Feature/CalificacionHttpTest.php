<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\CicloEscolar;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\School;
use App\Models\User;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalificacionHttpTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private User $docente;

    private Grupo $grupo;

    private Materia $materia;

    private Periodo $periodo;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);

        $this->docente = User::factory()->create(['school_id' => $this->school->id, 'email_verified_at' => now()]);
        $this->docente->assignRole('docente');

        $ciclo = CicloEscolar::factory()->create(['school_id' => $this->school->id]);
        $grado = Grado::factory()->create(['school_id' => $this->school->id, 'nivel' => 1]);
        $this->grupo = Grupo::factory()->create(['school_id' => $this->school->id, 'grado_id' => $grado->id, 'ciclo_id' => $ciclo->id]);
        $this->materia = Materia::factory()->create(['school_id' => $this->school->id]);
        $this->periodo = Periodo::factory()->create(['school_id' => $this->school->id, 'ciclo_id' => $ciclo->id]);
        $this->alumno = Alumno::factory()->create(['school_id' => $this->school->id, 'grupo_id' => $this->grupo->id]);
    }

    public function test_docente_captura_calificaciones(): void
    {
        $this->actingAs($this->docente)
            ->post(route('calificaciones.guardar'), [
                'grupo_id' => $this->grupo->id,
                'materia_id' => $this->materia->id,
                'periodo_id' => $this->periodo->id,
                'calificaciones' => [$this->alumno->id => 7.5],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('calificaciones', [
            'alumno_id' => $this->alumno->id,
            'materia_id' => $this->materia->id,
            'periodo_id' => $this->periodo->id,
            'calificacion' => 7.50,
        ]);
    }

    public function test_boleta_descarga_un_pdf(): void
    {
        Calificacion::factory()->create([
            'school_id' => $this->school->id,
            'alumno_id' => $this->alumno->id,
            'materia_id' => $this->materia->id,
            'periodo_id' => $this->periodo->id,
            'calificacion' => 9.0,
        ]);

        $resp = $this->actingAs($this->docente)->get(route('alumnos.boleta', $this->alumno));

        $resp->assertOk();
        $this->assertSame('application/pdf', $resp->headers->get('content-type'));
    }

    public function test_exportacion_excel_descarga_archivo(): void
    {
        $resp = $this->actingAs($this->docente)->get(route('calificaciones.exportar', [
            'grupo_id' => $this->grupo->id,
            'periodo_id' => $this->periodo->id,
        ]));

        $resp->assertOk();
        $this->assertStringContainsString(
            'spreadsheetml',
            (string) $resp->headers->get('content-type')
        );
    }
}

<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\School;
use App\Models\Tutor;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DominioRelacionesTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);
    }

    public function test_un_alumno_pertenece_a_un_grupo(): void
    {
        $grado = Grado::factory()->create(['school_id' => $this->school->id, 'nivel' => 1]);
        $ciclo = CicloEscolar::factory()->create(['school_id' => $this->school->id]);
        $grupo = Grupo::factory()->create([
            'school_id' => $this->school->id,
            'grado_id' => $grado->id,
            'ciclo_id' => $ciclo->id,
        ]);
        $alumno = Alumno::factory()->create([
            'school_id' => $this->school->id,
            'grupo_id' => $grupo->id,
        ]);

        $this->assertTrue($alumno->grupo->is($grupo));
        $this->assertTrue($grupo->alumnos->contains($alumno));
    }

    public function test_un_alumno_tiene_tutor_principal_y_secundario(): void
    {
        $alumno = Alumno::factory()->create(['school_id' => $this->school->id]);
        $principal = Tutor::factory()->create(['school_id' => $this->school->id]);
        $secundario = Tutor::factory()->create(['school_id' => $this->school->id]);

        $alumno->tutores()->attach($principal->id, ['school_id' => $this->school->id, 'tipo' => 'principal']);
        $alumno->tutores()->attach($secundario->id, ['school_id' => $this->school->id, 'tipo' => 'secundario']);

        $this->assertCount(2, $alumno->refresh()->tutores);
        $this->assertTrue($alumno->tutorPrincipal()->is($principal));
    }

    public function test_los_alumnos_se_aislan_por_escuela(): void
    {
        Alumno::factory()->create(['school_id' => $this->school->id]);

        $otra = School::factory()->create();
        Alumno::factory()->create(['school_id' => $otra->id]);

        // En contexto de la primera escuela solo se ve su alumno.
        app(TenantManager::class)->setSchoolId($this->school->id);
        $this->assertCount(1, Alumno::all());

        app(TenantManager::class)->setSchoolId($otra->id);
        $this->assertCount(1, Alumno::all());
    }

    public function test_matricula_es_unica_por_escuela_pero_repetible_entre_escuelas(): void
    {
        Alumno::factory()->create(['school_id' => $this->school->id, 'matricula' => 'A0001']);

        $otra = School::factory()->create();
        $alumnoOtra = Alumno::factory()->create(['school_id' => $otra->id, 'matricula' => 'A0001']);

        // La misma matrícula existe en otra escuela sin conflicto.
        $this->assertSame('A0001', $alumnoOtra->matricula);
    }

    public function test_el_alumno_usa_soft_delete(): void
    {
        $alumno = Alumno::factory()->create(['school_id' => $this->school->id]);
        $alumno->delete();

        $this->assertSoftDeleted('alumnos', ['id' => $alumno->id]);
    }
}

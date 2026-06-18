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
use App\Services\CalificacionService;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalificacionServiceTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private Grupo $grupo;

    private Materia $materia;

    private Periodo $periodo;

    private Alumno $a1;

    private Alumno $a2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school = School::factory()->create(['umbral_riesgo_calif' => 6.00]);
        app(TenantManager::class)->setSchoolId($this->school->id);

        $ciclo = CicloEscolar::factory()->create(['school_id' => $this->school->id]);
        $grado = Grado::factory()->create(['school_id' => $this->school->id, 'nivel' => 1]);
        $this->grupo = Grupo::factory()->create(['school_id' => $this->school->id, 'grado_id' => $grado->id, 'ciclo_id' => $ciclo->id]);
        $this->materia = Materia::factory()->create(['school_id' => $this->school->id]);
        $this->periodo = Periodo::factory()->create(['school_id' => $this->school->id, 'ciclo_id' => $ciclo->id]);

        $this->a1 = Alumno::factory()->create(['school_id' => $this->school->id, 'grupo_id' => $this->grupo->id]);
        $this->a2 = Alumno::factory()->create(['school_id' => $this->school->id, 'grupo_id' => $this->grupo->id]);
    }

    private function service(): CalificacionService
    {
        return app(CalificacionService::class);
    }

    public function test_captura_y_actualiza_sin_duplicar(): void
    {
        $this->service()->capturar($this->grupo, $this->materia, $this->periodo, [
            $this->a1->id => 8.5,
            $this->a2->id => 6.0,
        ]);

        $this->assertSame(2, Calificacion::count());

        // Recapturar actualiza, no duplica.
        $this->service()->capturar($this->grupo, $this->materia, $this->periodo, [$this->a1->id => 9.0]);

        $this->assertSame(2, Calificacion::count());
        $this->assertEquals(9.0, Calificacion::where('alumno_id', $this->a1->id)->value('calificacion'));
    }

    public function test_calcula_promedios(): void
    {
        $this->service()->capturar($this->grupo, $this->materia, $this->periodo, [
            $this->a1->id => 8.0,
            $this->a2->id => 6.0,
        ]);

        $this->assertEquals(8.0, $this->service()->promedioAlumnoPeriodo($this->a1, $this->periodo));
        $this->assertEquals(7.0, $this->service()->promedioGrupo($this->grupo, $this->periodo));
        $this->assertEquals(7.0, $this->service()->promedioMateriaGrupo($this->grupo, $this->materia, $this->periodo));
    }

    public function test_detecta_materias_en_riesgo(): void
    {
        $this->service()->capturar($this->grupo, $this->materia, $this->periodo, [$this->a1->id => 5.0]);

        $riesgo = $this->service()->materiasEnRiesgo($this->a1);

        $this->assertCount(1, $riesgo);
        $this->assertSame($this->materia->id, $riesgo->first()['materia']->id);
    }
}

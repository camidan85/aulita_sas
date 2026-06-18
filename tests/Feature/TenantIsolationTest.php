<?php

namespace Tests\Feature;

use App\Models\CicloEscolar;
use App\Models\School;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function tenant(): TenantManager
    {
        return app(TenantManager::class);
    }

    private function nuevoCiclo(string $nombre = '2025-2026'): CicloEscolar
    {
        return CicloEscolar::create([
            'nombre' => $nombre,
            'fecha_inicio' => '2025-08-01',
            'fecha_fin' => '2026-07-15',
            'vigente' => true,
        ]);
    }

    public function test_school_id_se_asigna_automaticamente_al_crear(): void
    {
        $school = School::factory()->create();
        $this->tenant()->setSchoolId($school->id);

        $ciclo = $this->nuevoCiclo();

        $this->assertSame($school->id, $ciclo->school_id);
    }

    public function test_las_consultas_se_filtran_por_tenant(): void
    {
        $a = School::factory()->create();
        $b = School::factory()->create();

        $this->tenant()->setSchoolId($a->id);
        $this->nuevoCiclo();

        $this->tenant()->setSchoolId($b->id);
        $this->nuevoCiclo();

        // En el contexto de A solo se ve el ciclo de A (RN-T01).
        $this->tenant()->setSchoolId($a->id);
        $this->assertCount(1, CicloEscolar::all());
        $this->assertSame($a->id, CicloEscolar::first()->school_id);

        // En el contexto de B, solo el de B.
        $this->tenant()->setSchoolId($b->id);
        $this->assertCount(1, CicloEscolar::all());
        $this->assertSame($b->id, CicloEscolar::first()->school_id);
    }

    public function test_no_se_puede_leer_un_registro_de_otra_escuela(): void
    {
        $a = School::factory()->create();
        $b = School::factory()->create();

        $this->tenant()->setSchoolId($a->id);
        $cicloA = $this->nuevoCiclo();

        // Desde B, ese id no es accesible.
        $this->tenant()->setSchoolId($b->id);
        $this->assertNull(CicloEscolar::find($cicloA->id));
    }

    public function test_bypass_ve_todas_las_escuelas(): void
    {
        $a = School::factory()->create();
        $b = School::factory()->create();

        $this->tenant()->setSchoolId($a->id);
        $this->nuevoCiclo();
        $this->tenant()->setSchoolId($b->id);
        $this->nuevoCiclo();

        $total = $this->tenant()->bypass(fn () => CicloEscolar::count());

        $this->assertSame(2, $total);
    }

    public function test_la_creacion_de_un_ciclo_queda_en_bitacora(): void
    {
        $school = School::factory()->create();
        $this->tenant()->setSchoolId($school->id);

        $ciclo = $this->nuevoCiclo();

        $this->assertDatabaseHas('bitacora', [
            'accion' => 'crear',
            'modulo' => 'ciclos_escolares',
            'model_id' => $ciclo->id,
            'school_id' => $school->id,
        ]);
    }
}

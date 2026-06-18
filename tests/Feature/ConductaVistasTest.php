<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Aviso;
use App\Models\Reporte;
use App\Models\School;
use App\Models\User;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConductaVistasTest extends TestCase
{
    use RefreshDatabase;

    private User $director;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);

        $this->director = User::factory()->create(['school_id' => $this->school->id, 'email_verified_at' => now()]);
        $this->director->assignRole('director');
    }

    public function test_vistas_de_reportes_y_avisos_renderizan(): void
    {
        $alumno = Alumno::factory()->create(['school_id' => $this->school->id]);

        $reporte = Reporte::factory()->create([
            'school_id' => $this->school->id,
            'alumno_id' => $alumno->id,
            'profesor_id' => $this->director->id,
        ]);

        $aviso = Aviso::factory()->create([
            'school_id' => $this->school->id,
            'publicado_por' => $this->director->id,
        ]);

        foreach ([
            route('reportes.index'),
            route('reportes.create'),
            route('reportes.show', $reporte),
            route('avisos.index'),
            route('avisos.create'),
            route('avisos.show', $aviso),
        ] as $url) {
            $this->actingAs($this->director)->get($url)->assertOk();
        }
    }
}

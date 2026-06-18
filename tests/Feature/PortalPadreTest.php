<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Cita;
use App\Models\Reporte;
use App\Models\School;
use App\Models\Tutor;
use App\Models\User;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalPadreTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private User $padre;

    private Alumno $hijo;

    private Alumno $ajeno;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);

        $this->padre = User::factory()->create(['school_id' => $this->school->id, 'email_verified_at' => now()]);
        $this->padre->assignRole('padre');

        $this->hijo = Alumno::factory()->create(['school_id' => $this->school->id, 'nombre' => 'HijoPropio']);
        $this->ajeno = Alumno::factory()->create(['school_id' => $this->school->id, 'nombre' => 'AlumnoAjeno']);

        $tutor = Tutor::factory()->create(['school_id' => $this->school->id, 'user_id' => $this->padre->id]);
        $this->hijo->tutores()->attach($tutor->id, ['school_id' => $this->school->id, 'tipo' => 'principal']);
    }

    public function test_el_padre_solo_ve_a_sus_hijos(): void
    {
        $this->actingAs($this->padre)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('HijoPropio')
            ->assertDontSee('AlumnoAjeno');
    }

    public function test_el_padre_solo_firma_reportes_de_sus_hijos(): void
    {
        $profesor = User::factory()->create(['school_id' => $this->school->id]);

        $reporteHijo = Reporte::factory()->create([
            'school_id' => $this->school->id, 'alumno_id' => $this->hijo->id,
            'profesor_id' => $profesor->id, 'requiere_firma' => true,
        ]);
        $reporteAjeno = Reporte::factory()->create([
            'school_id' => $this->school->id, 'alumno_id' => $this->ajeno->id,
            'profesor_id' => $profesor->id, 'requiere_firma' => true,
        ]);

        $this->actingAs($this->padre)->patch(route('reportes.firmar', $reporteHijo))->assertRedirect();
        $this->assertTrue($reporteHijo->firmadoPor($this->padre->id));

        $this->actingAs($this->padre)->patch(route('reportes.firmar', $reporteAjeno))->assertForbidden();
        $this->assertFalse($reporteAjeno->firmadoPor($this->padre->id));
    }

    public function test_vistas_de_citas_renderizan(): void
    {
        $this->actingAs($this->padre)->get(route('citas.index'))->assertOk();
        $this->actingAs($this->padre)->get(route('citas.create'))->assertOk();
    }

    public function test_el_padre_solicita_cita_para_su_hijo(): void
    {
        $this->actingAs($this->padre)->post(route('citas.store'), [
            'alumno_id' => $this->hijo->id,
            'con_rol' => 'director',
            'motivo' => 'Seguimiento académico',
            'fecha_solicitada' => now()->addDay()->toDateString(),
        ])->assertRedirect(route('citas.index'));

        $this->assertDatabaseHas('citas', [
            'alumno_id' => $this->hijo->id,
            'solicitante_user_id' => $this->padre->id,
            'estatus' => 'solicitada',
        ]);

        // No puede pedir cita para un alumno ajeno.
        $this->actingAs($this->padre)->post(route('citas.store'), [
            'alumno_id' => $this->ajeno->id,
            'con_rol' => 'director',
            'motivo' => 'x',
            'fecha_solicitada' => now()->addDay()->toDateString(),
        ])->assertForbidden();

        $this->assertSame(1, Cita::count());
    }
}

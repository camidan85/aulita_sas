<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\School;
use App\Models\Tutor;
use App\Notifications\AusenciaNotification;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FaltasAutomaticasTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed(RolesAndPermissionsSeeder::class);

        Carbon::setTestNow(Carbon::parse('2026-06-18 07:15:00', 'UTC'));

        $this->school = School::factory()->create([
            'timezone' => 'UTC',
            'hora_corte_faltas' => '07:15:00',
        ]);
        app(TenantManager::class)->setSchoolId($this->school->id);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_marca_falta_pendiente_a_quien_no_registro(): void
    {
        $ausente = Alumno::factory()->create(['school_id' => $this->school->id]);
        $presente = Alumno::factory()->create(['school_id' => $this->school->id]);

        Asistencia::factory()->create([
            'school_id' => $this->school->id,
            'alumno_id' => $presente->id,
            'fecha' => '2026-06-18',
            'estatus' => 'presente',
        ]);

        $this->artisan('asistencia:detectar-faltas', ['--school' => $this->school->id, '--force' => true])
            ->assertSuccessful();

        $this->assertDatabaseHas('asistencias', [
            'alumno_id' => $ausente->id,
            'fecha' => '2026-06-18 00:00:00',
            'estatus' => 'falta_pendiente',
            'origen' => 'automatico',
        ]);

        // El presente no recibe una segunda fila.
        $this->assertSame(1, Asistencia::where('alumno_id', $presente->id)->count());
    }

    public function test_no_duplica_si_se_corre_dos_veces(): void
    {
        Alumno::factory()->create(['school_id' => $this->school->id]);

        $this->artisan('asistencia:detectar-faltas', ['--school' => $this->school->id, '--force' => true]);
        $this->artisan('asistencia:detectar-faltas', ['--school' => $this->school->id, '--force' => true]);

        $this->assertSame(1, Asistencia::where('estatus', 'falta_pendiente')->count());
    }

    public function test_notifica_la_ausencia_al_tutor(): void
    {
        $alumno = Alumno::factory()->create(['school_id' => $this->school->id]);
        $tutor = Tutor::factory()->create([
            'school_id' => $this->school->id,
            'correo' => 'tutor@demo.test',
            'telefono' => '5215512345678',
        ]);
        $alumno->tutores()->attach($tutor->id, ['school_id' => $this->school->id, 'tipo' => 'principal']);

        $this->artisan('asistencia:detectar-faltas', ['--school' => $this->school->id, '--force' => true]);

        Notification::assertSentTo($tutor, AusenciaNotification::class);
    }
}

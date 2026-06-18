<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Reporte;
use App\Models\School;
use App\Models\Tutor;
use App\Models\User;
use App\Notifications\ReporteNotification;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConductaTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private User $docente;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('local');
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);

        $this->docente = User::factory()->create(['school_id' => $this->school->id, 'email_verified_at' => now()]);
        $this->docente->assignRole('docente');

        $this->alumno = Alumno::factory()->create(['school_id' => $this->school->id]);
        $tutor = Tutor::factory()->create(['school_id' => $this->school->id, 'correo' => 't@demo.test']);
        $this->alumno->tutores()->attach($tutor->id, ['school_id' => $this->school->id, 'tipo' => 'principal']);
    }

    public function test_docente_crea_reporte_con_evidencia_y_notifica(): void
    {
        $this->actingAs($this->docente)
            ->post(route('reportes.store'), [
                'alumno_id' => $this->alumno->id,
                'tipo' => 'mala_conducta',
                'descripcion' => 'Llegó tarde repetidamente.',
                'requiere_firma' => '1',
                'evidencias' => [UploadedFile::fake()->image('evidencia.jpg')],
            ])
            ->assertRedirect();

        $reporte = Reporte::first();
        $this->assertNotNull($reporte);
        $this->assertSame($this->docente->id, $reporte->profesor_id);

        $this->assertDatabaseHas('evidencias', [
            'reporte_id' => $reporte->id,
            'tipo' => 'imagen',
        ]);

        Storage::disk('local')->assertExists($reporte->evidencias->first()->path);

        Notification::assertSentTo(
            $this->alumno->tutores->first(),
            ReporteNotification::class
        );
    }

    public function test_descarga_de_evidencia(): void
    {
        $this->actingAs($this->docente)->post(route('reportes.store'), [
            'alumno_id' => $this->alumno->id,
            'tipo' => 'mala_conducta',
            'descripcion' => 'Detalle',
            'evidencias' => [UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')],
        ]);

        $evidencia = Reporte::first()->evidencias->first();

        $this->actingAs($this->docente)
            ->get(route('evidencias.descargar', $evidencia))
            ->assertOk();
    }
}

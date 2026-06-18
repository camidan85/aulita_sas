<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\School;
use App\Models\Tutor;
use App\Models\User;
use App\Notifications\AsistenciaRegistradaNotification;
use App\Notifications\Channels\WhatsAppChannel;
use App\Services\AsistenciaService;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AsistenciaNotificacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifica_a_tutores_y_administrativo_por_correo_y_whatsapp(): void
    {
        Notification::fake();
        $this->seed(RolesAndPermissionsSeeder::class);

        $school = School::factory()->create(['timezone' => 'UTC', 'hora_corte_faltas' => '07:15:00']);
        app(TenantManager::class)->setSchoolId($school->id);

        $alumno = Alumno::factory()->create(['school_id' => $school->id]);

        $tutor = Tutor::factory()->create([
            'school_id' => $school->id,
            'correo' => 'tutor@demo.test',
            'telefono' => '5215512345678',
        ]);
        $alumno->tutores()->attach($tutor->id, ['school_id' => $school->id, 'tipo' => 'principal']);

        $admin = User::factory()->create(['school_id' => $school->id]);
        $admin->assignRole('administrativo');

        app(AsistenciaService::class)->registrar($alumno);

        // El tutor recibe por correo Y WhatsApp.
        Notification::assertSentTo($tutor, AsistenciaRegistradaNotification::class, function ($n, $channels) {
            return in_array('mail', $channels, true) && in_array(WhatsAppChannel::class, $channels, true);
        });

        // El administrativo recibe (al menos correo).
        Notification::assertSentTo($admin, AsistenciaRegistradaNotification::class);
    }
}

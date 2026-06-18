<?php

namespace Tests\Feature;

use App\Models\AccountActivation;
use App\Models\Alumno;
use App\Models\School;
use App\Models\User;
use App\Tenancy\TenantManager;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActivacionPortalTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private Alumno $alumno;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->school = School::factory()->create();
        app(TenantManager::class)->setSchoolId($this->school->id);

        $this->alumno = Alumno::factory()->create([
            'school_id' => $this->school->id,
            'curp' => 'PELJ100101HDFXXX01',
            'apellido_paterno' => 'Pérez',
        ]);
    }

    public function test_flujo_de_activacion_crea_padre_y_vincula_hijo(): void
    {
        $this->post(route('activar.enviar'), [
            'curp' => 'PELJ100101HDFXXX01',
            'apellido_paterno' => 'Pérez',
            'nombre' => 'Mamá de Juan',
            'correo' => 'mama@demo.test',
            'telefono' => '5215512345678',
        ])->assertOk();

        $activation = AccountActivation::first();
        $this->assertNotNull($activation);
        $this->assertNull($activation->used_at);

        // Crear contraseña con el token.
        $this->get(route('activar.crear', $activation->token))->assertOk();

        $this->post(route('activar.guardar', $activation->token), [
            'password' => 'Password#1',
            'password_confirmation' => 'Password#1',
        ])->assertRedirect(route('portal.dashboard'));

        $padre = User::where('email', 'mama@demo.test')->first();
        $this->assertNotNull($padre);
        $this->assertTrue($padre->hasRole('padre'));
        $this->assertTrue($padre->esHijo($this->alumno->id));
        $this->assertNotNull($activation->fresh()->used_at);
    }

    public function test_datos_incorrectos_no_crean_activacion(): void
    {
        $this->post(route('activar.enviar'), [
            'curp' => 'XXXX000000XXXXXX00',
            'apellido_paterno' => 'Inexistente',
            'nombre' => 'Nadie',
            'correo' => 'nadie@demo.test',
        ])->assertSessionHasErrors('curp');

        $this->assertSame(0, AccountActivation::count());
    }

    public function test_enlace_expirado_es_rechazado(): void
    {
        $activation = AccountActivation::create([
            'school_id' => $this->school->id,
            'alumno_id' => $this->alumno->id,
            'curp' => $this->alumno->curp,
            'apellido_paterno' => 'Pérez',
            'nombre' => 'Papá',
            'correo' => 'papa@demo.test',
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->subHour(),
        ]);

        $this->get(route('activar.crear', $activation->token))->assertStatus(410);
    }
}

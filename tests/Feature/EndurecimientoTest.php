<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndurecimientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_respuestas_incluyen_cabeceras_de_seguridad(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_la_activacion_tiene_rate_limit(): void
    {
        $payload = [
            'curp' => 'XXXX000000XXXXXX00',
            'apellido_paterno' => 'Inexistente',
            'nombre' => 'Nadie',
            'correo' => 'nadie@demo.test',
        ];

        // El límite es 6/min por IP.
        for ($i = 0; $i < 6; $i++) {
            $this->post(route('activar.enviar'), $payload)->assertStatus(302);
        }

        $this->post(route('activar.enviar'), $payload)->assertStatus(429);
    }
}

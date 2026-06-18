<?php

namespace App\Observers;

use App\Models\Alumno;
use App\Services\QrTokenService;

class AlumnoObserver
{
    /**
     * Tras guardar, recalcula el codigo_qr según la plantilla de la escuela.
     * Usa saveQuietly internamente, así que no provoca recursión.
     */
    public function saved(Alumno $alumno): void
    {
        app(QrTokenService::class)->asignar($alumno);
    }
}

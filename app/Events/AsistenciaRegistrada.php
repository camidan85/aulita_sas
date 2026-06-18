<?php

namespace App\Events;

use App\Models\Asistencia;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AsistenciaRegistrada
{
    use Dispatchable, SerializesModels;

    public function __construct(public Asistencia $asistencia) {}
}

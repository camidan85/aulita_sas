<?php

namespace App\Listeners;

use App\Events\AsistenciaRegistrada;
use App\Notifications\AsistenciaRegistradaNotification;
use App\Support\DestinatariosEscolares;
use Illuminate\Support\Facades\Notification;

/**
 * Al registrarse una asistencia, notifica a los tutores (principal y secundario)
 * y al personal administrativo de la escuela (RN-N01). Las notificaciones se
 * encolan; un fallo de WhatsApp no bloquea el correo (RN-N03).
 */
class NotificarAsistenciaTutores
{
    public function handle(AsistenciaRegistrada $event): void
    {
        $asistencia = $event->asistencia;

        $destinatarios = DestinatariosEscolares::tutoresYAdministrativos($asistencia->alumno);

        if (empty($destinatarios)) {
            return;
        }

        Notification::send($destinatarios, new AsistenciaRegistradaNotification($asistencia));
    }
}

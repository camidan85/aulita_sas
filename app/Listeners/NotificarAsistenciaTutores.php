<?php

namespace App\Listeners;

use App\Events\AsistenciaRegistrada;
use App\Notifications\AsistenciaRegistradaNotification;
use App\Support\DestinatariosEscolares;
use Illuminate\Support\Facades\Notification;

/**
 * Al registrarse una asistencia, notifica SOLO a los tutores (principal y
 * secundario). El administrativo no recibe un correo por cada asistencia para
 * no saturarlo; sí recibe las ausencias y alertas de riesgo. Las notificaciones
 * se encolan; un fallo de WhatsApp no bloquea el correo (RN-N03).
 */
class NotificarAsistenciaTutores
{
    public function handle(AsistenciaRegistrada $event): void
    {
        $asistencia = $event->asistencia;

        $destinatarios = DestinatariosEscolares::tutores($asistencia->alumno);

        if (empty($destinatarios)) {
            return;
        }

        Notification::send($destinatarios, new AsistenciaRegistradaNotification($asistencia));
    }
}

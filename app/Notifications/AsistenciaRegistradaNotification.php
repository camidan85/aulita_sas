<?php

namespace App\Notifications;

use App\Models\Asistencia;
use App\Notifications\Channels\WhatsAppChannel;
use App\Support\Remitente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AsistenciaRegistradaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Asistencia $asistencia) {}

    /**
     * Canales según los datos de contacto disponibles del destinatario.
     */
    public function via(object $notifiable): array
    {
        $canales = [];

        if ($notifiable->routeNotificationFor('mail')) {
            $canales[] = 'mail';
        }

        if ($notifiable->routeNotificationFor('whatsapp')) {
            $canales[] = WhatsAppChannel::class;
        }

        return $canales ?: ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $a = $this->asistencia->loadMissing('alumno.grupo.grado', 'registradoPor');
        $alumno = $a->alumno;

        return (new MailMessage)
            ->from(...Remitente::para($a->school_id))
            ->subject('Registro de asistencia · '.$alumno->nombreCompleto())
            ->greeting('Aviso de asistencia')
            ->line("Alumno: {$alumno->nombreCompleto()}")
            ->line('Grupo: '.($alumno->grupo?->nombreCompleto() ?? '—'))
            ->line('Fecha: '.$a->fecha->format('d/m/Y'))
            ->line('Hora: '.$a->hora)
            ->line('Estatus: '.ucfirst($a->estatus))
            ->line('Registró: '.($a->registradoPor?->name ?? 'Sistema'));
    }

    /**
     * Plantilla aprobada de Meta. El nombre y los parámetros deben coincidir
     * con la plantilla configurada en WhatsApp Business.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $a = $this->asistencia->loadMissing('alumno');

        return [
            'name' => 'aviso_asistencia',
            'language' => ['code' => 'es_MX'],
            'components' => [[
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $a->alumno->nombreCompleto()],
                    ['type' => 'text', 'text' => $a->fecha->format('d/m/Y')],
                    ['type' => 'text', 'text' => (string) $a->hora],
                    ['type' => 'text', 'text' => ucfirst($a->estatus)],
                ],
            ]],
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Asistencia;
use App\Notifications\Channels\WhatsAppChannel;
use App\Support\Remitente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso de ausencia detectada automáticamente a la hora de corte (RN-FA02).
 */
class AusenciaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Asistencia $asistencia) {}

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
        $alumno = $this->asistencia->loadMissing('alumno.grupo.grado')->alumno;

        return (new MailMessage)
            ->from(...Remitente::para($this->asistencia->school_id))
            ->subject('Ausencia registrada · '.$alumno->nombreCompleto())
            ->greeting('Aviso de ausencia')
            ->line("Le informamos que {$alumno->nombreCompleto()} no ha registrado asistencia hoy.")
            ->line('Grupo: '.($alumno->grupo?->nombreCompleto() ?? '—'))
            ->line('Fecha: '.$this->asistencia->fecha->format('d/m/Y'))
            ->line('Si su hijo(a) ingresa más tarde, el registro se actualizará a retardo.')
            ->salutation(Remitente::nombre($this->asistencia->school_id));
    }

    public function toWhatsApp(object $notifiable): array
    {
        $alumno = $this->asistencia->loadMissing('alumno')->alumno;

        return [
            'name' => 'aviso_ausencia',
            'language' => ['code' => 'es_MX'],
            'components' => [[
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $alumno->nombreCompleto()],
                    ['type' => 'text', 'text' => $this->asistencia->fecha->format('d/m/Y')],
                ],
            ]],
        ];
    }
}

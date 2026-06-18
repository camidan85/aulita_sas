<?php

namespace App\Notifications;

use App\Models\AlertaRiesgo;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Alerta de riesgo por patrón de inasistencia/conducta (RN-R01..R04).
 */
class AlertaRiesgoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AlertaRiesgo $alerta) {}

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
        $alumno = $this->alerta->loadMissing('alumno')->alumno;

        return (new MailMessage)
            ->subject('Alerta de riesgo · '.$alumno->nombreCompleto())
            ->greeting('Alerta de riesgo escolar')
            ->line("Se ha generado una alerta para {$alumno->nombreCompleto()}.")
            ->line('Motivo: '.$this->alerta->descripcion())
            ->line($this->alerta->detalle ?? '')
            ->line('Le sugerimos comunicarse con la escuela.');
    }

    public function toWhatsApp(object $notifiable): array
    {
        $alumno = $this->alerta->loadMissing('alumno')->alumno;

        return [
            'name' => 'alerta_riesgo',
            'language' => ['code' => 'es_MX'],
            'components' => [[
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $alumno->nombreCompleto()],
                    ['type' => 'text', 'text' => $this->alerta->descripcion()],
                ],
            ]],
        ];
    }
}

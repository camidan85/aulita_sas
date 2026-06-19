<?php

namespace App\Notifications;

use App\Models\Reporte;
use App\Notifications\Channels\WhatsAppChannel;
use App\Support\Remitente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReporteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reporte $reporte) {}

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
        $r = $this->reporte->loadMissing('alumno');

        $mail = (new MailMessage)
            ->from(...Remitente::para($r->school_id))
            ->subject($r->tipoLabel().' · '.$r->alumno->nombreCompleto())
            ->greeting($r->tipoLabel())
            ->line("Alumno: {$r->alumno->nombreCompleto()}")
            ->line('Fecha: '.$r->fecha->format('d/m/Y'))
            ->line($r->descripcion);

        if ($r->requiere_firma) {
            $mail->line('Este reporte requiere su firma de enterado.');
        }

        return $mail->salutation(Remitente::nombre($r->school_id));
    }

    public function toWhatsApp(object $notifiable): array
    {
        $r = $this->reporte->loadMissing('alumno');

        return [
            'name' => 'aviso_reporte',
            'language' => ['code' => 'es_MX'],
            'components' => [[
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $r->alumno->nombreCompleto()],
                    ['type' => 'text', 'text' => $r->tipoLabel()],
                ],
            ]],
        ];
    }
}

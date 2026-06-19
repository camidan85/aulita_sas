<?php

namespace App\Notifications;

use App\Models\Aviso;
use App\Notifications\Channels\WhatsAppChannel;
use App\Support\Remitente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AvisoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Aviso $aviso) {}

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
        $mail = (new MailMessage)
            ->from(...Remitente::para($this->aviso->school_id))
            ->subject('Aviso: '.$this->aviso->titulo)
            ->greeting($this->aviso->titulo)
            ->line($this->aviso->contenido);

        if ($this->aviso->requiere_firma) {
            $mail->line('Este aviso requiere su firma de enterado.');
        }

        return $mail;
    }

    public function toWhatsApp(object $notifiable): array
    {
        return [
            'name' => 'aviso_general',
            'language' => ['code' => 'es_MX'],
            'components' => [[
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $this->aviso->titulo],
                ],
            ]],
        ];
    }
}

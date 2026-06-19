<?php

namespace App\Notifications;

use App\Models\AccountActivation;
use App\Support\Remitente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivacionPortalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AccountActivation $activation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('activar.crear', $this->activation->token);

        return (new MailMessage)
            ->from(...Remitente::para($this->activation->school_id))
            ->subject('Activa tu cuenta del portal de padres')
            ->greeting('Bienvenido a AULITA')
            ->line('Para activar tu cuenta y crear tu contraseña, da clic en el botón. El enlace expira en 24 horas.')
            ->action('Activar cuenta', $url)
            ->line('Si no solicitaste esto, ignora este mensaje.');
    }
}

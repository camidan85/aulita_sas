<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

/**
 * Canal de WhatsApp vía Meta Cloud API.
 * Los mensajes los inicia el sistema, por lo que se envían como plantillas (HSM)
 * aprobadas (RN-N02). Si no hay credenciales configuradas, no hace nada (local).
 */
class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $to = $notifiable->routeNotificationFor('whatsapp', $notification);

        if (! $to) {
            return;
        }

        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');

        // Sin credenciales (entorno local): no-op para no romper el flujo.
        if (! $token || ! $phoneId) {
            return;
        }

        $version = config('services.whatsapp.api_version', 'v21.0');

        Http::withToken($token)
            ->post("https://graph.facebook.com/{$version}/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => $notification->toWhatsApp($notifiable),
            ])
            ->throw();
    }
}

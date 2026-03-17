<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(public readonly string $url) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — GRYN')
            ->greeting('Bonjour ' . $notifiable->first_name . ',')
            ->line('Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectuée pour votre compte.')
            ->action('Réinitialiser mon mot de passe', $this->url)
            ->line('Ce lien expire dans ' . config('auth.passwords.users.expire') . ' minutes.')
            ->line('Si vous n\'avez pas effectué cette demande, ignorez cet email.');
    }
}

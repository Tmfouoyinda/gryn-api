<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(public string $url) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->subject('Réinitialisation de mot de passe')
            ->line('Cliquez sur le bouton pour réinitialiser votre mot de passe.')
            ->action('Réinitialiser', $this->url)
            ->line('Ce lien expire dans ' . config('auth.passwords.users.expire') . ' minutes.');
    }
}

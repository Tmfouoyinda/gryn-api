<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message de contact — ' . ($this->data['subject'] ?? 'Sans sujet'),
            replyTo: [$this->data['email']],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'email.contact');
    }

    public function attachments(): array
    {
        return [];
    }
}

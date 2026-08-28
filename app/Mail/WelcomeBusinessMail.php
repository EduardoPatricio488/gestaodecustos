<?php

namespace App\Mail;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeBusinessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Workspace $workspace) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao Comando da '.$this->workspace->name.'! 🏢',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-business', // Vamos criar esta view a seguir
        );
    }
}

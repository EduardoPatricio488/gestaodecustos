<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $name, public $companyName) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acesso Suspenso ⚠️ - ' . $this->companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family: sans-serif; background-color: #fff7ed; padding: 40px; color: #9a3412;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 30px; border: 1px solid #ffedd5;'>
                        <h1 style='color: #9a3412;'>Olá, {$this->name}</h1>
                        <p>Informamos que o teu acesso ao terminal da empresa <strong>{$this->companyName}</strong> foi temporariamente <strong>suspenso</strong> pela administração.</p>
                        <p style='font-size: 13px; color: #7c2d12;'>Esta é uma medida administrativa. Para mais esclarecimentos, contacta os RH.</p>
                    </div>
                </div>
            ",
        );
    }
}

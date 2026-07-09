<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeTerminatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $name, public $companyName) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cessação de Vínculo Contratual 🛑 - ' . $this->companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family: sans-serif; background-color: #fef2f2; padding: 40px; color: #991b1b;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 30px; border: 1px solid #fee2e2;'>
                        <h1 style='color: #991b1b;'>Notificação de Saída</h1>
                        <p>Caro(a) <strong>{$this->name}</strong>, informamos que a tua ligação contratual com a <strong>{$this->companyName}</strong> foi encerrada no sistema nesta data.</p>
                        <p>Agradecemos o teu contributo durante o tempo em que estiveste connosco.</p>
                    </div>
                </div>
            ",
        );
    }
}

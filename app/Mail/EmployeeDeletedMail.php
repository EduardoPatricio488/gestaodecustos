<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $name) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Remoção de Dados de Perfil 🗑️',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family: sans-serif; background-color: #f4f4f5; padding: 40px; color: #18181b;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 30px; border: 1px solid #e4e4e7;'>
                        <h1 style='color: #18181b;'>Dados Removidos</h1>
                        <p>Olá <strong>{$this->name}</strong>,</p>
                        <p>Informamos que, por motivos de gestão de dados ou término de processo, o teu registo de colaborador foi <strong>eliminado permanentemente</strong> do nosso sistema.</p>
                    </div>
                </div>
            ",
        );
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeReactivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $name, public $companyName) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acesso Restabelecido! ✅ - ' . $this->companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family: sans-serif; background-color: #f0fdf4; padding: 40px; color: #166534;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 30px; border: 1px solid #dcfce7;'>
                        <h1 style='color: #166534;'>Olá, {$this->name}!</h1>
                        <p>Boas notícias! O teu acesso ao sistema da empresa <strong>{$this->companyName}</strong> foi <strong>reativado</strong> pela administração.</p>
                        <p>Já podes voltar a aceder ao teu terminal, registar ponto e consultar os teus documentos normalmente.</p>
                        <br>
                        <p style='font-weight: bold; color: #10b981;'>Bom trabalho!</p>
                    </div>
                </div>
            ",
        );
    }
}

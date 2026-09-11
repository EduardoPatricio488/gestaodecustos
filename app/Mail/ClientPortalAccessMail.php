<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientPortalAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public Workspace $workspace,
        public string $token,
        public string $portalUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acesso ao portal de cliente — '.($this->workspace->legal_name ?: $this->workspace->name),
        );
    }

    public function content(): Content
    {
        $companyName = e($this->workspace->legal_name ?: $this->workspace->name);
        $clientName = e($this->client->name);
        $taxNumber = e($this->workspace->tax_number ?? $this->workspace->nif ?? '');
        $token = e($this->token);
        $portalUrl = e($this->portalUrl);

        $companyTaxNumberHtml = $taxNumber !== ''
            ? "<div style='margin-top:8px;font-size:13px;color:#52525b;'>NIF da empresa: <strong style='color:#18181b;'>{$taxNumber}</strong></div>"
            : '';

        return new Content(
            htmlString: "
                <div style='font-family:Arial,Helvetica,sans-serif;background:#f4f4f5;padding:40px;color:#18181b;'>
                    <div style='max-width:620px;margin:0 auto;background:#fff;padding:36px;border-radius:24px;border:1px solid #e4e4e7;'>
                        <div style='font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#059669;'>Finance Pro IA</div>
                        <h1 style='margin:12px 0 8px;font-size:25px;'>O teu acesso ao portal está pronto</h1>
                        <p style='font-size:15px;line-height:1.7;'>Olá <strong>{$clientName}</strong>, a empresa <strong>{$companyName}</strong> disponibilizou-te acesso ao portal de cliente.</p>
                        {$companyTaxNumberHtml}
                        <div style='background:#ecfdf5;border:1px solid #a7f3d0;border-radius:18px;padding:22px;margin:24px 0;'>
                            <div style='font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#047857;'>Código de acesso</div>
                            <div style='font-size:32px;font-weight:900;letter-spacing:6px;color:#065f46;font-family:monospace;margin-top:8px;'>{$token}</div>
                        </div>
                        <p style='font-size:14px;line-height:1.7;'>Utiliza este código no portal para autenticar o teu acesso.</p>
                        <p style='margin:28px 0;'><a href='{$portalUrl}' style='display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:14px 22px;border-radius:12px;font-weight:800;'>Abrir portal de cliente</a></p>
                        <p style='font-size:12px;line-height:1.7;color:#71717a;'>Não partilhes este código com terceiros. Esta é uma mensagem automática enviada pelo Finance Pro IA.</p>
                    </div>
                </div>
            ",
        );
    }
}

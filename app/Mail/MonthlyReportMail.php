<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'O teu Relatório Financeiro Mensal - ' . $this->data['monthName']);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.monthly-report'); // Vamos criar esta vista a seguir
    }

    public function attachments(): array
    {
        // GERA O PDF EM TEMPO REAL E ANEXA
        $pdf = Pdf::loadView('pdf.monthly-report', $this->data);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Relatorio_Mensal.pdf')
                ->withMime('application/pdf'),
        ];
    }
}

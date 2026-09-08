<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CfoReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $analysis, public array $stats) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'O teu Relatório do CFO Inteligente - '.now()->translatedFormat('d M Y'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.cfo-report');
    }
}

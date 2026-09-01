<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'O teu Relatório Financeiro Diário - '.$this->data['date']->format('d/m/Y'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.daily-report');
    }
}

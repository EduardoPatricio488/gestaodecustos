<?php

namespace App\Mail;

use App\Models\PortalAccessRequest;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalAccessRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Workspace $workspace,
        public PortalAccessRequest $request,
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->request->portal_type === 'supplier' ? 'fornecedor' : 'cliente';

        return new Envelope(
            subject: 'Pedido de acesso ao Portal do '.ucfirst($label).' — '.$this->workspace->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails/portal-access-request',
        );
    }
}

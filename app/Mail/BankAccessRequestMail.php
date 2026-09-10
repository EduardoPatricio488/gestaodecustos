<?php

namespace App\Mail;

use App\Models\BankAccessRequest;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BankAccessRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Workspace $workspace,
        public BankAccessRequest $request,
    ) {}

    public function build()
    {
        return $this->subject('Pedido de acesso bancário — '.$this->workspace->name)
            ->view('emails.bank-access-request');
    }
}

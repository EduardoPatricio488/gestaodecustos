<?php

namespace App\Mail;

use App\Models\BankAccessRequest;
use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BankAccessCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Workspace $workspace,
        public BankAccessRequest $request,
        public string $token,
    ) {}

    public function build()
    {
        return $this->subject('Credenciais de acesso bancário — '.$this->workspace->name)
            ->view('emails.bank-access-credentials');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkspaceInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $companyName;
    public $inviteCode;
    public $employeeName;

    public function __construct($companyName, $inviteCode, $employeeName)
    {
        $this->companyName = $companyName;
        $this->inviteCode = $inviteCode;
        $this->employeeName = $employeeName;
    }

    public function build()
    {
        return $this->subject('Chave de Acesso à Empresa 🔑 - ' . $this->companyName)
            ->html("
                <div style='font-family: sans-serif; background-color: #f4f4f5; padding: 40px; color: #18181b;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                        <div style='text-align: center; margin-bottom: 30px;'>
                            <h1 style='font-size: 24px; font-weight: 800; margin: 0;'>Olá, {$this->employeeName}!</h1>
                            <p style='color: #71717a; margin-top: 8px;'>A administração da <strong>{$this->companyName}</strong> enviou-te o convite de acesso.</p>
                        </div>

                        <div style='background: #f1f5f9; padding: 24px; border-radius: 16px; text-align: center; border: 2px dashed #cbd5e1;'>
                            <p style='font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 12px; letter-spacing: 1px;'>Token de Acesso à Empresa</p>
                            <span style='font-family: monospace; font-size: 32px; font-weight: 900; color: #10b981; letter-spacing: 4px;'>{$this->inviteCode}</span>
                        </div>

                        <div style='margin-top: 30px; line-height: 1.6; font-size: 14px;'>
                            <p><strong>Como entrar?</strong></p>
                            <ol>
                                <li>Acede ao <strong>Hub Business</strong> no site.</li>
                                <li>Seleciona a opção <strong>'Sou Colaborador'</strong>.</li>
                                <li>Introduz o token acima para vincular a tua conta.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            ");
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject($this->code . ' é o teu código de verificação - Finance Pro IA')
            ->html("
                <div style='font-family: sans-serif; background-color: #f8fafc; padding: 40px; color: #1e293b;'>
                    <div style='max-width: 400px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;'>

                        <div style='text-align: center; margin-bottom: 25px;'>
                            <h2 style='margin: 0; color: #4f46e5; font-size: 24px; font-weight: 800; letter-spacing: -1px;'>Finance Pro IA</h2>
                            <p style='margin: 5px 0 0; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px;'>Segurança de Protocolo</p>
                        </div>

                        <div style='text-align: center;'>
                            <p style='font-size: 14px; color: #64748b; margin-bottom: 20px;'>Para ativar a tua conta e aceder ao terminal, introduz o código de segurança abaixo:</p>

                            <div style='background: #f1f5f9; padding: 20px; border-radius: 12px; border: 2px dashed #cbd5e1;'>
                                <span style='font-family: monospace; font-size: 32px; font-weight: 900; color: #0f172a; letter-spacing: 8px;'>{$this->code}</span>
                            </div>

                            <p style='font-size: 11px; color: #94a3b8; margin-top: 25px; font-style: italic;'>
                                Se não solicitaste este registo, ignora este email por segurança.
                            </p>
                        </div>
                    </div>

                    <div style='text-align: center; margin-top: 20px; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;'>
                        &copy; " . date('Y') . " Finance Pro IA — Encriptação de Ponta-a-Ponta
                    </div>
                </div>
            ");
    }
}

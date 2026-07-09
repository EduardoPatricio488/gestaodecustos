<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HiredNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $companyName;

    public function __construct($name, $companyName)
    {
        $this->name = $name;
        $this->companyName = $companyName;
    }

    public function build()
    {
        return $this->subject('Bem-vindo(a) à Equipa! 🎉 - ' . $this->companyName)
            ->html("
                <div style='font-family: sans-serif; background-color: #f0fdf4; padding: 40px; color: #1e293b;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid #dcfce7;'>

                        <div style='text-align: center; margin-bottom: 30px;'>
                            <div style='display: inline-block; padding: 15px; background-color: #10b981; border-radius: 20px;'>
                                <span style='font-size: 30px;'>🤝</span>
                            </div>
                            <h1 style='margin: 20px 0 10px; color: #064e3b; font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: -1px;'>Parabéns, {$this->name}!</h1>
                            <p style='margin: 0; font-size: 12px; font-weight: 800; color: #10b981; text-transform: uppercase; letter-spacing: 3px;'>Candidatura Aceite</p>
                        </div>

                        <div style='line-height: 1.6; color: #374151; font-size: 15px;'>
                            <p>É com enorme entusiasmo que informamos que a tua candidatura para a <strong>{$this->companyName}</strong> foi aprovada!</p>
                            <p>Ficámos muito impressionados com o teu perfil e acreditamos que serás uma peça fundamental no nosso crescimento.</p>

                            <div style='margin-top: 30px; padding: 20px; background-color: #f9fafb; border-radius: 15px; border-left: 4px solid #10b981;'>
                                <p style='margin: 0; font-weight: bold; color: #111827;'>Próximos Passos:</p>
                                <p style='margin: 5px 0 0; font-size: 13px;'>O teu gestor entrará em contacto contigo em breve para formalizar o contrato e iniciar o teu Onboarding no sistema.</p>
                            </div>
                        </div>

                        <div style='margin-top: 40px; text-align: center; border-top: 1px solid #f1f5f9; pt: 30px;'>
                            <p style='font-size: 12px; color: #94a3b8;'>Bem-vindo(a) a bordo!</p>
                            <p style='font-weight: 800; color: #064e3b; text-transform: uppercase;'>A Administração, {$this->companyName}</p>
                        </div>
                    </div>
                </div>
            ");
    }
}

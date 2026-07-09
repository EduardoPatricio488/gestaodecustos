<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BusinessPlanActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $companyName;

    public function __construct($userName, $companyName)
    {
        $this->userName = $userName;
        $this->companyName = $companyName;
    }

    public function build()
    {
        return $this->subject('Plano Business Ativado! 💎 - ' . $this->companyName)
            ->html("
                <div style='font-family: sans-serif; background-color: #f5f3ff; padding: 40px; color: #1e1b4b;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 30px; box-shadow: 0 10px 15px rgba(0,0,0,0.05); border: 1px solid #ddd6fe;'>

                        <div style='text-align: center; margin-bottom: 30px;'>
                            <div style='display: inline-block; padding: 15px; background-color: #7c3aed; border-radius: 20px;'>
                                <span style='font-size: 30px;'>💎</span>
                            </div>
                            <h1 style='margin: 20px 0 10px; color: #1e1b4b; font-size: 26px; font-weight: 800; text-transform: uppercase;'>Acesso Premium Ativo</h1>
                            <p style='margin: 0; font-size: 11px; font-weight: 700; color: #7c3aed; text-transform: uppercase; letter-spacing: 2px;'>Finance Pro Business</p>
                        </div>

                        <div style='line-height: 1.6; color: #4b5563; font-size: 15px;'>
                            <p>Olá <strong>{$this->userName}</strong>,</p>
                            <p>Temos o prazer de informar que a administração da <strong>{$this->companyName}</strong> ativou as funcionalidades <strong>Business/Pro</strong> na tua conta.</p>

                            <div style='margin-top: 30px; padding: 25px; background-color: #fdfbff; border-radius: 20px; border: 1px solid #ede9fe;'>
                                <p style='margin: 0 0 15px; font-weight: bold; color: #6d28d9;'>O que tens agora disponível:</p>
                                <ul style='margin: 0; padding-left: 20px; font-size: 13px; color: #5b21b6;'>
                                    <li>Consultoria com <strong>CFO Inteligente (IA)</strong></li>
                                    <li>Módulo de Segurança <strong>Lock In</strong></li>
                                    <li>Gestão Avançada de Inventário</li>
                                    <li>Acesso total ao Hub de Empresas</li>
                                </ul>
                            </div>

                            <p style='margin-top: 30px;'>A tua conta já foi atualizada. Faz login e explora o teu novo terminal.</p>
                        </div>

                        <div style='margin-top: 40px; text-align: center; border-top: 1px solid #f3f4f6; padding-top: 20px;'>
                            <p style='font-size: 10px; color: #94a3b8; text-transform: uppercase;'>Powered by Finance Pro IA</p>
                        </div>
                    </div>
                </div>
            ");
    }
}

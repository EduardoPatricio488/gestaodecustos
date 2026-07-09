<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeDataUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $companyName;

    public function __construct($employee, $companyName)
    {
        $this->employee = $employee;
        $this->companyName = $companyName;
    }

    public function build()
    {
        return $this->subject('Atualização na tua Ficha de Colaborador 👤 - ' . $this->companyName)
            ->html("
                <div style='font-family: sans-serif; background-color: #f8fafc; padding: 40px; color: #1e293b;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 30px; border: 1px solid #e2e8f0; shadow: 0 4px 6px -1px rgba(0,0,0,0.1);'>

                        <div style='text-align: center; margin-bottom: 30px;'>
                            <div style='display: inline-block; padding: 15px; background-color: #3b82f6; border-radius: 20px;'>
                                <span style='font-size: 30px;'>📝</span>
                            </div>
                            <h1 style='margin: 20px 0 10px; color: #1e293b; font-size: 24px; font-weight: 800; text-transform: uppercase;'>Dados Atualizados</h1>
                            <p style='margin: 0; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 2px;'>Notificação de Protocolo RH</p>
                        </div>

                        <div style='line-height: 1.6; color: #475569; font-size: 15px;'>
                            <p>Olá <strong>{$this->employee->name}</strong>,</p>
                            <p>Informamos que a administração da <strong>{$this->companyName}</strong> acabou de atualizar os parâmetros da tua ficha técnica no sistema.</p>

                            <div style='margin-top: 25px; padding: 20px; background-color: #f1f5f9; border-radius: 15px;'>
                                <p style='margin: 0; font-size: 12px; font-weight: bold; color: #64748b; text-transform: uppercase;'>Novos detalhes em vigor:</p>
                                <ul style='margin: 10px 0 0; padding-left: 20px; font-size: 13px;'>
                                    <li><strong>Cargo:</strong> {$this->employee->role}</li>
                                    <li><strong>Dia de Pagamento:</strong> Dia {$this->employee->pay_day}</li>
                                    <li><strong>Vencimento:</strong> " . number_format($this->employee->salary, 2, ',', ' ') . "€</li>
                                </ul>
                            </div>

                            <p style='margin-top: 25px; font-size: 13px;'>Podes consultar todos os detalhes atualizados e o teu histórico no teu painel pessoal em <strong>'A Minha Ficha'</strong>.</p>
                        </div>

                        <div style='margin-top: 40px; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 20px;'>
                            <p style='font-size: 10px; color: #94a3b8; text-transform: uppercase;'>Mensagem automática gerada pelo Finance Pro IA</p>
                        </div>
                    </div>
                </div>
            ");
    }
}

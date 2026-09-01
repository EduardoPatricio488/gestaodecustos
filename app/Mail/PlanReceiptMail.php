<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $planSlug;

    public float $amount;

    public string $invoiceReference;

    public ?string $receiptUrl;

    public function __construct(User $user, string $planSlug, float $amount, string $invoiceReference, ?string $receiptUrl = null)
    {
        $this->user = $user;
        $this->planSlug = $planSlug;
        $this->amount = round($amount, 2);
        $this->invoiceReference = $invoiceReference;
        $this->receiptUrl = $receiptUrl;
    }

    public function build()
    {
        $planName = ucfirst($this->planSlug === 'free' ? 'Free' : $this->planSlug);
        $amountValue = $this->planSlug === 'free' ? 0.0 : $this->amount;
        $amount = number_format($amountValue, 2, ',', '.');
        $appName = config('app.name');
        $paymentText = $this->planSlug === 'free'
            ? 'Confirmamos que o plano <strong>Free</strong> está ativo e o valor desta renovação é <strong>0,00 €</strong>.'
            : 'Confirmamos o pagamento do plano <strong>'.$planName.'</strong> e incluímos este recibo para a sua referência.';

        return $this->subject('Recibo do plano '.$planName.' - '.$appName)
            ->html("
                <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 32px; color: #0f172a;'>
                    <div style='max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden;'>
                        <div style='padding: 28px 32px 18px; background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%); color: #fff;'>
                            <h1 style='margin: 0; font-size: 28px;'>Recibo de pagamento</h1>
                            <p style='margin: 8px 0 0; opacity: 0.85;'>{$appName}</p>
                        </div>

                        <div style='padding: 28px 32px 12px;'>
                            <p>Olá <strong>{$this->user->name}</strong>,</p>
                            <p>{$paymentText}</p>

                            <div style='margin: 24px 0; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;'>
                                <p style='margin: 0 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #475569;'>Detalhes</p>
                                <p style='margin: 0; font-size: 16px;'><strong>Referência:</strong> {$this->invoiceReference}</p>
                                <p style='margin: 8px 0 0; font-size: 16px;'><strong>Valor:</strong> {$amount} €</p>
                                <p style='margin: 8px 0 0; font-size: 16px;'><strong>Plano:</strong> {$planName}</p>
                            </div>

                            <p style='margin: 0; font-size: 14px; line-height: 1.6; color: #475569;'>
                                Este e-mail serve como comprovativo do estado atual do plano e da renovação correspondente.
                            </p>

                            ".($this->receiptUrl ? "<p style='margin-top: 22px;'><a href='{$this->receiptUrl}' style='display:inline-block; padding:12px 18px; background:#2563eb; color:#fff; text-decoration:none; border-radius:10px; font-weight:bold;'>Ver fatura no Stripe</a></p>" : '')."
                        </div>

                        <div style='padding: 0 32px 28px; color: #64748b; font-size: 12px;'>
                            <p style='margin: 0; border-top: 1px solid #e2e8f0; padding-top: 18px;'>Obrigado por usar {$appName}.</p>
                        </div>
                    </div>
                </div>
            ");
    }
}

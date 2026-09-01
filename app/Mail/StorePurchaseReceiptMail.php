<?php

namespace App\Mail;

use App\Models\StorePurchase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class StorePurchaseReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    /** @var Collection<int, StorePurchase> */
    public Collection $purchases;

    public string $reference;

    public function __construct(User $user, Collection $purchases, string $reference)
    {
        $this->user = $user;
        $this->purchases = $purchases;
        $this->reference = $reference;
    }

    public function build()
    {
        $appName = config('app.name');
        $total = number_format((float) $this->purchases->sum('amount_paid'), 2, ',', '.');

        $rows = $this->purchases->map(function (StorePurchase $purchase) {
            $title = e($purchase->product?->title ?? 'Produto');
            $amount = number_format((float) $purchase->amount_paid, 2, ',', '.');

            return "<tr>
                <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0;'>{$title}</td>
                <td style='padding: 10px 0; border-bottom: 1px solid #e2e8f0; text-align: right;'>{$amount} €</td>
            </tr>";
        })->implode('');

        return $this->subject('Recibo da tua compra - '.$appName)
            ->html("
                <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 32px; color: #0f172a;'>
                    <div style='max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden;'>
                        <div style='padding: 28px 32px 18px; background: linear-gradient(135deg, #0f172a 0%, #059669 100%); color: #fff;'>
                            <h1 style='margin: 0; font-size: 28px;'>Recibo de pagamento</h1>
                            <p style='margin: 8px 0 0; opacity: 0.85;'>{$appName} · Loja</p>
                        </div>

                        <div style='padding: 28px 32px 12px;'>
                            <p>Olá <strong>{$this->user->name}</strong>,</p>
                            <p>Obrigado pela tua compra! Confirmamos o pagamento por cartão (Stripe) dos seguintes produtos:</p>

                            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                                {$rows}
                            </table>

                            <div style='margin: 8px 0 24px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;'>
                                <p style='margin: 0 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #475569;'>Detalhes</p>
                                <p style='margin: 0; font-size: 16px;'><strong>Referência:</strong> {$this->reference}</p>
                                <p style='margin: 8px 0 0; font-size: 16px;'><strong>Total pago:</strong> {$total} €</p>
                            </div>

                            <p style='margin: 0; font-size: 14px; line-height: 1.6; color: #475569;'>
                                Os recursos comprados já estão disponíveis no teu inventário na loja.
                            </p>
                        </div>

                        <div style='padding: 0 32px 28px; color: #64748b; font-size: 12px;'>
                            <p style='margin: 0; border-top: 1px solid #e2e8f0; padding-top: 18px;'>Obrigado por usar {$appName}.</p>
                        </div>
                    </div>
                </div>
            ");
    }
}

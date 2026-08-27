<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripeWebhookListener
{
    /**
     * Este código corre quando o Stripe confirma o pagamento.
     * Usamos o client_reference_id que é o ID do nosso utilizador.
     */
    public function handle(WebhookReceived $event)
    {
        $payload = $event->payload;

        // 1. Ouvir apenas o evento de checkout concluído com sucesso
        if ($payload['type'] === 'checkout.session.completed') {

            $session = $payload['data']['object'];

            // Vamos buscar o ID do utilizador que enviámos no checkout
            $userId = $session['client_reference_id'] ?? null;

            // Vamos buscar o valor total pago (em cêntimos: 1000 = 10€)
            $amountPaid = $session['amount_total'] ?? 0;

            if ($userId) {
                $user = User::find($userId);

                if ($user) {
                    // 2. Determinar o plano com base no valor pago
                    // 1000 cêntimos ou mais = Plano Business (pro)
                    // Caso contrário = Plano Premium (plus)
                    $planToActivate = ($amountPaid >= 1000) ? 'pro' : 'plus';

                    // 3. Atualizar o Utilizador
                    $user->update([
                        'plan' => $planToActivate
                    ]);

                    // 4. Atualizar o Workspace atual do utilizador
                    if ($user->currentWorkspace) {
                        $user->currentWorkspace->update([
                            'plan' => $planToActivate
                        ]);
                    }

                    Log::info("✅ PAGAMENTO STRIPE: Plano {$planToActivate} ativado para o utilizador ID {$userId} ({$user->email})");
                } else {
                    Log::error("❌ ERRO WEBHOOK: Pagamento recebido para User ID {$userId}, mas o utilizador não existe na BD.");
                }
            } else {
                Log::warning("⚠️ AVISO WEBHOOK: Checkout concluído mas 'client_reference_id' não encontrado no payload.");
            }
        }
    }
}

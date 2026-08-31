<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookListener
{
    public function handle(WebhookReceived $event)
    {
        $payload = $event->payload;

        // 1. Verificamos se o evento é de checkout concluído
        if ($payload['type'] === 'checkout.session.completed') {

            $session = $payload['data']['object'];
            $userId = $session['client_reference_id'] ?? null;

            // 2. Vamos buscar o plano que enviámos no metadata do passo anterior
            $planSlug = $session['metadata']['plan_slug'] ?? null;

            if ($userId && $planSlug) {
                $user = User::find($userId);

                if ($user) {
                    // 3. Atualizar o plano na BD (Utilizador e Workspace)
                    $user->update(['plan' => $planSlug]);

                    if ($user->currentWorkspace) {
                        $user->currentWorkspace->update(['plan' => $planSlug]);
                    }

                    Log::info("✅ ATIVAÇÃO DINÂMICA: Plano '{$planSlug}' ativo para o User ID {$userId}");
                }
            } else {
                Log::warning("⚠️ Webhook incompleto: User ({$userId}) ou Plan ({$planSlug}) ausentes.");
            }
        }
    }
}

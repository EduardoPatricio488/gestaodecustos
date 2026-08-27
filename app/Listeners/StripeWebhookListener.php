<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookListener
{
    public function handle(WebhookReceived $event)
    {
        $payload = $event->payload;

        // Log para termos a certeza que o Stripe entrou no teu código
        Log::info('🔔 STRIPE EVENTO: '.$payload['type']);

        if ($payload['type'] === 'checkout.session.completed') {
            $session = $payload['data']['object'];

            $userId = $session['client_reference_id'] ?? null;
            $amount = $session['amount_total'] ?? 0;

            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $plan = ($amount >= 1000) ? 'pro' : 'plus';

                    // Forçar atualização na base de dados
                    $user->plan = $plan;
                    $user->save();

                    // Atualizar o Workspace
                    $workspace = Workspace::where('owner_id', $user->id)->first();
                    if ($workspace) {
                        $workspace->plan = $plan;
                        $workspace->save();
                    }

                    Log::info("✅ SUCESSO: Plano {$plan} ativado para o utilizador {$user->email}");
                }
            }
        }
    }
}

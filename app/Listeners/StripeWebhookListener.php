<?php

namespace App\Listeners;

use App\Mail\PlanReceiptMail;
use App\Models\EmailLog;
use App\Models\StoreCheckoutSession;
use App\Models\User;
use App\Services\StorePurchaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookListener
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        if ($payload['type'] === 'checkout.session.completed') {
            $session = $payload['data']['object'] ?? [];

            if (($session['metadata']['type'] ?? null) === 'store_purchase') {
                $this->handleStoreCheckoutCompleted($session);

                return;
            }

            $this->handleCheckoutSessionCompleted($session);

            return;
        }

        if ($payload['type'] === 'invoice.paid') {
            $this->handleInvoicePaid($payload['data']['object'] ?? []);
        }
    }

    protected function handleCheckoutSessionCompleted(array $session): void
    {
        $userId = $session['client_reference_id'] ?? null;
        $planSlug = $session['metadata']['plan_slug'] ?? null;

        if (! $userId || ! $planSlug) {
            Log::warning("⚠️ Webhook incompleto: User ({$userId}) ou Plan ({$planSlug}) ausentes.");

            return;
        }

        $user = User::find($userId);

        if (! $user) {
            return;
        }

        $user->update(['plan' => $planSlug]);

        if ($user->currentWorkspace) {
            $user->currentWorkspace->update(['plan' => $planSlug]);
        }

        $amount = $this->parseAmountFromCents((int) ($session['amount_total'] ?? 0));
        $reference = (string) ($session['id'] ?? 'checkout-session');

        $this->sendReceipt(
            $user,
            $planSlug,
            $amount,
            $reference,
            $session['invoice'] ?? null,
        );

        Log::info("✅ ATIVAÇÃO DINÂMICA: Plano '{$planSlug}' ativo para o User ID {$userId}");
    }

    protected function handleStoreCheckoutCompleted(array $session): void
    {
        $pendingId = $session['metadata']['pending_id'] ?? null;

        if (! $pendingId || ($session['payment_status'] ?? null) !== 'paid') {
            return;
        }

        $pending = StoreCheckoutSession::find($pendingId);

        if (! $pending) {
            Log::warning("⚠️ Webhook da loja: sessão pendente {$pendingId} não encontrada.");

            return;
        }

        app(StorePurchaseService::class)->completeStoreCheckout($pending, (string) ($session['id'] ?? ''));

        Log::info("✅ COMPRA NA LOJA CONFIRMADA (webhook): sessão pendente {$pendingId}.");
    }

    protected function handleInvoicePaid(array $invoice): void
    {
        $customerId = $invoice['customer'] ?? null;

        if (! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (! $user) {
            return;
        }

        $amount = $this->parseAmountFromCents((int) ($invoice['amount_paid'] ?? 0));
        $planSlug = $user->currentPlanSlug();
        $reference = (string) ($invoice['id'] ?? 'invoice');
        $receiptUrl = $invoice['hosted_invoice_url'] ?? null;

        $this->sendReceipt($user, $planSlug, $amount, $reference, $receiptUrl);

        Log::info("✅ RECIBO MENSAL ENVIADO: utilizador {$user->id} pago {$amount} do plano {$planSlug}");
    }

    protected function sendReceipt(User $user, string $planSlug, float $amount, string $reference, ?string $receiptUrl = null): void
    {
        $referenceKey = 'plan_receipt:'.$user->id.':'.$reference;

        if (cache()->has($referenceKey)) {
            Log::info("⚠️ Recibo já enviado para {$referenceKey}; ignorado para evitar duplicação.");

            return;
        }

        $alreadySent = EmailLog::where('user_id', $user->id)
            ->where('month_reference', $reference)
            ->where('subject', 'like', '%Recibo do plano%')
            ->exists();

        if ($alreadySent) {
            cache()->put($referenceKey, true, now()->addDay());
            Log::info("⚠️ Recibo duplicado detectado em log para {$reference}; ignorado.");

            return;
        }

        cache()->put($referenceKey, true, now()->addDay());

        Mail::to($user->email)->send(new PlanReceiptMail(
            $user,
            $planSlug,
            $amount,
            $reference,
            $receiptUrl,
        ));

        EmailLog::create([
            'user_id' => $user->id,
            'workspace_id' => $user->current_workspace_id,
            'subject' => 'Recibo do plano '.$planSlug,
            'month_reference' => $reference,
            'sent_at' => now(),
        ]);
    }

    protected function parseAmountFromCents(int $amountInCents): float
    {
        return round($amountInCents / 100, 2);
    }
}

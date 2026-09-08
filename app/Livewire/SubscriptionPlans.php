<?php

namespace App\Livewire;

use App\Models\SubscriptionPlan;
use App\Services\SubscriptionCheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SubscriptionPlans extends Component
{
    public $showSuccessModal = false;

    public $newPlanData = [];

    public function upgrade($plan)
    {
        $user = Auth::user();

        if ($plan === 'free') {
            app(SubscriptionCheckoutService::class)->upgradePlan($user, 'free');
            $this->showSuccessFor(null);

            return;
        }

        $planModel = SubscriptionPlan::where('slug', $plan)->where('is_active', true)->first();

        if (! $planModel) {
            $this->dispatch('toast', variant: 'error', text: 'Este plano já não está disponível.');

            return;
        }

        $priceId = $planModel->resolvedStripePriceId() ?: match ($plan) {
            'pro' => config('services.stripe.prices.pro'),
            'business' => config('services.stripe.prices.business'),
            default => config("services.stripe.prices.{$plan}"),
        };

        if (! $priceId) {
            if (app()->environment('production')) {
                $this->dispatch('toast', variant: 'error', text: 'Este plano ainda não está disponível para pagamento.');

                return;
            }

            app(SubscriptionCheckoutService::class)->upgradePlan($user, $planModel->slug);
            $this->showSuccessFor($planModel);

            return;
        }

        try {
            $checkout = $user->newSubscription($planModel->slug, $priceId)
                ->checkout([
                    'success_url' => route('dashboard', ['checkout' => 'success']).'&session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('hub.pricing', ['checkout' => 'cancel']),
                    'client_reference_id' => $user->id,
                    // 🔥 ESTA LINHA FALTAVA: É o que diz ao Webhook o que ativar
                    'metadata' => [
                        'plan_slug' => $planModel->slug,
                    ],
                ]);

            return redirect($checkout->url);
        } catch (\Throwable $e) {
            Log::error('Erro no Stripe Checkout: '.$e->getMessage(), [
                'user_id' => $user->id,
                'plan' => $planModel->slug,
                'price_id_prefix' => substr((string) $priceId, 0, 10),
            ]);

            if (app()->environment('production')) {
                $this->dispatch('toast', variant: 'error', text: 'Não foi possível contactar o Stripe. Tenta novamente mais tarde.');

                return;
            }

            $this->dispatch('toast', variant: 'error', text: 'Não foi possível contactar o Stripe. Ativação local de demonstração.');
            app(SubscriptionCheckoutService::class)->upgradePlan($user, $planModel->slug);
            $this->showSuccessFor($planModel);
        }
    }

    public function manageSubscription()
    {
        $user = Auth::user();

        if (! $user->stripe_id) {
            $this->dispatch('toast', variant: 'error', text: 'Esta conta ainda não tem uma assinatura Stripe associada. Contacta o suporte para regularizar o acesso.');

            return;
        }

        try {
            return $user->redirectToBillingPortal(route('hub.pricing'));
        } catch (\Throwable $e) {
            Log::error('Erro ao abrir o portal de faturação do Stripe: '.$e->getMessage(), [
                'user_id' => $user->id,
            ]);
            $this->dispatch('toast', variant: 'error', text: 'Não foi possível abrir a gestão da assinatura. Tenta novamente mais tarde.');
        }
    }

    private function showSuccessFor(?SubscriptionPlan $plan): void
    {
        $this->newPlanData = [
            'name' => $plan?->name ?? 'Free',
            'color' => ($plan && $plan->price >= 10) ? 'violet' : ($plan ? 'emerald' : 'zinc'),
            'icon' => ($plan && $plan->hasFeature('business_mode')) ? '🏢' : ($plan ? '⭐' : '🌱'),
            'raw' => $plan?->slug ?? 'free',
            'business' => $plan?->hasFeature('business_mode') ?? false,
        ];
        $this->showSuccessModal = true;
    }

    public function finish()
    {
        if (! empty($this->newPlanData['business'])) {
            return redirect()->route('hub.business.gateway');
        }

        return redirect()->route('dashboard');
    }

    public function render()
    {
        $active = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();

        return view('livewire.subscription-plans', [
            'currentPlan' => auth()->user()->plan ?? auth()->user()->currentWorkspace->plan ?? 'free',
            'corePlans' => $active->filter(fn (SubscriptionPlan $plan) => $plan->isCore())->values(),
            'extraPlans' => $active->reject(fn (SubscriptionPlan $plan) => $plan->isCore())->values(),
        ]);
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class SubscriptionPlans extends Component
{
    public $showSuccessModal = false;
    public $newPlanData = [];

    /**
     * Inicia o processo de Upgrade via Stripe
     */
    public function upgrade($plan)
    {
        $user = Auth::user();

        // 1. Se for o plano grátis, fazemos o downgrade local imediato
        if ($plan === 'free') {
            $user->update(['plan' => 'free']);
            if ($user->currentWorkspace) {
                $user->currentWorkspace->update(['plan' => 'free']);
            }
            $this->showSuccessFor('free');
            return;
        }

        // 2. Mapeamento de IDs do Stripe
        $prices = [
            'plus' => 'price_1TosJDH35BygzIwGXxaIKBjZ',
            'pro'  => 'price_1TosJuH35BygzIwGL7R3R2TH',
        ];

        if (!isset($prices[$plan])) {
            $this->dispatch('toast', variant: 'error', text: 'Plano inválido.');
            return;
        }

        // 3. Gerar a sessão de Checkout do Stripe
        $checkout = $user->newSubscription($plan, $prices[$plan])
            ->checkout([
                'success_url' => route('dashboard', ['checkout' => 'success']),
                'cancel_url' => route('hub.pricing', ['checkout' => 'cancel']),
                // 🔥 SEGURANÇA PARA A VENDA: Passamos o ID do utilizador como referência.
                // Isto garante que o Webhook saiba exatamente quem pagou.
                'client_reference_id' => $user->id,
            ]);

        // Redirecionamos manualmente para o URL do Stripe (Compatível com Livewire 3)
        return redirect($checkout->url);
    }

    private function showSuccessFor(string $plan): void
    {
        $this->newPlanData = [
            'name' => match ($plan) {
                'pro', 'company' => 'Business',
                'plus' => 'Premium',
                default => 'Gratuito',
            },
            'color' => match ($plan) {
                'pro', 'company' => 'violet',
                'plus' => 'emerald',
                default => 'zinc',
            },
            'icon' => match ($plan) {
                'pro', 'company' => '🏢',
                'plus' => '⭐',
                default => '🌱',
            },
            'raw' => $plan,
        ];
        $this->showSuccessModal = true;
    }

    public function finish()
    {
        if (in_array($this->newPlanData['raw'] ?? '', ['pro', 'company'])) {
            return redirect()->route('hub.business.gateway');
        }

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.subscription-plans', [
            'currentPlan' => auth()->user()->plan ?? auth()->user()->currentWorkspace->plan ?? 'free',
        ]);
    }
}

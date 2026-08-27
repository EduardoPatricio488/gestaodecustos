<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SubscriptionPlans extends Component
{
    public $showSuccessModal = false;

    public $newPlanData = [];

    /**
     * Inicia o processo de Upgrade via Stripe Checkout
     * Configuramos as chaves no .env para facilitar a vida ao comprador.
     */
    public function upgrade($plan)
    {
        $user = Auth::user();

        // 1. Tratamento de Downgrade para plano grátis
        if ($plan === 'free') {
            $user->update(['plan' => 'free']);

            if ($user->currentWorkspace) {
                $user->currentWorkspace->update(['plan' => 'free']);
            }

            $this->showSuccessFor('free');

            return;
        }

        // 2. Mapeamento dinâmico via Variáveis de Ambiente (.env)
        // Isso é o que torna o software "vendável" e fácil de configurar.
        $priceId = match ($plan) {
            'plus' => env('STRIPE_PRICE_PLUS'), // Definir no .env
            'pro' => env('STRIPE_PRICE_BUSINESS'), // Definir no .env
            default => null,
        };

        if (! $priceId) {
            Log::error("Tentativa de upgrade falhou: Preço para o plano [{$plan}] não configurado no .env");
            $this->dispatch('toast', variant: 'error', text: 'Este plano ainda não foi configurado pelo administrador.');

            return;
        }

        try {
            // 3. Gerar a sessão de Checkout do Stripe
            // Usamos o client_reference_id para o Webhook saber quem pagou.
            $checkout = $user->newSubscription($plan, $priceId)
                ->checkout([
                    'success_url' => route('dashboard', ['checkout' => 'success']),
                    'cancel_url' => route('hub.pricing', ['checkout' => 'cancel']),
                    'client_reference_id' => $user->id,
                ]);

            // Redirecionamento seguro para o Stripe
            return redirect($checkout->url);

        } catch (\Exception $e) {
            Log::error('Erro no Stripe Checkout: '.$e->getMessage());
            $this->dispatch('toast', variant: 'error', text: 'Erro ao conectar com o provedor de pagamentos.');
        }
    }

    /**
     * Prepara os dados para o modal de sucesso (após upgrade ou downgrade grátis)
     */
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

    /**
     * Finaliza o processo e redireciona o utilizador
     */
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

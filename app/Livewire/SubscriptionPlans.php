<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class SubscriptionPlans extends Component
{
    public $showSuccessModal = false;
    public $newPlanData = [];

    /**
     * Faz o upgrade do plano
     * $plan pode ser: 'free', 'plus' ou 'pro'
     */
    public function upgrade($plan)
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        if ($workspace) {
            // 1. Gravar na Base de Dados (Mantendo plus/pro para compatibilidade)
            $workspace->update([
                'plan' => $plan
            ]);

            // 2. Criar Registo Financeiro para o Painel Admin
            if ($plan !== 'free') {
                DB::table('payments')->insert([
                    'user_id' => $user->id,
                    'invoice_id' => 'INV-' . strtoupper($plan) . '-' . time(),
                    'plan_type' => $plan, // plus ou pro
                    'amount' => ($plan === 'pro' ? 10.00 : 5.00),
                    'status' => 'paid',
                    'method' => 'system',
                    'paid_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3. Configurar os dados para a mensagem de sucesso
            $this->newPlanData = [
                'name'  => ($plan === 'pro' ? 'Business' : ($plan === 'plus' ? 'Premium' : 'Gratuito')),
                'color' => ($plan === 'pro' ? 'violet' : ($plan === 'plus' ? 'emerald' : 'zinc')),
                'icon'  => ($plan === 'pro' ? '🏢' : '⭐'),
                'raw'   => $plan
            ];

            $this->showSuccessModal = true;
            $this->dispatch('toast', text: "Plano " . $this->newPlanData['name'] . " ativado!");
        }
    }

    public function finish()
    {
        if ($this->newPlanData['raw'] === 'pro') {
            return redirect()->route('hub.business.dashboard');
        }
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.subscription-plans', [
            'currentPlan' => auth()->user()->currentWorkspace->plan ?? 'free'
        ]);
    }
}

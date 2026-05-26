<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SubscriptionPlans extends Component
{
    /**
     * Faz o upgrade do plano e redireciona para a área correspondente.
     */
    public function upgrade($plan)
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        if ($workspace) {
            // 1. Atualiza o plano na base de dados
            // 'free' = 0€, 'plus' = 5€, 'pro' = 10€
            $workspace->update([
                'plan' => $plan
            ]);

            $this->dispatch('toast', text: "Plano " . strtoupper($plan) . " ativado com sucesso!");

            // 2. Se o plano for 'pro' (Business), vai direto para a área da empresa
            if ($plan === 'pro') {
                return redirect()->route('hub.business.dashboard');
            }

            // Caso contrário, volta para o dashboard pessoal
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.subscription-plans', [
            'currentPlan' => auth()->user()->currentWorkspace->plan ?? 'free'
        ]);
    }
}

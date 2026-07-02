<?php

namespace App\Livewire;

use App\Services\SubscriptionCheckoutService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SubscriptionPlans extends Component
{
    public $showSuccessModal = false;
    public $newPlanData = [];

    public function upgrade($plan)
    {
        $user = auth()->user();

        if ($plan === 'free') {
            $user->update(['plan' => 'free']);
            if ($user->currentWorkspace) {
                $user->currentWorkspace->update(['plan' => 'free']);
            }
            $this->showSuccessFor('free');

            return;
        }

        app(SubscriptionCheckoutService::class)->upgradePlan($user, $plan);
        $this->showSuccessFor($plan);
        $this->dispatch('toast', variant: 'success', text: 'Plano '.$this->newPlanData['name'].' ativado!');
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
            auth()->user()->update(['current_workspace_id' => null]);

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

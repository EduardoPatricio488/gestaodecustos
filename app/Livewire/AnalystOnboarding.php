<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class AnalystOnboarding extends Component
{
    public $step = 1;
    public $totalSteps = 4;
    public $isOpen = false;

    public function mount()
    {
        $user = auth()->user();
        // Abre automaticamente se for Analista e ainda não concluiu o onboarding
        if ($user->isAnalyst() && !$user->onboarding_completed) {
            $this->isOpen = true;
        }
    }

    #[On('open-analyst-tutorial')]
    public function restart()
    {
        $this->step = 1;
        $this->isOpen = true;
    }

    public function nextStep()
    {
        if ($this->step < $this->totalSteps) {
            $this->step++;
        } else {
            $this->finish();
        }
    }

    public function finish()
    {
        auth()->user()->update(['onboarding_completed' => true]);
        $this->isOpen = false;
        $this->dispatch('toast', text: 'Protocolo de análise de dados ativo.');
    }

    public function render()
    {
        return view('livewire.analyst-onboarding');
    }
}

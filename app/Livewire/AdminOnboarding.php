<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class AdminOnboarding extends Component
{
    public $step = 1;
    public $totalSteps = 5;
    public $isOpen = false;

    public function mount()
    {
        $user = auth()->user();
        // Abre automaticamente apenas se for admin e ainda não tiver concluído
        if ($user->isAdmin() && !$user->onboarding_completed) {
            $this->isOpen = true;
        }
    }

    #[On('open-admin-tutorial')]
    public function restartTutorial()
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
        $this->dispatch('toast', text: 'Protocolo de administração ativado.');
    }

    public function render()
    {
        return view('livewire.admin-onboarding');
    }
}

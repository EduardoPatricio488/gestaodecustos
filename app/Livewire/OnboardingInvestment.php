<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;
use App\Models\BankAccount;

class OnboardingInvestment extends InvestmentsHub
{
    use AuthorizesOnboardingWorkspace;

    public bool $onboardingSaved = false;

    public function mount(): void
    {
        $this->initializeOnboardingWorkspace();
        parent::mount();
    }

    public function dispatch($event, ...$params)
    {
        if (in_array($event, ['modal-show-add-investment', 'modal-close-add-investment'], true)) {
            $event = 'onboarding-'.$event;
        }

        return parent::dispatch($event, ...$params);
    }

    public function createAsset(): void
    {
        $this->authorizeOnboardingWrite();
        $this->onboardingSaved = false;
        parent::createAsset();
    }

    public function save(): void
    {
        $this->authorizeOnboardingWrite();
        $this->editingId = null;
        parent::save();
        $this->onboardingSaved = true;
    }

    public function render()
    {
        $user = auth()->user();
        $bankAccounts = BankAccount::where('workspace_id', $user->current_workspace_id)
            ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id))
            ->orderBy('name')->get();

        return view('livewire.onboarding-investment', compact('bankAccounts'));
    }
}

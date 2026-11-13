<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;
use App\Models\BankAccount;

class OnboardingIncome extends IncomeHub
{
    use AuthorizesOnboardingWorkspace;

    public bool $onboardingSaved = false;

    public function mount(bool $dashboardMode = false)
    {
        $this->initializeOnboardingWorkspace();
        parent::mount($dashboardMode);
    }

    public function dispatch($event, ...$params)
    {
        if (str_starts_with($event, 'modal-show-') || str_starts_with($event, 'modal-close-')) {
            $event = 'onboarding-'.$event;
        }

        return parent::dispatch($event, ...$params);
    }

    public function openIncomeSource(string $source): void
    {
        $this->authorizeOnboardingWrite();
        $this->onboardingSaved = false;
        $this->resetValidation();
        match ($source) {
            'emprego' => $this->newEmployment(),
            'renda' => $this->openOnboardingRental(),
            'freelance' => $this->openFreelanceModal(),
            'investimento' => $this->openInvestmentModal(),
            'reforma' => $this->openPensionModal(),
            'bolsa' => $this->openScholarshipModal(),
            'outro' => $this->openOtherModal(),
            default => abort(404),
        };
    }

    private function openOnboardingRental(): void
    {
        $this->reset(['editingFixedId', 'recDescription', 'recAmount', 'recRentalGross', 'recRentalExpenses', 'recDay', 'recBankAccountId']);
        $this->recPropertyType = 'arrendamento';
        $this->recDay = 1;
        $this->dispatch('modal-show-renda');
    }

    private function prepareNewIncome(): void
    {
        $this->authorizeOnboardingWrite();
        $this->editingFixedId = null;
        if ($this->recBankAccountId !== '') {
            $user = auth()->user();
            abort_unless(BankAccount::where('workspace_id', $user->current_workspace_id)
                ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id))
                ->whereKey($this->recBankAccountId)->exists(), 422, 'Conta bancária inválida.');
        }
    }

    public function saveEmployment(bool $closeModal = true): ?int
    {
        $this->prepareNewIncome();
        $this->recalculateEmploymentSalary();

        $id = parent::saveEmployment($closeModal);
        $this->onboardingSaved = $id !== null;

        return $id;
    }

    public function scanEmploymentContract(): void
    {
        $this->authorizeOnboardingWrite();
        parent::scanEmploymentContract();
    }

    public function saveRental()
    {
        $this->prepareNewIncome();
        parent::saveRental();
        $this->onboardingSaved = true;
    }
    public function saveFreelance()
    {
        $this->prepareNewIncome();
        parent::saveFreelance();
        $this->onboardingSaved = true;
    }
    public function saveInvestment()
    {
        $this->prepareNewIncome();
        parent::saveInvestment();
        $this->onboardingSaved = true;
    }
    public function savePension()
    {
        $this->prepareNewIncome();
        parent::savePension();
        $this->onboardingSaved = true;
    }
    public function saveScholarship()
    {
        $this->prepareNewIncome();
        parent::saveScholarship();
        $this->onboardingSaved = true;
    }
    public function saveOther()
    {
        $this->prepareNewIncome();
        parent::saveOther();
        $this->onboardingSaved = true;
    }

    public function render()
    {
        return view('livewire.onboarding-income');
    }
}

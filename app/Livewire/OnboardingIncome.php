<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;
use App\Models\BankAccount;
use App\Models\RecurringIncome;

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
        $sourceLabels = [
            'emprego' => 'Contrato de trabalho',
            'imobiliario' => 'Rendimento imobiliário',
            'freelance' => 'Trabalho independente',
            'investimento' => 'Rendimento de investimentos',
            'reforma' => 'Reforma / pensão',
            'bolsa' => 'Bolsa de estudo',
            'outro' => 'Outros rendimentos',
        ];

        $savedIncomes = RecurringIncome::query()
            ->with('bankAccount:id,name')
            ->where('workspace_id', $this->onboardingWorkspaceId)
            ->where('user_id', auth()->id())
            ->whereIn('source', array_keys($sourceLabels))
            ->where(function ($query): void {
                // O subsídio de alimentação em cartão é um registo auxiliar do contrato,
                // não uma fonte de rendimento independente.
                $query->where('source', '!=', 'emprego')
                    ->orWhereNull('metadata->income_type');
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $savedIncomeGroups = collect($sourceLabels)
            ->map(function (string $label, string $source) use ($savedIncomes): array {
                return [
                    'source' => $source,
                    'label' => $label,
                    'records' => $savedIncomes->where('source', $source)->values(),
                ];
            })
            ->filter(fn (array $group): bool => $group['records']->isNotEmpty())
            ->values();

        return view('livewire.onboarding-income', compact('savedIncomeGroups'));
    }
}

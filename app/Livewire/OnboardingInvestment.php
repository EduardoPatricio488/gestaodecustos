<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;
use App\Models\BankAccount;
use App\Models\Investment;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class OnboardingInvestment extends InvestmentsHub
{
    use AuthorizesOnboardingWorkspace;

    public bool $onboardingSaved = false;

    #[Locked]
    public ?int $expandedInvestmentId = null;

    public array $xtbForm = [];

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

    public function toggleInvestment(int $id): void
    {
        $this->authorizeOnboardingWrite();
        $this->resetValidation();
        if ($this->expandedInvestmentId === $id) {
            $this->reset(['expandedInvestmentId', 'xtbForm']);

            return;
        }

        $investment = $this->investmentQuery()->findOrFail($id);
        $this->expandedInvestmentId = $investment->id;
        $this->xtbForm = $investment->only([
            'name', 'symbol', 'isin', 'type', 'quantity', 'average_price', 'current_price',
            'fees', 'bank_account_id', 'exchange', 'network', 'provider', 'issuer', 'series',
            'product_type', 'interest_rate', 'loyalty_bonus',
        ]);
        $this->xtbForm['operation_date'] = $investment->operation_date?->toDateString();
        $this->xtbForm['capitalization_date'] = $investment->capitalization_date?->toDateString();
        $this->onboardingSaved = false;
    }

    public function saveInvestment(): void
    {
        $this->authorizeOnboardingWrite();
        abort_unless($this->expandedInvestmentId, 404);
        $investment = $this->investmentQuery()->findOrFail($this->expandedInvestmentId);
        $user = auth()->user();
        $validated = $this->validate([
            'xtbForm.name' => 'required|string|max:255',
            'xtbForm.symbol' => 'required|string|max:20',
            'xtbForm.isin' => 'nullable|string|max:12',
            'xtbForm.type' => 'required|in:Acao,Cripto,ETF,Fundo,Divida',
            'xtbForm.quantity' => 'required|numeric|gt:0',
            'xtbForm.average_price' => 'required|numeric|gt:0',
            'xtbForm.current_price' => 'required|numeric|min:0',
            'xtbForm.fees' => 'nullable|numeric|min:0',
            'xtbForm.bank_account_id' => ['required', 'integer', Rule::exists('bank_accounts', 'id')->where(
                fn ($query) => $query->where('workspace_id', $user->current_workspace_id)
                    ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id))
            )],
            'xtbForm.operation_date' => 'nullable|date',
            'xtbForm.exchange' => 'nullable|string|max:255',
            'xtbForm.network' => 'nullable|string|max:255',
            'xtbForm.provider' => 'nullable|string|max:255',
            'xtbForm.issuer' => 'nullable|string|max:255',
            'xtbForm.series' => 'nullable|string|max:255',
            'xtbForm.product_type' => 'nullable|in:CA,CT',
            'xtbForm.interest_rate' => 'nullable|numeric|min:0',
            'xtbForm.loyalty_bonus' => 'nullable|numeric|min:0',
            'xtbForm.capitalization_date' => 'nullable|date',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser texto.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'gt' => 'O campo :attribute deve ser superior a :value.',
            'min.numeric' => 'O campo :attribute deve ser igual ou superior a :min.',
            'max.string' => 'O campo :attribute não pode ter mais de :max caracteres.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'in' => 'O campo :attribute selecionado é inválido.',
            'exists' => 'A :attribute selecionada não está disponível neste espaço.',
        ], [
            'xtbForm.name' => 'nome do ativo', 'xtbForm.symbol' => 'símbolo', 'xtbForm.isin' => 'ISIN',
            'xtbForm.type' => 'classe', 'xtbForm.quantity' => 'quantidade', 'xtbForm.average_price' => 'preço médio',
            'xtbForm.current_price' => 'preço atual', 'xtbForm.fees' => 'comissões', 'xtbForm.bank_account_id' => 'conta associada',
            'xtbForm.operation_date' => 'data da operação', 'xtbForm.exchange' => 'bolsa', 'xtbForm.network' => 'rede',
            'xtbForm.provider' => 'gestora', 'xtbForm.issuer' => 'emitente', 'xtbForm.series' => 'série',
            'xtbForm.product_type' => 'produto', 'xtbForm.interest_rate' => 'taxa base',
            'xtbForm.loyalty_bonus' => 'prémio de permanência', 'xtbForm.capitalization_date' => 'data de capitalização',
        ]);

        $data = array_map(fn ($value) => $value === '' ? null : $value, $validated['xtbForm']);
        $data['symbol'] = strtoupper(trim($data['symbol']));
        $data['isin'] = filled($data['isin'] ?? null) ? strtoupper(trim($data['isin'])) : null;
        $data['fees'] = $data['fees'] ?? 0;
        $investment->update($data);
        $this->reset(['expandedInvestmentId', 'xtbForm']);
        $this->onboardingSaved = true;
    }

    public function deleteAsset(int $id): void
    {
        $this->authorizeOnboardingWrite();
        $this->investmentQuery()->findOrFail($id)->delete();
        if ($this->expandedInvestmentId === $id) {
            $this->reset(['expandedInvestmentId', 'xtbForm']);
        }
        $this->onboardingSaved = false;
        $this->dispatch('toast', text: 'Investimento eliminado.');
    }

    private function investmentQuery()
    {
        $user = auth()->user();

        return Investment::where('workspace_id', $user->current_workspace_id)
            ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id));
    }

    #[On('onboarding-investments-imported')]
    public function refreshInvestments(): void
    {
        $this->authorizeOnboardingWrite();
    }

    public function render()
    {
        $this->authorizeOnboardingWrite();
        $user = auth()->user();
        $bankAccounts = BankAccount::where('workspace_id', $user->current_workspace_id)
            ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id))
            ->orderBy('name')->get();
        $xtbInvestments = $this->investmentQuery()->orderBy('name')->orderBy('id')->get();

        return view('livewire.onboarding-investment', compact('bankAccounts', 'xtbInvestments'));
    }
}

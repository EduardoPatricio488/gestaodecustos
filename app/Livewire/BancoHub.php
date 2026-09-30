<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankCredit;
use App\Models\BankPatrimony;
use App\Models\BankReserve;
use App\Models\BankTransfer;
use App\Models\BankTransitItem;
use App\Services\BancoService;
use App\Services\BusinessAccessService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class BancoHub extends Component
{
    use WithPagination;

    public string $activeTab = 'overview';
    public bool $showModal = false;
    public string $modalType = '';
    public ?int $editingId = null;
    public string $acc_name = '';
    public string $acc_type = 'corrente';
    public float $acc_balance = 0;
    public string $acc_currency = 'EUR';
    public string $acc_color = '#6366f1';
    public string $acc_icon = 'building-library';
    public string $acc_status = 'active';
    public string $acc_description = '';
    public string $acc_bank_name = '';
    public string $acc_iban = '';
    public string $acc_swift = '';
    public string $acc_holder = '';
    public bool $acc_is_business = false;
    public bool $acc_include = true;
    public ?float $acc_alert_below = null;
    public string $acc_opened_at = '';
    public ?int $tr_from_id = null;
    public ?int $tr_to_id = null;
    public float $tr_amount = 0;
    public string $tr_description = '';
    public string $tr_date = '';
    public string $tr_status = 'completed';
    public string $tr_notes = '';
    public string $res_name = '';
    public float $res_amount = 0;
    public ?float $res_target = null;
    public string $res_target_date = '';
    public string $res_color = '#10b981';
    public string $res_icon = 'banknotes';
    public string $res_description = '';
    public bool $res_is_business = false;
    public string $trs_name = '';
    public float $trs_amount = 0;
    public string $trs_direction = 'in';
    public string $trs_type = 'transfer_sent';
    public string $trs_origin = '';
    public string $trs_destination = '';
    public string $trs_expected_date = '';
    public string $trs_description = '';
    public bool $trs_is_business = false;
    public string $crd_name = '';
    public float $crd_amount = 0;
    public string $crd_category = 'client';
    public string $crd_due_date = '';
    public string $crd_notes = '';
    public bool $crd_business = false;
    public string $pat_type = 'real_estate';
    public string $pat_name = '';
    public float $pat_value = 0;
    public ?float $pat_purchase_price = null;
    public string $pat_purchase_date = '';
    public string $pat_description = '';
    public bool $pat_is_business = false;
    public string $search = '';
    public string $accountFilter = 'all';
    public string $statusFilter = 'all';

    protected function rules(): array
    {
        return match ($this->modalType) {
            'account' => ['acc_name' => 'required|string|max:100', 'acc_type' => 'required|string', 'acc_balance' => 'required|numeric', 'acc_currency' => 'required|string|max:10', 'acc_color' => 'required|string'],
            'transfer' => ['tr_from_id' => 'required|integer|different:tr_to_id', 'tr_to_id' => 'required|integer', 'tr_amount' => 'required|numeric|min:0.01', 'tr_date' => 'required|date'],
            'reserve' => ['res_name' => 'required|string|max:100', 'res_amount' => 'required|numeric|min:0'],
            'transit' => ['trs_name' => 'required|string|max:100', 'trs_amount' => 'required|numeric|min:0.01', 'trs_direction' => 'required|in:in,out'],
            'credit' => ['crd_name' => 'required|string|max:100', 'crd_amount' => 'required|numeric|min:0.01'],
            'patrimony' => ['pat_name' => 'required|string|max:100', 'pat_type' => 'required|string', 'pat_value' => 'required|numeric|min:0'],
            default => [],
        };
    }

    public function mount(): void
    {
        $this->tr_date = now()->format('Y-m-d');
        $this->trs_expected_date = now()->addDays(3)->format('Y-m-d');
        $this->crd_due_date = now()->addDays(30)->format('Y-m-d');
    }

    private function authorizeBankMutation(): void
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        if ($workspace && in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('manage_financials', $user, $workspace);
        }
    }

    public function save(): void
    {
        $this->authorizeBankMutation();
        $this->validate();
        $user = auth()->user();
        $wsId = $user->current_workspace_id;
        $isCreating = ! $this->editingId;

        if ($this->modalType === 'transfer') {
            abort_unless(
                BankAccount::where('workspace_id', $wsId)
                    ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->whereKey($this->tr_from_id)->exists()
                && BankAccount::where('workspace_id', $wsId)
                    ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->whereKey($this->tr_to_id)->exists(),
                422,
                'Conta bancária inválida.'
            );
        }

        match ($this->modalType) {
            'account' => $this->saveAccount($wsId),
            'transfer' => $this->saveTransfer($wsId),
            'reserve' => $this->saveReserve($wsId),
            'transit' => $this->saveTransit($wsId),
            'credit' => $this->saveCredit($wsId),
            'patrimony' => $this->savePatrimony($wsId),
        };

        $this->showModal = false;
        if ($isCreating) {
            $user->awardXp(15, 'registo bancário criado');
        }
        $this->dispatch('toast', variant: 'success', text: 'Registo bancário guardado com sucesso!');
        $this->resetForm();
    }

    private function saveAccount(int $wsId): void
    {
        BankAccount::updateOrCreate(['id' => $this->editingId, 'workspace_id' => $wsId], [
            'workspace_id' => $wsId, 'user_id' => auth()->id(), 'name' => $this->acc_name, 'type' => $this->acc_type,
            'balance' => $this->acc_balance, 'currency' => $this->acc_currency, 'color' => $this->acc_color,
            'icon' => $this->acc_icon, 'status' => $this->acc_status, 'description' => $this->acc_description ?: null,
            'bank_name' => $this->acc_bank_name ?: null, 'iban' => $this->acc_iban ?: null, 'swift' => $this->acc_swift ?: null,
            'holder_name' => $this->acc_holder ?: null, 'is_business' => $this->acc_is_business, 'include_in_total' => $this->acc_include,
            'alert_below' => $this->acc_alert_below ?: null, 'opened_at' => $this->acc_opened_at ?: null,
        ]);
    }

    private function saveTransfer(int $wsId): void
    {
        $transfer = BankTransfer::updateOrCreate(['id' => $this->editingId, 'workspace_id' => $wsId], [
            'workspace_id' => $wsId, 'user_id' => auth()->id(), 'from_account_id' => $this->tr_from_id,
            'to_account_id' => $this->tr_to_id, 'amount' => $this->tr_amount, 'description' => $this->tr_description ?: null,
            'transferred_at' => $this->tr_date, 'status' => $this->tr_status, 'notes' => $this->tr_notes ?: null,
        ]);
        if ($transfer->status === 'completed' && ! $this->editingId) {
            BankAccount::where('workspace_id', $wsId)->whereKey($this->tr_from_id)->decrement('balance', $this->tr_amount);
            BankAccount::where('workspace_id', $wsId)->whereKey($this->tr_to_id)->increment('balance', $this->tr_amount);
        }
    }

    private function saveReserve(int $wsId): void { BankReserve::updateOrCreate(['id' => $this->editingId, 'workspace_id' => $wsId], ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'name' => $this->res_name, 'amount' => $this->res_amount, 'target_amount' => $this->res_target ?: null, 'target_date' => $this->res_target_date ?: null, 'color' => $this->res_color, 'icon' => $this->res_icon, 'description' => $this->res_description ?: null, 'is_business' => $this->res_is_business, 'status' => 'active']); }
    private function saveTransit(int $wsId): void { BankTransitItem::updateOrCreate(['id' => $this->editingId, 'workspace_id' => $wsId], ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'name' => $this->trs_name, 'amount' => $this->trs_amount, 'direction' => $this->trs_direction, 'type' => $this->trs_type, 'origin' => $this->trs_origin ?: null, 'destination' => $this->trs_destination ?: null, 'expected_date' => $this->trs_expected_date ?: null, 'description' => $this->trs_description ?: null, 'is_business' => $this->trs_is_business, 'status' => 'pending']); }
    private function saveCredit(int $wsId): void { BankCredit::updateOrCreate(['id' => $this->editingId, 'workspace_id' => $wsId], ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'name' => $this->crd_name, 'amount' => $this->crd_amount, 'category' => $this->crd_category, 'due_date' => $this->crd_due_date ?: null, 'notes' => $this->crd_notes ?: null, 'is_business' => $this->crd_business, 'status' => 'pending']); }
    private function savePatrimony(int $wsId): void { BankPatrimony::updateOrCreate(['id' => $this->editingId, 'workspace_id' => $wsId], ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'type' => $this->pat_type, 'name' => $this->pat_name, 'value' => $this->pat_value, 'purchase_price' => $this->pat_purchase_price ?: null, 'purchase_date' => $this->pat_purchase_date ?: null, 'description' => $this->pat_description ?: null, 'is_business' => $this->pat_is_business, 'status' => 'active']); }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'acc_name', 'acc_balance', 'acc_description', 'acc_bank_name', 'acc_iban', 'acc_swift', 'acc_holder', 'acc_alert_below', 'acc_opened_at', 'tr_from_id', 'tr_to_id', 'tr_amount', 'tr_description', 'tr_notes', 'res_name', 'res_amount', 'res_target', 'res_target_date', 'res_description', 'trs_name', 'trs_amount', 'trs_origin', 'trs_destination', 'trs_expected_date', 'trs_description', 'crd_name', 'crd_amount', 'crd_due_date', 'crd_notes', 'pat_name', 'pat_value', 'pat_purchase_price', 'pat_purchase_date', 'pat_description']);
        $this->modalType = '';
    }
}

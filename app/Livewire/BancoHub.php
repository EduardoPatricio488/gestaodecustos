<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Services\BancoService;
use App\Models\BankAccount;
use App\Models\BankTransfer;
use App\Models\BankReserve;
use App\Models\BankTransitItem;
use App\Models\BankCredit;
use App\Models\BankPatrimony;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class BancoHub extends Component
{
    use WithPagination;

    /* ─── NAVEGAÇÃO ─────────────────────────────────────────────── */
    public string $activeTab = 'overview';

    /* ─── ESTADO GLOBAL ─────────────────────────────────────────── */
    public bool $showModal       = false;
    public string $modalType     = ''; // account | transfer | reserve | transit | credit | patrimony
    public ?int $editingId       = null;

    /* ─── CONTA BANCÁRIA ─────────────────────────────────────────── */
    public string $acc_name        = '';
    public string $acc_type        = 'corrente';
    public float  $acc_balance     = 0;
    public string $acc_currency    = 'EUR';
    public string $acc_color       = '#6366f1';
    public string $acc_icon        = 'building-library';
    public string $acc_status      = 'active';
    public string $acc_description = '';
    public string $acc_bank_name   = '';
    public string $acc_iban        = '';
    public string $acc_swift       = '';
    public string $acc_holder      = '';
    public bool   $acc_is_business = false;
    public bool   $acc_include     = true;
    public ?float $acc_alert_below = null;
    public string $acc_opened_at   = '';

    /* ─── TRANSFERÊNCIA ─────────────────────────────────────────── */
    public ?int   $tr_from_id      = null;
    public ?int   $tr_to_id        = null;
    public float  $tr_amount       = 0;
    public string $tr_description  = '';
    public string $tr_date         = '';
    public string $tr_status       = 'completed';
    public string $tr_notes        = '';

    /* ─── RESERVA ───────────────────────────────────────────────── */
    public string $res_name        = '';
    public float  $res_amount      = 0;
    public ?float $res_target      = null;
    public string $res_target_date = '';
    public string $res_color       = '#10b981';
    public string $res_icon        = 'banknotes';
    public string $res_description = '';
    public bool   $res_is_business = false;

    /* ─── TRÂNSITO ──────────────────────────────────────────────── */
    public string $trs_name          = '';
    public float  $trs_amount        = 0;
    public string $trs_direction     = 'in';
    public string $trs_type          = 'transfer_sent';
    public string $trs_origin        = '';
    public string $trs_destination   = '';
    public string $trs_expected_date = '';
    public string $trs_description   = '';
    public bool   $trs_is_business   = false;

    /* ─── CRÉDITO ───────────────────────────────────────────────── */
    public string $crd_name     = '';
    public float  $crd_amount   = 0;
    public string $crd_category = 'client';
    public string $crd_due_date = '';
    public string $crd_notes    = '';
    public bool   $crd_business = false;

    /* ─── PATRIMÓNIO ─────────────────────────────────────────────── */
    public string $pat_type          = 'real_estate';
    public string $pat_name          = '';
    public float  $pat_value         = 0;
    public ?float $pat_purchase_price= null;
    public string $pat_purchase_date = '';
    public string $pat_description   = '';
    public bool   $pat_is_business   = false;

    /* ─── FILTROS ───────────────────────────────────────────────── */
    public string $search            = '';
    public string $accountFilter     = 'all'; // all, personal, business
    public string $statusFilter      = 'all';

    /* ─── VALIDAÇÕES ────────────────────────────────────────────── */
    protected function rules(): array
    {
        return match ($this->modalType) {
            'account' => [
                'acc_name'    => 'required|string|max:100',
                'acc_type'    => 'required|string',
                'acc_balance' => 'required|numeric',
                'acc_currency'=> 'required|string|max:10',
                'acc_color'   => 'required|string',
            ],
            'transfer' => [
                'tr_from_id'  => 'required|integer|different:tr_to_id',
                'tr_to_id'    => 'required|integer',
                'tr_amount'   => 'required|numeric|min:0.01',
                'tr_date'     => 'required|date',
            ],
            'reserve' => [
                'res_name'   => 'required|string|max:100',
                'res_amount' => 'required|numeric|min:0',
            ],
            'transit' => [
                'trs_name'   => 'required|string|max:100',
                'trs_amount' => 'required|numeric|min:0.01',
                'trs_direction' => 'required|in:in,out',
            ],
            'credit' => [
                'crd_name'   => 'required|string|max:100',
                'crd_amount' => 'required|numeric|min:0.01',
            ],
            'patrimony' => [
                'pat_name'  => 'required|string|max:100',
                'pat_type'  => 'required|string',
                'pat_value' => 'required|numeric|min:0',
            ],
            default => [],
        };
    }

    /* ─── MOUNT ──────────────────────────────────────────────────── */
    public function mount(): void
    {
        $this->tr_date         = now()->format('Y-m-d');
        $this->trs_expected_date = now()->addDays(3)->format('Y-m-d');
        $this->crd_due_date    = now()->addDays(30)->format('Y-m-d');
    }

    /* ─── ABRIR MODAIS ───────────────────────────────────────────── */

    public function openAccountModal(?int $id = null): void
    {
        $this->resetForm();
        $this->modalType = 'account';

        if ($id) {
            $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)
                ->findOrFail($id);

            $this->editingId       = $account->id;
            $this->acc_name        = $account->name;
            $this->acc_type        = $account->type;
            $this->acc_balance     = $account->balance;
            $this->acc_currency    = $account->currency ?? 'EUR';
            $this->acc_color       = $account->color ?? '#6366f1';
            $this->acc_icon        = $account->icon ?? 'building-library';
            $this->acc_status      = $account->status ?? 'active';
            $this->acc_description = $account->description ?? '';
            $this->acc_bank_name   = $account->bank_name ?? '';
            $this->acc_iban        = $account->iban ?? '';
            $this->acc_swift       = $account->swift ?? '';
            $this->acc_holder      = $account->holder_name ?? '';
            $this->acc_is_business = (bool) $account->is_business;
            $this->acc_include     = (bool) ($account->include_in_total ?? true);
            $this->acc_alert_below = $account->alert_below;
            $this->acc_opened_at   = $account->opened_at
                ? $account->opened_at->format('Y-m-d')
                : '';
        }

        $this->showModal = true;
    }

    public function openTransferModal(): void
    {
        $this->resetForm();
        $this->modalType = 'transfer';
        $this->tr_date   = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openReserveModal(?int $id = null): void
    {
        $this->resetForm();
        $this->modalType = 'reserve';

        if ($id) {
            $reserve = BankReserve::where('workspace_id', auth()->user()->current_workspace_id)
                ->findOrFail($id);

            $this->editingId       = $reserve->id;
            $this->res_name        = $reserve->name;
            $this->res_amount      = $reserve->amount;
            $this->res_target      = $reserve->target_amount;
            $this->res_target_date = $reserve->target_date ? $reserve->target_date->format('Y-m-d') : '';
            $this->res_color       = $reserve->color ?? '#10b981';
            $this->res_icon        = $reserve->icon ?? 'banknotes';
            $this->res_description = $reserve->description ?? '';
            $this->res_is_business = (bool) $reserve->is_business;
        }

        $this->showModal = true;
    }

    public function openTransitModal(?int $id = null): void
    {
        $this->resetForm();
        $this->modalType = 'transit';

        if ($id) {
            $item = BankTransitItem::where('workspace_id', auth()->user()->current_workspace_id)
                ->findOrFail($id);

            $this->editingId          = $item->id;
            $this->trs_name           = $item->name;
            $this->trs_amount         = $item->amount;
            $this->trs_direction      = $item->direction;
            $this->trs_type           = $item->type;
            $this->trs_origin         = $item->origin ?? '';
            $this->trs_destination    = $item->destination ?? '';
            $this->trs_expected_date  = $item->expected_date ? $item->expected_date->format('Y-m-d') : '';
            $this->trs_description    = $item->description ?? '';
            $this->trs_is_business    = (bool) $item->is_business;
        }

        $this->showModal = true;
    }

    public function openCreditModal(?int $id = null): void
    {
        $this->resetForm();
        $this->modalType = 'credit';

        if ($id) {
            $credit = BankCredit::where('workspace_id', auth()->user()->current_workspace_id)
                ->findOrFail($id);

            $this->editingId    = $credit->id;
            $this->crd_name     = $credit->name;
            $this->crd_amount   = $credit->amount;
            $this->crd_category = $credit->category ?? 'client';
            $this->crd_due_date = $credit->due_date ? $credit->due_date->format('Y-m-d') : '';
            $this->crd_notes    = $credit->notes ?? '';
            $this->crd_business = (bool) $credit->is_business;
        }

        $this->showModal = true;
    }

    public function openPatrimonyModal(?int $id = null): void
    {
        $this->resetForm();
        $this->modalType = 'patrimony';

        if ($id) {
            $pat = BankPatrimony::where('workspace_id', auth()->user()->current_workspace_id)
                ->findOrFail($id);

            $this->editingId          = $pat->id;
            $this->pat_type           = $pat->type;
            $this->pat_name           = $pat->name;
            $this->pat_value          = $pat->value;
            $this->pat_purchase_price = $pat->purchase_price;
            $this->pat_purchase_date  = $pat->purchase_date ? $pat->purchase_date->format('Y-m-d') : '';
            $this->pat_description    = $pat->description ?? '';
            $this->pat_is_business    = (bool) $pat->is_business;
        }

        $this->showModal = true;
    }

    /* ─── GUARDAR ─────────────────────────────────────────────────── */

    public function save(): void
    {
        $this->validate();
        $wsId = auth()->user()->current_workspace_id;

        match ($this->modalType) {
            'account'   => $this->saveAccount($wsId),
            'transfer'  => $this->saveTransfer($wsId),
            'reserve'   => $this->saveReserve($wsId),
            'transit'   => $this->saveTransit($wsId),
            'credit'    => $this->saveCredit($wsId),
            'patrimony' => $this->savePatrimony($wsId),
        };

        $this->showModal = false;
        $this->dispatch('toast', text: 'Guardado com sucesso!');
        $this->resetForm();
    }

    private function saveAccount(int $wsId): void
    {
        BankAccount::updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id'    => $wsId,
                'user_id'         => auth()->id(),
                'name'            => $this->acc_name,
                'type'            => $this->acc_type,
                'balance'         => $this->acc_balance,
                'currency'        => $this->acc_currency,
                'color'           => $this->acc_color,
                'icon'            => $this->acc_icon,
                'status'          => $this->acc_status,
                'description'     => $this->acc_description ?: null,
                'bank_name'       => $this->acc_bank_name ?: null,
                'iban'            => $this->acc_iban ?: null,
                'swift'           => $this->acc_swift ?: null,
                'holder_name'     => $this->acc_holder ?: null,
                'is_business'     => $this->acc_is_business,
                'include_in_total'=> $this->acc_include,
                'alert_below'     => $this->acc_alert_below ?: null,
                'opened_at'       => $this->acc_opened_at ?: null,
            ]
        );
    }

    private function saveTransfer(int $wsId): void
    {
        $transfer = BankTransfer::updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id'   => $wsId,
                'user_id'        => auth()->id(),
                'from_account_id'=> $this->tr_from_id,
                'to_account_id'  => $this->tr_to_id,
                'amount'         => $this->tr_amount,
                'description'    => $this->tr_description ?: null,
                'transferred_at' => $this->tr_date,
                'status'         => $this->tr_status,
                'notes'          => $this->tr_notes ?: null,
            ]
        );

        // Actualizar saldos se a transferência estiver completa
        if ($transfer->status === 'completed' && !$this->editingId) {
            BankAccount::withoutGlobalScopes()->where('id', $this->tr_from_id)
                ->decrement('balance', $this->tr_amount);
            BankAccount::withoutGlobalScopes()->where('id', $this->tr_to_id)
                ->increment('balance', $this->tr_amount);
        }
    }

    private function saveReserve(int $wsId): void
    {
        BankReserve::updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => $wsId,
                'user_id'      => auth()->id(),
                'name'         => $this->res_name,
                'amount'       => $this->res_amount,
                'target_amount'=> $this->res_target ?: null,
                'target_date'  => $this->res_target_date ?: null,
                'color'        => $this->res_color,
                'icon'         => $this->res_icon,
                'description'  => $this->res_description ?: null,
                'is_business'  => $this->res_is_business,
                'status'       => 'active',
            ]
        );
    }

    private function saveTransit(int $wsId): void
    {
        BankTransitItem::updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id'  => $wsId,
                'user_id'       => auth()->id(),
                'name'          => $this->trs_name,
                'amount'        => $this->trs_amount,
                'direction'     => $this->trs_direction,
                'type'          => $this->trs_type,
                'origin'        => $this->trs_origin ?: null,
                'destination'   => $this->trs_destination ?: null,
                'expected_date' => $this->trs_expected_date ?: null,
                'description'   => $this->trs_description ?: null,
                'is_business'   => $this->trs_is_business,
                'status'        => 'pending',
            ]
        );
    }

    private function saveCredit(int $wsId): void
    {
        BankCredit::updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id' => $wsId,
                'user_id'      => auth()->id(),
                'name'         => $this->crd_name,
                'amount'       => $this->crd_amount,
                'category'     => $this->crd_category,
                'due_date'     => $this->crd_due_date ?: null,
                'notes'        => $this->crd_notes ?: null,
                'is_business'  => $this->crd_business,
                'status'       => 'pending',
            ]
        );
    }

    private function savePatrimony(int $wsId): void
    {
        BankPatrimony::updateOrCreate(
            ['id' => $this->editingId],
            [
                'workspace_id'  => $wsId,
                'user_id'       => auth()->id(),
                'type'          => $this->pat_type,
                'name'          => $this->pat_name,
                'value'         => $this->pat_value,
                'purchase_price'=> $this->pat_purchase_price ?: null,
                'purchase_date' => $this->pat_purchase_date ?: null,
                'description'   => $this->pat_description ?: null,
                'is_business'   => $this->pat_is_business,
                'status'        => 'active',
            ]
        );
    }

    /* ─── ELIMINAR ───────────────────────────────────────────────── */

    public function deleteAccount(int $id): void
    {
        BankAccount::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Conta eliminada.');
    }

    public function deleteReserve(int $id): void
    {
        BankReserve::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Reserva eliminada.');
    }

    public function deleteTransitItem(int $id): void
    {
        BankTransitItem::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo em trânsito eliminado.');
    }

    public function deleteCredit(int $id): void
    {
        BankCredit::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Crédito eliminado.');
    }

    public function deletePatrimony(int $id): void
    {
        BankPatrimony::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Ativo eliminado.');
    }

    public function confirmTransitItem(int $id): void
    {
        BankTransitItem::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)->update([
                'status'         => 'confirmed',
                'confirmed_date' => now()->toDateString(),
            ]);
        $this->dispatch('toast', text: 'Item confirmado com sucesso.');
    }

    public function markCreditReceived(int $id): void
    {
        $credit = BankCredit::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id);
        $credit->update([
            'status'          => 'received',
            'received_amount' => $credit->amount,
        ]);
        $this->dispatch('toast', text: 'Crédito marcado como recebido.');
    }

    /* ─── HELPERS ─────────────────────────────────────────────────── */

    private function resetForm(): void
    {
        $this->editingId = null;

        // Account
        $this->acc_name = $this->acc_type = $this->acc_description = '';
        $this->acc_bank_name = $this->acc_iban = $this->acc_swift = $this->acc_holder = '';
        $this->acc_color = '#6366f1';
        $this->acc_icon  = 'building-library';
        $this->acc_currency = 'EUR';
        $this->acc_balance = 0;
        $this->acc_type = 'corrente';
        $this->acc_status = 'active';
        $this->acc_is_business = false;
        $this->acc_include = true;
        $this->acc_alert_below = null;
        $this->acc_opened_at = '';

        // Transfer
        $this->tr_from_id = $this->tr_to_id = null;
        $this->tr_amount = 0;
        $this->tr_description = $this->tr_notes = '';
        $this->tr_status = 'completed';
        $this->tr_date = now()->format('Y-m-d');

        // Reserve
        $this->res_name = $this->res_description = '';
        $this->res_amount = 0;
        $this->res_target = null;
        $this->res_target_date = '';
        $this->res_color = '#10b981';
        $this->res_icon = 'banknotes';
        $this->res_is_business = false;

        // Transit
        $this->trs_name = $this->trs_origin = $this->trs_destination = $this->trs_description = '';
        $this->trs_amount = 0;
        $this->trs_direction = 'in';
        $this->trs_type = 'transfer_sent';
        $this->trs_expected_date = now()->addDays(3)->format('Y-m-d');
        $this->trs_is_business = false;

        // Credit
        $this->crd_name = $this->crd_notes = '';
        $this->crd_amount = 0;
        $this->crd_category = 'client';
        $this->crd_due_date = now()->addDays(30)->format('Y-m-d');
        $this->crd_business = false;

        // Patrimony
        $this->pat_type = 'real_estate';
        $this->pat_name = $this->pat_description = '';
        $this->pat_value = 0;
        $this->pat_purchase_price = null;
        $this->pat_purchase_date = '';
        $this->pat_is_business = false;
    }

    /* ─── RENDER ─────────────────────────────────────────────────── */

    public function render()
    {
        $wsId    = auth()->user()->current_workspace_id;
        $service = new BancoService($wsId);

        $accounts         = $service->getAccounts();
        $personalAccounts = $accounts->where('is_business', false)->where('status', '!=', 'archived');
        $businessAccounts = $accounts->where('is_business', true)->where('status', '!=', 'archived');
        $reserves         = $service->getReserves();
        $transitItems     = $service->getTransitItems();
        $credits          = $service->getCredits();
        $debts            = $service->getDebts();
        $goals            = $service->getGoals();
        $patrimony        = $service->getPatrimony();
        $transfers        = $service->getTransfers(30);
        $summary          = $service->getSummary();
        $liquidity        = $service->getLiquidity();
        $stats            = $service->getStats();
        $alerts           = $service->getAlerts();
        $distribution     = $service->getPatrimonyDistribution();
        $monthlyFlow      = $service->getMonthlyFlow(12);

        return view('livewire.banco-hub', compact(
            'accounts', 'personalAccounts', 'businessAccounts',
            'reserves', 'transitItems', 'credits', 'debts',
            'goals', 'patrimony', 'transfers', 'summary',
            'liquidity', 'stats', 'alerts', 'distribution', 'monthlyFlow'
        ));
    }
}

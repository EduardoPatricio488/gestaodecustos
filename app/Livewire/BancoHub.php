<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankCredit;
use App\Models\BankPatrimony;
use App\Models\BankReserve;
use App\Models\BankTransfer;
use App\Models\BankTransitItem;
use App\Models\Workspace;
use App\Services\BancoService;
use App\Services\BusinessAccessService;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Smalot\PdfParser\Parser;

#[Layout('components.layouts.app')]
class BancoHub extends Component
{
    use WithFileUploads, WithPagination;

    public string $activeTab = 'overview';
    public bool $showModal = false;
    public string $modalType = '';
    public ?int $editingId = null;
    public string $acc_name = '';
    public string $acc_type = 'corrente';
    public ?float $acc_balance = null;
    public $ibanProof = null;
    public string $accountEntryMode = 'manual';
    public string $ibanProofMessage = '';
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
    public ?int $res_bank_account_id = null;
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
    public ?int $trs_bank_account_id = null;
    public ?int $crd_bank_account_id = null;
    public ?int $pat_bank_account_id = null;
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
            'account' => ['acc_name' => 'required|string|max:100', 'acc_type' => 'required|string', 'acc_balance' => 'required|numeric', 'acc_currency' => 'required|string|max:10', 'acc_color' => 'required|string', 'acc_iban' => 'nullable|string|max:34', 'acc_swift' => 'nullable|string|max:11'],
            'transfer' => ['tr_from_id' => 'required|integer|different:tr_to_id', 'tr_to_id' => 'required|integer', 'tr_amount' => 'required|numeric|min:0.01', 'tr_date' => 'required|date'],
            'reserve' => ['res_name' => 'required|string|max:100', 'res_amount' => 'required|numeric|min:0', 'res_bank_account_id' => 'required|integer'],
            'transit' => ['trs_name' => 'required|string|max:100', 'trs_amount' => 'required|numeric|min:0.01', 'trs_direction' => 'required|in:in,out', 'trs_bank_account_id' => 'required|integer'],
            'credit' => ['crd_name' => 'required|string|max:100', 'crd_amount' => 'required|numeric|min:0.01', 'crd_bank_account_id' => 'required|integer'],
            'patrimony' => ['pat_name' => 'required|string|max:100', 'pat_type' => 'required|string', 'pat_value' => 'required|numeric|min:0', 'pat_bank_account_id' => 'required|integer'],
            default => [],
        };
    }

    public function mount(): void
    {
        $this->tr_date = now()->format('Y-m-d');
        $this->trs_expected_date = now()->addDays(3)->format('Y-m-d');
        $this->crd_due_date = now()->addDays(30)->format('Y-m-d');
    }

    public function openAccountModal(?int $id = null): void
    {
        $this->resetForm();
        $this->accountEntryMode = 'manual';
        $this->modalType = 'account';
        if ($id) {
            $account = BankAccount::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
            $this->editingId = $account->id;
            $this->acc_name = $account->name;
            $this->acc_type = $account->type;
            $this->acc_balance = $account->balance;
            $this->acc_currency = $account->currency ?? 'EUR';
            $this->acc_color = $account->color ?? '#6366f1';
            $this->acc_icon = $account->icon ?? 'building-library';
            $this->acc_status = $account->status ?? 'active';
            $this->acc_description = $account->description ?? '';
            $this->acc_bank_name = $account->bank_name ?? '';
            $this->acc_iban = $account->iban ?? '';
            $this->acc_swift = $account->swift ?? '';
            $this->acc_holder = $account->holder_name ?? '';
            $this->acc_is_business = (bool) $account->is_business;
            $this->acc_include = (bool) ($account->include_in_total ?? true);
            $this->acc_alert_below = $account->alert_below;
            $this->acc_opened_at = $account->opened_at ? $account->opened_at->format('Y-m-d') : '';
        }
        $this->showModal = true;
    }

    public function updatedIbanProof(): void
    {
        $this->resetValidation('ibanProof');
        $this->validate([
            'ibanProof' => 'required|file|mimes:pdf|max:5120',
        ], [
            'ibanProof.mimes' => 'Carrega um comprovativo em formato PDF.',
            'ibanProof.max' => 'O ficheiro não pode ultrapassar 5 MB.',
        ]);

        try {
            $text = (new Parser())->parseFile($this->ibanProof->getRealPath())->getText();
            $text = preg_replace('/\\s+/u', ' ', mb_strtoupper($text));

            if (! preg_match('/PT(?:\\s*[0-9]){23}/u', $text, $ibanMatch)) {
                throw new \RuntimeException('Não foi possível encontrar um IBAN português no documento.');
            }

            $iban = preg_replace('/\\s+/', '', $ibanMatch[0]);
            if (strlen($iban) !== 25) {
                throw new \RuntimeException('O IBAN extraído não tem um formato válido.');
            }

            $this->acc_iban = $iban;
            $bankCode = substr($iban, 4, 4);
            $bank = match ($bankCode) {
                '0018' => 'Santander',
                '0035' => 'Caixa Geral de Depósitos',
                '0007' => 'Novo Banco',
                '0033' => 'Millennium BCP',
                '0010' => 'Banco BPI',
                default => '',
            };

            if ($bank !== '') {
                $this->acc_bank_name = $bank;
                $this->acc_name = 'Conta '.$bank;
                $this->acc_color = $bank === 'Santander' ? '#EC0000' : $this->acc_color;
                $this->acc_icon = 'landmark';
            }

            if (preg_match('/\\b([A-Z]{4}PT[A-Z0-9]{2}(?:[A-Z0-9]{3})?)\\b/u', $text, $swiftMatch)) {
                $this->acc_swift = $swiftMatch[1];
            }

            if (preg_match('/TITULAR\\s+(.+?)(?=\\s+(?:IBAN|BIC|SWIFT|DATA|CONTA)\\b|$)/u', $text, $holderMatch)) {
                $this->acc_holder = trim($holderMatch[1]);
            }

            $this->acc_currency = 'EUR';
            $this->accountEntryMode = 'manual';
            $this->ibanProofMessage = 'Comprovativo lido. Confirma os dados e introduz o saldo atual da conta.';
            $this->dispatch('toast', variant: 'success', text: 'Comprovativo analisado. Confirma os dados da conta.');
        } catch (\Throwable $exception) {
            $this->ibanProofMessage = '';
            $this->addError('ibanProof', $exception instanceof \RuntimeException
                ? $exception->getMessage()
                : 'Não foi possível ler este PDF. Confirma que é um comprovativo de IBAN legível.');
        }
    }

    public function openTransferModal(): void
    {
        $this->resetForm();
        $this->modalType = 'transfer';
        $this->tr_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openReserveModal(?int $id = null): void
    {
        $this->resetForm();
        $this->modalType = 'reserve';
        if ($id) {
            $reserve = BankReserve::where('workspace_id', auth()->user()->current_workspace_id)
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->findOrFail($id);
            $this->editingId = $reserve->id;
            $this->res_bank_account_id = $reserve->bank_account_id;
            $this->res_name = $reserve->name;
            $this->res_amount = $reserve->amount;
            $this->res_target = $reserve->target_amount;
            $this->res_target_date = $reserve->target_date ? $reserve->target_date->format('Y-m-d') : '';
            $this->res_color = $reserve->color ?? '#10b981';
            $this->res_icon = $reserve->icon ?? 'banknotes';
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
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->findOrFail($id);
            $this->editingId = $item->id;
            $this->trs_bank_account_id = $item->bank_account_id;
            $this->trs_name = $item->name;
            $this->trs_amount = $item->amount;
            $this->trs_direction = $item->direction;
            $this->trs_type = $item->type;
            $this->trs_origin = $item->origin ?? '';
            $this->trs_destination = $item->destination ?? '';
            $this->trs_expected_date = $item->expected_date ? $item->expected_date->format('Y-m-d') : '';
            $this->trs_description = $item->description ?? '';
            $this->trs_is_business = (bool) $item->is_business;
        }
        $this->showModal = true;
    }

    public function openCreditModal(?int $id = null): void
    {
        $this->resetForm();
        $this->modalType = 'credit';
        if ($id) {
            $credit = BankCredit::where('workspace_id', auth()->user()->current_workspace_id)
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->findOrFail($id);
            $this->editingId = $credit->id;
            $this->crd_bank_account_id = $credit->bank_account_id;
            $this->crd_name = $credit->name;
            $this->crd_amount = $credit->amount;
            $this->crd_category = $credit->category ?? 'client';
            $this->crd_due_date = $credit->due_date ? $credit->due_date->format('Y-m-d') : '';
            $this->crd_notes = $credit->notes ?? '';
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
                ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
                ->findOrFail($id);
            $this->editingId = $pat->id;
            $this->pat_bank_account_id = $pat->bank_account_id;
            $this->pat_type = $pat->type;
            $this->pat_name = $pat->name;
            $this->pat_value = $pat->value;
            $this->pat_purchase_price = $pat->purchase_price;
            $this->pat_purchase_date = $pat->purchase_date ? $pat->purchase_date->format('Y-m-d') : '';
            $this->pat_description = $pat->description ?? '';
            $this->pat_is_business = (bool) $pat->is_business;
        }
        $this->showModal = true;
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
                BankAccount::where('workspace_id', $wsId)->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereKey($this->tr_from_id)->exists()
                && BankAccount::where('workspace_id', $wsId)->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereKey($this->tr_to_id)->exists(),
                422,
                'Conta bancária inválida.'
            );
        }
        $accountField = match ($this->modalType) { 'reserve' => $this->res_bank_account_id, 'transit' => $this->trs_bank_account_id, 'credit' => $this->crd_bank_account_id, 'patrimony' => $this->pat_bank_account_id, default => null };
        if ($accountField !== null) {
            abort_unless(BankAccount::where('workspace_id', $wsId)->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))->whereKey($accountField)->exists(), 422, 'Conta bancária inválida.');
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
        $this->persistWorkspaceRecord(BankAccount::class, $wsId, [
            'workspace_id' => $wsId,
            'user_id' => auth()->id(),
            'name' => $this->acc_name,
            'type' => $this->acc_type,
            'balance' => $this->acc_balance,
            'currency' => $this->acc_currency,
            'color' => $this->acc_color,
            'icon' => $this->acc_icon,
            'status' => $this->acc_status,
            'description' => $this->acc_description ?: null,
            'bank_name' => $this->acc_bank_name ?: null,
            'iban' => $this->acc_iban ?: null,
            'swift' => $this->acc_swift ?: null,
            'holder_name' => $this->acc_holder ?: null,
            'is_business' => $this->acc_is_business,
            'include_in_total' => $this->acc_include,
            'alert_below' => $this->acc_alert_below ?: null,
            'opened_at' => $this->acc_opened_at ?: null,
        ]);
    }

    private function saveTransfer(int $wsId): void
    {
        $transfer = $this->persistWorkspaceRecord(BankTransfer::class, $wsId, ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'from_account_id' => $this->tr_from_id, 'to_account_id' => $this->tr_to_id, 'amount' => $this->tr_amount, 'description' => $this->tr_description ?: null, 'transferred_at' => $this->tr_date, 'status' => $this->tr_status, 'notes' => $this->tr_notes ?: null]);
        if ($transfer->status === 'completed' && ! $this->editingId) {
            BankAccount::where('workspace_id', $wsId)->whereKey($this->tr_from_id)->decrement('balance', $this->tr_amount);
            BankAccount::where('workspace_id', $wsId)->whereKey($this->tr_to_id)->increment('balance', $this->tr_amount);
        }
    }

    private function saveReserve(int $wsId): void
    {
        $this->persistWorkspaceRecord(BankReserve::class, $wsId, ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'bank_account_id' => $this->res_bank_account_id, 'name' => $this->res_name, 'amount' => $this->res_amount, 'target_amount' => $this->res_target ?: null, 'target_date' => $this->res_target_date ?: null, 'color' => $this->res_color, 'icon' => $this->res_icon, 'description' => $this->res_description ?: null, 'is_business' => $this->res_is_business, 'status' => 'active']);
    }

    private function saveTransit(int $wsId): void
    {
        $this->persistWorkspaceRecord(BankTransitItem::class, $wsId, ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'bank_account_id' => $this->trs_bank_account_id, 'name' => $this->trs_name, 'amount' => $this->trs_amount, 'direction' => $this->trs_direction, 'type' => $this->trs_type, 'origin' => $this->trs_origin ?: null, 'destination' => $this->trs_destination ?: null, 'expected_date' => $this->trs_expected_date ?: null, 'description' => $this->trs_description ?: null, 'is_business' => $this->trs_is_business, 'status' => 'pending']);
    }

    private function saveCredit(int $wsId): void
    {
        $this->persistWorkspaceRecord(BankCredit::class, $wsId, ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'bank_account_id' => $this->crd_bank_account_id, 'name' => $this->crd_name, 'amount' => $this->crd_amount, 'category' => $this->crd_category, 'due_date' => $this->crd_due_date ?: null, 'notes' => $this->crd_notes ?: null, 'is_business' => $this->crd_business, 'status' => 'pending']);
    }

    private function savePatrimony(int $wsId): void
    {
        $this->persistWorkspaceRecord(BankPatrimony::class, $wsId, ['workspace_id' => $wsId, 'user_id' => auth()->id(), 'bank_account_id' => $this->pat_bank_account_id, 'type' => $this->pat_type, 'name' => $this->pat_name, 'value' => $this->pat_value, 'purchase_price' => $this->pat_purchase_price ?: null, 'purchase_date' => $this->pat_purchase_date ?: null, 'description' => $this->pat_description ?: null, 'is_business' => $this->pat_is_business, 'status' => 'active']);
    }

    private function persistWorkspaceRecord(string $modelClass, int $wsId, array $attributes): Model
    {
        $record = $this->editingId
            ? $modelClass::where('workspace_id', $wsId)->findOrFail($this->editingId)
            : new $modelClass;

        $record->fill($attributes);
        $record->save();

        return $record;
    }

    public function deleteAccount(int $id): void
    {
        $this->authorizeBankMutation();
        $user = auth()->user();
        $workspaceType = Workspace::whereKey($user->current_workspace_id)->value('type');
        $accountQuery = BankAccount::where('workspace_id', $user->current_workspace_id);
        if (! in_array($workspaceType, ['business', 'company'], true)) {
            $accountQuery->where('user_id', $user->id);
        }
        $account = $accountQuery->findOrFail($id);
        $account->delete();

        $this->dispatch('toast', variant: 'success', text: 'Conta eliminada com sucesso.');
    }

    public function deleteReserve(int $id): void
    {
        $this->authorizeBankMutation();
        BankReserve::where('workspace_id', auth()->user()->current_workspace_id)->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Reserva eliminada.');
    }

    public function deleteTransitItem(int $id): void
    {
        $this->authorizeBankMutation();
        BankTransitItem::where('workspace_id', auth()->user()->current_workspace_id)->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo em trânsito eliminado.');
    }

    public function deleteCredit(int $id): void
    {
        $this->authorizeBankMutation();
        BankCredit::where('workspace_id', auth()->user()->current_workspace_id)->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Crédito eliminado.');
    }

    public function deletePatrimony(int $id): void
    {
        $this->authorizeBankMutation();
        BankPatrimony::where('workspace_id', auth()->user()->current_workspace_id)->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Ativo eliminado.');
    }

    public function confirmTransitItem(int $id): void
    {
        $this->authorizeBankMutation();
        BankTransitItem::where('workspace_id', auth()->user()->current_workspace_id)->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))->findOrFail($id)->update(['status' => 'confirmed', 'confirmed_date' => now()->toDateString()]);
        $this->dispatch('toast', text: 'Item confirmado com sucesso.');
    }

    public function markCreditReceived(int $id): void
    {
        $this->authorizeBankMutation();
        $credit = BankCredit::where('workspace_id', auth()->user()->current_workspace_id)->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))->findOrFail($id);
        $credit->update(['status' => 'received', 'received_amount' => $credit->amount]);
        $this->dispatch('toast', text: 'Crédito marcado como recebido.');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->acc_name = '';
        $this->acc_type = 'corrente';
        $this->acc_balance = null;
        $this->acc_currency = 'EUR';
        $this->acc_color = '#6366f1';
        $this->acc_icon = 'building-library';
        $this->acc_status = 'active';
        $this->acc_description = '';
        $this->acc_bank_name = $this->acc_iban = $this->acc_swift = $this->acc_holder = '';\n        $this->ibanProof = null;\n        $this->accountEntryMode = 'manual';\n        $this->ibanProofMessage = '';
        $this->acc_is_business = false;
        $this->acc_include = true;
        $this->acc_alert_below = null;
        $this->acc_opened_at = '';
        $this->tr_from_id = $this->tr_to_id = null;
        $this->tr_amount = 0;
        $this->tr_description = $this->tr_notes = '';
        $this->tr_status = 'completed';
        $this->tr_date = now()->format('Y-m-d');
        $this->res_bank_account_id = null;
        $this->res_name = $this->res_description = '';
        $this->res_amount = 0;
        $this->res_target = null;
        $this->res_target_date = '';
        $this->res_color = '#10b981';
        $this->res_icon = 'banknotes';
        $this->res_is_business = false;
        $this->trs_name = $this->trs_origin = $this->trs_destination = $this->trs_description = '';
        $this->trs_amount = 0;
        $this->trs_direction = 'in';
        $this->trs_type = 'transfer_sent';
        $this->trs_expected_date = now()->addDays(3)->format('Y-m-d');
        $this->trs_is_business = false;
        $this->trs_bank_account_id = $this->crd_bank_account_id = $this->pat_bank_account_id = null;
        $this->crd_name = $this->crd_notes = '';
        $this->crd_amount = 0;
        $this->crd_category = 'client';
        $this->crd_due_date = now()->addDays(30)->format('Y-m-d');
        $this->crd_business = false;
        $this->pat_type = 'real_estate';
        $this->pat_name = $this->pat_description = '';
        $this->pat_value = 0;
        $this->pat_purchase_price = null;
        $this->pat_purchase_date = '';
        $this->pat_is_business = false;
        $this->modalType = '';
    }

    public function render()
    {
        $user = auth()->user();
        $wsId = $user->current_workspace_id;
        $service = new BancoService($wsId, $user->id);
        $overviewService = new BancoService($wsId, $user->id, $this->accountFilter === 'all' ? null : $this->accountFilter);
        $allAccounts = $service->getAccounts();
        $bankFilters = $allAccounts->pluck('bank_name')->filter()->unique()->sort()->values();
        $accounts = $service->getAccounts();
        if ($user->currentWorkspace?->type === 'personal') {
            $accounts = $accounts->where('user_id', $user->id)->values();
        }
        $personalAccounts = $accounts->where('is_business', false)->where('status', '!=', 'archived');
        $businessAccounts = $accounts->where('is_business', true)->where('status', '!=', 'archived');
        $reserves = $service->getReserves();
        $transitItems = $service->getTransitItems();
        $credits = $service->getCredits();
        $debts = $service->getDebts();
        $goals = $service->getGoals();
        $patrimony = $service->getPatrimony();
        $transfers = $service->getTransfers(30);
        $summary = $overviewService->getSummary();
        $liquidity = $overviewService->getLiquidity();
        $stats = $overviewService->getStats();
        $alerts = $overviewService->getAlerts();
        $distribution = $overviewService->getPatrimonyDistribution();
        $monthlyFlow = $overviewService->getMonthlyFlow(12);
        return view('livewire.banco-hub', compact('accounts', 'bankFilters', 'personalAccounts', 'businessAccounts', 'reserves', 'transitItems', 'credits', 'debts', 'goals', 'patrimony', 'transfers', 'summary', 'liquidity', 'stats', 'alerts', 'distribution', 'monthlyFlow'));
    }
}

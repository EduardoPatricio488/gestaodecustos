<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankTransfer;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Services\BusinessAccessService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BankTransactionsHub extends Component
{
    public bool $showEditModal = false;
    public ?int $editingMovementId = null;
    public string $editingMovementType = '';
    public string $editDescription = '';
    public string $editAmount = '';
    public string $editDate = '';
    public ?int $editBankAccountId = null;
    public ?int $editFromAccountId = null;
    public ?int $editToAccountId = null;
    public string $editRecipient = '';
    public string $editDirection = 'out';
    public string $editChannel = 'bank';
    public string $editStatus = 'completed';
    public ?int $editCategoryId = null;
    public bool $canConvertTransferToExpense = false;
    public bool $showConvertExpenseOptions = false;

    private function authorizeMutation(): void
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;
        if ($workspace && in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('manage_financials', $user, $workspace);
        }
    }

    private function scopedQuery(string $model)
    {
        $user = auth()->user();

        return $model::query()
            ->where('workspace_id', $user->current_workspace_id)
            ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id));
    }

    public function openNewTransfer(): void
    {
        $this->authorizeMutation();
        $this->resetValidation();
        $this->editingMovementId = null;
        $this->editingMovementType = 'transfer';
        $this->canConvertTransferToExpense = false;
        $this->showConvertExpenseOptions = false;
        $this->editCategoryId = null;
        $this->editDescription = '';
        $this->editAmount = '';
        $this->editDate = now()->format('Y-m-d');
        $this->editFromAccountId = null;
        $this->editToAccountId = null;
        $this->editRecipient = '';
        $this->editDirection = 'out';
        $this->editStatus = 'completed';
        $this->showEditModal = true;
    }

    public function editMovement(string $key): void
    {
        $this->authorizeMutation();
        [$type, $id] = array_pad(explode('-', $key, 2), 2, null);
        abort_unless(in_array($type, ['transfer', 'income', 'expense'], true) && ctype_digit((string) $id), 404);

        $this->resetValidation();
        $this->editingMovementType = $type;
        $this->editingMovementId = (int) $id;
        $this->showConvertExpenseOptions = false;

        if ($type === 'transfer') {
            $item = $this->scopedQuery(BankTransfer::class)->findOrFail($id);
            $this->editDescription = (string) ($item->description ?? '');
            $this->editAmount = (string) $item->amount;
            $this->editDate = $item->transferred_at?->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->editDirection = (string) ($item->direction ?? 'out');
            $this->editFromAccountId = $item->from_account_id ? (int) $item->from_account_id : null;
            $this->editToAccountId = $item->to_account_id ? (int) $item->to_account_id : null;
            $this->editRecipient = (string) ($item->recipient ?: ($this->editDirection === 'in' ? $item->fromAccount?->name : $item->toAccount?->name) ?? '');
            $this->editStatus = (string) $item->status;
            $this->canConvertTransferToExpense = ! is_null($item->bank_statement_import_id);
            $this->editCategoryId = null;
        } elseif ($type === 'income') {
            $item = $this->scopedQuery(Income::class)->where(fn ($q) => $q->whereNotNull('bank_account_id')->orWhereNotNull('bank_statement_import_id'))
                ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
                ->findOrFail($id);
            $this->editDescription = (string) ($item->description ?? '');
            $this->editAmount = (string) $item->amount;
            $this->editDate = $item->received_at?->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->editBankAccountId = (int) $item->bank_account_id;
            $this->editChannel = str_contains(mb_strtolower((string) $item->source), 'mb') || str_contains(mb_strtolower((string) $item->description), 'mb way') ? 'mbway' : 'bank';
        } else {
            $item = $this->scopedQuery(Expense::class)->where(fn ($q) => $q->whereNotNull('bank_account_id')->orWhereNotNull('bank_statement_import_id')->orWhereNotNull('metadata->import_id'))->findOrFail($id);
            $this->editDescription = (string) ($item->description ?? '');
            $this->editAmount = (string) ((float) $item->amount_paid > 0 ? $item->amount_paid : $item->amount);
            $this->editDate = $item->spent_at?->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->editBankAccountId = (int) $item->bank_account_id;
            $metadata = is_array($item->metadata) ? $item->metadata : [];
            $method = mb_strtolower((string) ($metadata['payment_method'] ?? $metadata['payment_type'] ?? ''));
            $this->editChannel = str_contains($method, 'mb') || str_contains(mb_strtolower((string) $item->description), 'mb way') ? 'mbway' : 'bank';
        }

        $this->showEditModal = true;
    }

    public function convertTransferToExpense(): void
    {
        $this->authorizeMutation();

        if ($this->editingMovementType !== 'transfer' || ! $this->editingMovementId || ! $this->canConvertTransferToExpense) {
            abort(403);
        }

        $this->validate([
            'editCategoryId' => 'required|integer',
        ], [
            'editCategoryId.required' => 'Seleciona a categoria da despesa.',
        ]);

        $user = auth()->user();
        $workspaceId = (int) $user->current_workspace_id;

        DB::transaction(function () use ($user, $workspaceId): void {
            $transfer = $this->scopedQuery(BankTransfer::class)
                ->lockForUpdate()
                ->findOrFail($this->editingMovementId);

            if (! $transfer->bank_statement_import_id) {
                throw new \RuntimeException('Só podes converter movimentos importados de extratos bancários.');
            }

            $categoryQuery = Category::query()
                ->where('workspace_id', $workspaceId)
                ->whereKey($this->editCategoryId);

            if ($user->currentWorkspace?->type === 'personal') {
                $categoryQuery->where('user_id', $user->id);
            }

            $category = $categoryQuery->firstOrFail();

            // Se a transferência tiver alterado saldos de contas internas, repõe o saldo antes de a remover.
            if ($transfer->status === 'completed') {
                if (($transfer->direction ?? 'out') === 'in' && $transfer->to_account_id) {
                    BankAccount::whereKey($transfer->to_account_id)->decrement('balance', $transfer->amount);
                } elseif ($transfer->from_account_id) {
                    BankAccount::whereKey($transfer->from_account_id)->increment('balance', $transfer->amount);
                }
            }

            $amount = round((float) $transfer->amount, 2);
            Expense::create([
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,
                'category_id' => $category->id,
                'bank_statement_import_id' => $transfer->bank_statement_import_id,
                'title' => mb_substr((string) ($transfer->description ?: $transfer->recipient ?: 'Despesa importada'), 0, 100),
                'amount' => $amount,
                'amount_paid' => $amount,
                'status' => 'paid',
                'description' => $transfer->description ?: $transfer->recipient,
                'spent_at' => $transfer->transferred_at,
                'metadata' => [
                    'import_id' => $transfer->bank_statement_import_id,
                    'movement_type' => 'expense',
                    'converted_from_bank_transfer_id' => $transfer->id,
                ],
            ]);

            $transfer->delete();
        });

        $this->showEditModal = false;
        $this->dispatch('toast', variant: 'success', text: 'Movimento convertido em despesa com sucesso.');
    }

    public function saveMovement(): void
    {
        $this->authorizeMutation();
        $this->validate([
            'editDescription' => 'nullable|string|max:255',
            'editAmount' => 'required|numeric|min:0.01',
            'editDate' => 'required|date',
            'editChannel' => 'required|in:bank,mbway',
        ]);

        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;

        DB::transaction(function () use ($user, $workspaceId): void {
            if ($this->editingMovementType === 'transfer') {
                $accountField = $this->editDirection === 'in' ? 'editToAccountId' : 'editFromAccountId';
                $this->validate([
                    $accountField => 'required|integer',
                    'editRecipient' => 'required|string|max:255',
                    'editDirection' => 'required|in:in,out',
                    'editStatus' => 'required|in:completed,cancelled',
                ]);

                $account = BankAccount::where('workspace_id', $workspaceId)
                    ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                    ->findOrFail($this->editDirection === 'in' ? $this->editToAccountId : $this->editFromAccountId);

                if ($this->editingMovementId) {
                    $transfer = $this->scopedQuery(BankTransfer::class)->lockForUpdate()->findOrFail($this->editingMovementId);

                    if ($transfer->status === 'completed') {
                        if (($transfer->direction ?? 'out') === 'in' && $transfer->to_account_id) {
                            BankAccount::whereKey($transfer->to_account_id)->decrement('balance', $transfer->amount);
                        } elseif ($transfer->from_account_id) {
                            BankAccount::whereKey($transfer->from_account_id)->increment('balance', $transfer->amount);
                        }
                    }

                    $transfer->update([
                        'direction' => $this->editDirection,
                        'from_account_id' => $this->editDirection === 'out' ? $account->id : null,
                        'to_account_id' => $this->editDirection === 'in' ? $account->id : null,
                        'recipient' => trim($this->editRecipient),
                        'amount' => (float) $this->editAmount,
                        'currency' => $account->currency ?? 'EUR',
                        'description' => $this->editDescription ?: null,
                        'transferred_at' => $this->editDate,
                        'status' => $this->editStatus,
                    ]);
                } else {
                    BankTransfer::create([
                        'workspace_id' => $workspaceId,
                        'user_id' => $user->id,
                        'direction' => $this->editDirection,
                        'from_account_id' => $this->editDirection === 'out' ? $account->id : null,
                        'to_account_id' => $this->editDirection === 'in' ? $account->id : null,
                        'recipient' => trim($this->editRecipient),
                        'amount' => (float) $this->editAmount,
                        'currency' => $account->currency ?? 'EUR',
                        'description' => $this->editDescription ?: null,
                        'transferred_at' => $this->editDate,
                        'status' => $this->editStatus,
                    ]);
                }

                if ($this->editStatus === 'completed') {
                    $this->editDirection === 'in'
                        ? $account->increment('balance', (float) $this->editAmount)
                        : $account->decrement('balance', (float) $this->editAmount);
                }

                return;
            }

            $account = BankAccount::where('workspace_id', $workspaceId)
                ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->findOrFail($this->editBankAccountId);

            if ($this->editingMovementType === 'income') {
                $income = $this->scopedQuery(Income::class)->whereNotNull('bank_account_id')
                    ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
                    ->findOrFail($this->editingMovementId);
                $income->update([
                    'bank_account_id' => $account->id,
                    'description' => $this->editDescription ?: ($this->editChannel === 'mbway' ? 'Recebimento MB WAY' : 'Recebimento'),
                    'amount' => (float) $this->editAmount,
                    'received_at' => $this->editDate,
                    'source' => $this->editChannel === 'mbway' ? 'MB WAY' : ($income->source ?: 'Transferência bancária'),
                ]);
            } else {
                $expense = $this->scopedQuery(Expense::class)->whereNotNull('bank_account_id')->findOrFail($this->editingMovementId);
                $metadata = is_array($expense->metadata) ? $expense->metadata : [];
                $metadata['payment_method'] = $this->editChannel === 'mbway' ? 'MB WAY' : 'Transferência bancária';
                $expense->update([
                    'bank_account_id' => $account->id,
                    'description' => $this->editDescription ?: ($this->editChannel === 'mbway' ? 'Pagamento MB WAY' : 'Pagamento'),
                    'amount' => (float) $this->editAmount,
                    'amount_paid' => (float) $this->editAmount,
                    'spent_at' => $this->editDate,
                    'metadata' => $metadata,
                ]);
            }
        });

        $this->showEditModal = false;
        $this->dispatch('toast', variant: 'success', text: $this->editingMovementId ? 'Movimento atualizado com sucesso.' : 'Transferência registada com sucesso.');
    }

    public function deleteMovement(string $key): void
    {
        $this->authorizeMutation();
        [$type, $id] = array_pad(explode('-', $key, 2), 2, null);
        abort_unless(in_array($type, ['transfer', 'income', 'expense'], true) && ctype_digit((string) $id), 404);

        DB::transaction(function () use ($type, $id): void {
            if ($type === 'transfer') {
                $transfer = $this->scopedQuery(BankTransfer::class)->lockForUpdate()->findOrFail($id);
                if ($transfer->status === 'completed') {
                    if (($transfer->direction ?? 'out') === 'in' && $transfer->to_account_id) {
                        BankAccount::whereKey($transfer->to_account_id)->decrement('balance', $transfer->amount);
                    } elseif ($transfer->from_account_id) {
                        BankAccount::whereKey($transfer->from_account_id)->increment('balance', $transfer->amount);
                    }
                }
                $transfer->delete();
            } elseif ($type === 'income') {
                $this->scopedQuery(Income::class)->where(fn ($q) => $q->whereNotNull('bank_account_id')->orWhereNotNull('bank_statement_import_id'))
                    ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
                    ->findOrFail($id)->delete();
            } else {
                $this->scopedQuery(Expense::class)->where(fn ($q) => $q->whereNotNull('bank_account_id')->orWhereNotNull('bank_statement_import_id')->orWhereNotNull('metadata->import_id'))->findOrFail($id)->delete();
            }
        });

        $this->dispatch('toast', variant: 'success', text: 'Movimento eliminado com sucesso.');
    }

    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;
        $personal = $user->currentWorkspace?->type === 'personal';
        $scope = fn ($query) => $query->where('workspace_id', $workspaceId)
            ->when($personal, fn ($q) => $q->where('user_id', $user->id));

        $transfers = $scope(BankTransfer::query()->with(['fromAccount', 'toAccount']))
            ->latest('transferred_at')->get()->map(fn ($item) => [
                'id' => 'transfer-'.$item->id, 'date' => $item->transferred_at,
                'kind' => 'transfer', 'label' => $item->description ?: 'Transferência entre contas',
                'category' => $item->category,
                'account' => (($item->direction ?? 'out') === 'in' ? $item->toAccount?->name : $item->fromAccount?->name) ?? 'Conta bancária',
                'counterpart' => $item->recipient ?: 'Destinatário não indicado',
                'amount' => (float) $item->amount, 'direction' => ($item->direction ?? 'out') === 'in' ? 'in' : 'out',
                'channel' => $item->category === 'Imposto do Selo' ? 'Imposto do Selo' : 'Transferência bancária',
                'status' => mb_strtolower((string) $item->status) === 'cancelled' ? 'Cancelada' : 'Concluído',
            ]);

        $incomes = $scope(Income::query()->with('bankAccount'))
            ->where(fn ($q) => $q->whereNotNull('bank_account_id')->orWhereNotNull('bank_statement_import_id'))
            ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
            ->latest('received_at')->get()->map(function ($item) {
                $source = mb_strtolower((string) ($item->source ?? ''));
                $description = mb_strtolower((string) $item->description);
                $mbway = str_contains($source, 'mbway') || str_contains($source, 'mb way') || str_contains($description, 'mbway') || str_contains($description, 'mb way');
                return [
                    'id' => 'income-'.$item->id, 'date' => $item->received_at,
                    'kind' => $mbway ? 'mbway' : 'income',
                    'label' => $item->description ?: ($mbway ? 'Recebimento MB WAY' : 'Recebimento'),
                    'account' => $item->bankAccount?->name ?? 'Conta bancária',
                    'counterpart' => 'Entrada de dinheiro', 'amount' => (float) $item->amount,
                    'direction' => 'in', 'channel' => $mbway ? 'MB WAY' : 'Recebimento bancário',
                    'status' => 'Concluído',
                ];
            });

        $expenses = $scope(Expense::query()->with('bankAccount'))
            ->where(fn ($q) => $q->whereNotNull('bank_account_id')->orWhereNotNull('bank_statement_import_id')->orWhereNotNull('metadata->import_id'))
            ->latest('spent_at')->get()->map(function ($item) {
                $metadata = is_array($item->metadata) ? $item->metadata : [];
                $method = mb_strtolower((string) ($metadata['payment_method'] ?? $metadata['payment_type'] ?? ''));
                $description = mb_strtolower((string) $item->description);
                $mbway = str_contains($method, 'mbway') || str_contains($method, 'mb way') || str_contains($description, 'mbway') || str_contains($description, 'mb way');
                return [
                    'id' => 'expense-'.$item->id, 'date' => $item->spent_at,
                    'kind' => $mbway ? 'mbway' : 'expense',
                    'label' => $item->description ?: ($mbway ? 'Pagamento MB WAY' : 'Pagamento'),
                    'account' => $item->bankAccount?->name ?? 'Conta bancária',
                    'counterpart' => 'Saída de dinheiro',
                    'amount' => (float) $item->amount_paid > 0 ? (float) $item->amount_paid : (float) $item->amount,
                    'direction' => 'out', 'channel' => $mbway ? 'MB WAY' : 'Pagamento bancário',
                    'status' => mb_strtolower((string) $item->status) === 'cancelled' ? 'Cancelada' : 'Concluído',
                ];
            });

        $movements = $transfers->concat($incomes)->concat($expenses)
            ->sortByDesc(fn ($item) => $item['date']?->timestamp ?? 0)->values();

        return view('livewire.bank-transactions-hub', [
            'movements' => $movements,
            'totalIn' => $incomes->sum('amount') + $transfers->where('direction', 'in')->sum('amount'),
            'totalOut' => $expenses->sum(fn ($item) => (float) $item['amount']) + $transfers->where('direction', 'out')->sum('amount'),
            'totalTransfers' => $transfers->reject(fn ($item) => ($item['category'] ?? null) === 'Imposto do Selo')->sum('amount'),
            'totalStampDuty' => $transfers->filter(fn ($item) => ($item['category'] ?? null) === 'Imposto do Selo')->sum('amount'),
            'movementCount' => $movements->count(),
            'bankAccounts' => $scope(BankAccount::query())->orderBy('name')->get(),
            'expenseCategories' => Category::query()->where('workspace_id', $workspaceId)
                ->when($personal, fn ($q) => $q->where('user_id', $user->id))
                ->where('hidden_from_sidebar', false)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}

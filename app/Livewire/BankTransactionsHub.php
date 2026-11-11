<?php

namespace App\Livewire;

use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\Income;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BankTransactionsHub extends Component
{
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
                'account' => $item->fromAccount?->name ?? 'Conta de origem',
                'counterpart' => $item->toAccount?->name ?? 'Conta de destino',
                'amount' => (float) $item->amount, 'direction' => 'internal',
                'channel' => 'Transferência bancária', 'status' => $item->status,
            ]);

        $incomes = $scope(Income::query()->with('bankAccount'))->whereNotNull('bank_account_id')
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

        $expenses = $scope(Expense::query()->with('bankAccount'))->whereNotNull('bank_account_id')
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
                    'status' => $item->status ?: 'Concluído',
                ];
            });

        $movements = $transfers->concat($incomes)->concat($expenses)
            ->sortByDesc(fn ($item) => $item['date']?->timestamp ?? 0)->values();

        return view('livewire.bank-transactions-hub', [
            'movements' => $movements,
            'totalIn' => $incomes->sum('amount'),
            'totalOut' => $expenses->sum(fn ($item) => (float) $item['amount']),
            'totalTransfers' => $transfers->sum('amount'),
            'movementCount' => $movements->count(),
        ]);
    }
}

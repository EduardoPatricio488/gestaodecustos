<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class DebtHub extends Component
{
    // Propriedades do Formulário - Inicializadas para evitar erros de tipo
    public string $type = 'owe';

    public string $person_name = '';

    public $amount = '';

    public string $description = '';

    public string $due_at = '';

    public ?int $editingId = null;

    // Liquidação: escolha de destino (banco ou dinheiro físico)
    public ?int $settlingId = null;

    public $settleBankAccountId = '';

    public $settlingAmount = 0;

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::where('workspace_id', auth()->user()->current_workspace_id)
            ->orderBy('name')
            ->get();
    }

    public function settlingDebt(): ?Debt
    {
        if (! $this->settlingId) {
            return null;
        }

        return Debt::where('workspace_id', auth()->user()->current_workspace_id)->find($this->settlingId);
    }

    public function openCreateModal()
    {
        $this->reset(['person_name', 'amount', 'description', 'due_at', 'editingId']);
        $this->type = 'owe';
        $this->dispatch('open-debt-modal');
    }

    public function save(): void
    {
        $this->validate([
            'person_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:owe,owed',
            'due_at' => 'nullable|date',
        ]);

        $workspaceId = auth()->user()->current_workspace_id;

        Debt::updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => auth()->id(),
                'workspace_id' => $workspaceId,
                'type' => $this->type,
                'person_name' => $this->person_name,
                'amount' => (float) $this->amount,
                'description' => $this->description,
                'due_at' => $this->due_at ?: null,
                'is_paid' => false,
            ]
        );

        $user = auth()->user();

        if (! $this->editingId && method_exists($user, 'awardXp')) {
            $user->awardXp(30, 'protocolo validado');
            $this->dispatch('toast', variant: 'success', text: $user->xpToastText(30, 'protocolo validado'));
        } else {
            $this->dispatch('toast', text: 'Registo atualizado!');
        }

        $this->dispatch('close-debt-modal');
        $this->reset(['person_name', 'amount', 'description', 'due_at', 'editingId']);
    }

    public function edit(int $id)
    {
        $debt = Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        $this->editingId = $debt->id;
        $this->type = $debt->type;
        $this->person_name = $debt->person_name;
        $this->amount = $debt->amount;
        $this->description = $debt->description ?? '';
        $this->due_at = $debt->due_at ? Carbon::parse($debt->due_at)->format('Y-m-d') : '';

        $this->dispatch('open-debt-modal');
    }

    public function openSettleModal(int $id)
    {
        $debt = Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        if ($debt->is_paid) {
            $this->reopenDebt($id);

            return;
        }

        $this->settlingId = $id;
        $this->settleBankAccountId = '';
        $this->settlingAmount = (float) $debt->amount;
        $this->dispatch('open-settle-modal');
    }

    public function confirmSettle(): void
    {
        $debt = $this->settlingDebt();

        if (! $debt) {
            return;
        }

        $workspaceId = auth()->user()->current_workspace_id;
        $bankAccountId = $this->settleBankAccountId ?: null;

        // Só bloqueia por saldo insuficiente quando é um pagamento (saída de dinheiro)
        if ($debt->type === 'owe' && $bankAccountId) {
            $account = BankAccount::where('workspace_id', $workspaceId)->find($bankAccountId);

            if ($account && (float) $debt->amount > (float) $account->current_balance) {
                $this->dispatch('toast', variant: 'error', text: 'Saldo insuficiente em "'.$account->name.'": disponível '.number_format($account->current_balance, 2, ',', '.').'€.');

                return;
            }
        }

        if ($debt->type === 'owe') {
            $expense = Expense::create([
                'user_id' => auth()->id(),
                'workspace_id' => $workspaceId,
                'bank_account_id' => $bankAccountId,
                'description' => 'Pagamento a '.$debt->person_name.($debt->description ? ' - '.$debt->description : ''),
                'amount' => $debt->amount,
                'spent_at' => now(),
                'is_company' => false,
            ]);
            $debt->expense_id = $expense->id;
        } else {
            $income = Income::create([
                'user_id' => auth()->id(),
                'workspace_id' => $workspaceId,
                'bank_account_id' => $bankAccountId,
                'description' => 'Recebimento de '.$debt->person_name.($debt->description ? ' - '.$debt->description : ''),
                'amount' => $debt->amount,
                'received_at' => now(),
                'type' => 'Extra',
                'source' => 'outro',
                'frequency' => 'pontual',
            ]);
            $debt->income_id = $income->id;
        }

        $debt->is_paid = true;
        $debt->save();

        $this->reset(['settlingId', 'settleBankAccountId']);
        $this->dispatch('close-settle-modal');
        $this->dispatch('toast', text: 'Liquidado e lançado em '.($debt->type === 'owe' ? 'Despesas' : 'Receitas').'!');
    }

    public function reopenDebt(int $id)
    {
        $debt = Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        if (! $debt->is_paid) {
            return;
        }

        // Reabrir: remove a despesa/receita que tinha sido lançada
        if ($debt->expense_id) {
            Expense::where('id', $debt->expense_id)->delete();
        }
        if ($debt->income_id) {
            Income::where('id', $debt->income_id)->delete();
        }

        $debt->update(['is_paid' => false, 'expense_id' => null, 'income_id' => null]);
        $this->dispatch('toast', text: 'Registo reaberto.');
    }

    public function delete(int $id)
    {
        Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo eliminado.');
    }

    /**
     * Lógica de Decoração (Extraída do Render para limpeza)
     */
    private function decorateDebt($debt)
    {
        if ($debt->due_at) {
            $due = Carbon::parse($debt->due_at);
            $debt->isOverdue = $due->isPast() && ! $due->isToday();
            $debt->isUrgent = $due->isBetween(now(), now()->addDays(7));
        } else {
            $debt->isOverdue = false;
            $debt->isUrgent = false;
        }

        return $debt;
    }

    public function render()
    {
        $wsId = auth()->user()->current_workspace_id;

        // 1. Queries Diretas (Performance: Filtrar no SQL é melhor que em PHP)
        $iOwe = Debt::where('workspace_id', $wsId)
            ->where('type', 'owe')
            ->where('is_paid', false)
            ->orderBy('due_at', 'asc')
            ->get()
            ->map(fn ($d) => $this->decorateDebt($d));

        $theyOweMe = Debt::where('workspace_id', $wsId)
            ->where('type', 'owed')
            ->where('is_paid', false)
            ->orderBy('due_at', 'asc')
            ->get()
            ->map(fn ($d) => $this->decorateDebt($d));

        $history = Debt::where('workspace_id', $wsId)
            ->where('is_paid', true)
            ->latest('updated_at')
            ->take(10)
            ->get();

        // 2. Cálculos de KPI baseados nas queries já filtradas
        $totalIOwe = $iOwe->sum('amount');
        $totalTheyOweMe = $theyOweMe->sum('amount');

        return view('livewire.debt-hub', [
            'iOwe' => $iOwe,
            'theyOweMe' => $theyOweMe,
            'history' => $history,
            'totalIOwe' => $totalIOwe,
            'totalTheyOweMe' => $totalTheyOweMe,
            'netBalance' => $totalTheyOweMe - $totalIOwe,
            'overdueCount' => $iOwe->where('isOverdue', true)->count(),
            'urgentCount' => $iOwe->where('isUrgent', true)->count(),
        ]);
    }
}

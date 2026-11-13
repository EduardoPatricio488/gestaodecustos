<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesOnboardingWorkspace;
use App\Models\BankStatementImport;
use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\Income;

class OnboardingStatement extends StatementImportHub
{
    use AuthorizesOnboardingWorkspace;

    public bool $showImportForm = false;

    public function mount(): void
    {
        $this->initializeOnboardingWorkspace();
    }

    public function openImportForm(): void
    {
        $this->authorizeOnboardingWrite();
        $this->showImportForm = true;
        $this->dispatch('onboarding-statement-import-opened');
    }

    public function updatedStatementFile(): void
    {
        $this->clearPreview();
    }

    public function generatePreview(): void
    {
        $this->authorizeOnboardingWrite();
        parent::generatePreview();
    }

    public function importStatement(): void
    {
        $this->authorizeOnboardingWrite();
        parent::importStatement();
    }

    public function render()
    {
        if ($this->showImportForm) {
            $this->authorizeOnboardingWrite();
        }

        $data = $this->showImportForm ? parent::render()->getData() : [];
        $workspaceId = (int) auth()->user()->current_workspace_id;

        $imports = BankStatementImport::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->where('transactions_imported', '>', 0)
            ->orderByDesc('created_at')
            ->get(['id', 'filename', 'created_at', 'transactions_imported']);

        $importIds = $imports->pluck('id');
        $confirmedMovements = collect();

        if ($importIds->isNotEmpty()) {
            $expenses = Expense::query()
                ->with('category:id,name')
                ->where('workspace_id', $workspaceId)
                ->where(function ($query) use ($importIds) {
                    $query->whereIn('bank_statement_import_id', $importIds)
                        ->orWhereIn('metadata->import_id', $importIds);
                })
                ->get()
                ->map(fn (Expense $expense) => [
                    'type' => (is_array($expense->metadata) && ($expense->metadata['movement_type'] ?? null) === 'debt_payment') ? 'Pagamento de dívida' : 'Despesa',
                    'date' => $expense->spent_at?->format('Y-m-d'),
                    'description' => $expense->description ?: $expense->title,
                    'amount' => (float) $expense->amount,
                    'category' => $expense->category?->name ?? 'Sem categoria',
                ]);

            $incomes = Income::query()
                ->with('category:id,name')
                ->where('workspace_id', $workspaceId)
                ->whereIn('bank_statement_import_id', $importIds)
                ->get()
                ->map(fn (Income $income) => [
                    'type' => 'Receita',
                    'date' => $income->received_at?->format('Y-m-d'),
                    'description' => $income->description,
                    'amount' => (float) $income->amount,
                    'category' => $income->category?->name ?? 'Sem categoria',
                ]);

            $transfers = BankTransfer::query()
                ->where('workspace_id', $workspaceId)
                ->where('user_id', auth()->id())
                ->whereIn('bank_statement_import_id', $importIds)
                ->get()
                ->map(fn (BankTransfer $transfer) => [
                    'type' => (($transfer->direction ?? 'out') === 'in') ? 'Transferência recebida' : 'Transferência enviada',
                    'date' => $transfer->transferred_at?->format('Y-m-d'),
                    'description' => $transfer->description,
                    'amount' => (float) $transfer->amount,
                    'category' => 'Transferências bancárias',
                ]);

            $confirmedMovements = $expenses->concat($incomes)->concat($transfers)
                ->sortByDesc('date')
                ->values();
        }

        return view('livewire.onboarding-statement', compact('data', 'confirmedMovements'));
    }
}

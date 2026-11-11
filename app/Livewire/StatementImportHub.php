<?php

namespace App\Livewire;

use App\Models\BankStatementImport;
use App\Models\CategorizationRule;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Services\BankImportService;
use App\Services\BusinessAccessService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class StatementImportHub extends Component
{
    use WithFileUploads;

    public $statementFile;

    public ?BankStatementImport $lastImport = null;

    public array $preview = [];

    public bool $previewReady = false;

    public array $selectedSignatures = [];

    public ?int $viewingImportId = null;

    public array $viewingImportMovements = [];

    public string $ruleKeyword = '';

    public ?int $ruleCategoryId = null;

    public int $rulePriority = 100;

    public function generatePreview(): void
    {
        $this->authorizeImport();
        $this->validate(['statementFile' => 'required|file|mimes:pdf,csv,txt,ofx|max:10240']);

        try {
            $service = app(BankImportService::class);
            $extension = strtolower($this->statementFile->getClientOriginalExtension());
            if ($extension === 'pdf') {
                $this->preview = $service->previewSantanderPdf($this->statementFile, (int) auth()->user()->current_workspace_id, (int) auth()->id());
            } else {
                $this->preview = $service->preview($this->statementFile);
            }
            $this->previewReady = true;
        } catch (\Throwable $exception) {
            $this->preview = [];
            $this->previewReady = false;
            $this->selectedSignatures = [];
            $this->addError('statementFile', $exception->getMessage());
            return;
        }
        $this->selectedSignatures = array_values(array_filter(array_map(
            fn (array $row) => $row['signature'] ?? null,
            $this->preview['rows'] ?? []
        )));

        if (($this->preview['transactions_total'] ?? 0) === 0) {
            $this->dispatch('toast', variant: 'warning', text: 'Não foram encontrados movimentos no ficheiro.');
        } else {
            $this->dispatch('toast', variant: 'success', text: count($this->preview['rows']).' movimentos identificados. Revê cada um antes de confirmar.');
        }
    }

    public function importStatement(): void
    {
        $this->authorizeImport();
        if (! $this->previewReady) {
            $this->dispatch('toast', variant: 'warning', text: 'Gera primeiro a pré-visualização.');

            return;
        }

        if (count($this->selectedSignatures) === 0) {
            $this->dispatch('toast', variant: 'warning', text: 'Seleciona pelo menos uma transação para importar.');

            return;
        }

        $this->validate(['statementFile' => 'required|file|mimes:pdf,csv,txt,ofx|max:10240']);

        $workspace = auth()->user()->currentWorkspace;
        $service = app(BankImportService::class);

        if (($this->preview['source_file_type'] ?? null) === 'pdf') {
            $selectedRows = array_values(array_filter(
                $this->preview['rows'] ?? [],
                fn (array $row) => in_array($row['signature'] ?? null, $this->selectedSignatures, true)
            ));
            $this->lastImport = $service->confirmPreview($workspace, (int) auth()->id(), $this->statementFile->getClientOriginalName(), $selectedRows);
        } else {
            $this->lastImport = $service->import($workspace, auth()->id(), $this->statementFile, $this->selectedSignatures);
        }

        if ($this->lastImport->status === 'completed') {
            $duplicatesSkipped = (int) ($this->lastImport->errors['duplicates_skipped'] ?? 0);
            $text = "Importadas {$this->lastImport->transactions_imported} transações!";
            if ($duplicatesSkipped > 0) {
                $text .= " {$duplicatesSkipped} duplicadas foram ignoradas.";
            }

            $this->dispatch('toast', variant: 'success', text: $text);
        } else {
            $this->dispatch('toast', variant: 'danger', text: 'Erro na importação. Verifica o formato do ficheiro.');
        }

        $this->reset('statementFile');
        $this->preview = [];
        $this->previewReady = false;
        $this->selectedSignatures = [];
    }

    public function clearPreview(): void
    {
        $this->preview = [];
        $this->previewReady = false;
        $this->selectedSignatures = [];
    }

    public function selectAllPreviewRows(): void
    {
        $this->selectedSignatures = array_values(array_filter(array_map(
            fn (array $row) => $row['signature'] ?? null,
            $this->preview['rows'] ?? []
        )));
    }

    public function clearSelectedPreviewRows(): void
    {
        $this->selectedSignatures = [];
    }

    public function viewImportMovements(int $importId): void
    {
        $this->authorizeImport();
        $workspaceId = (int) auth()->user()->current_workspace_id;
        $import = BankStatementImport::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->findOrFail($importId);

        $expenses = Expense::query()
            ->where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where(function ($query) use ($import) {
                $query->where('bank_statement_import_id', $import->id)
                    ->orWhere('metadata->import_id', $import->id);
            })
            ->get()
            ->map(fn (Expense $expense) => [
                'type' => 'Despesa',
                'date' => $expense->spent_at?->format('Y-m-d'),
                'description' => $expense->description ?: $expense->title,
                'amount' => (float) $expense->amount,
                'category' => $expense->category?->name ?? 'Sem categoria',
            ]);

        $incomes = Income::query()
            ->with('category:id,name')
            ->where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where('bank_statement_import_id', $import->id)
            ->get()
            ->map(fn (Income $income) => [
                'type' => 'Receita',
                'date' => $income->received_at?->format('Y-m-d'),
                'description' => $income->description,
                'amount' => (float) $income->amount,
                'category' => $income->category?->name ?? 'Sem categoria',
            ]);

        $this->viewingImportId = $import->id;
        $this->viewingImportMovements = $expenses->concat($incomes)
            ->sortBy('date')
            ->values()
            ->all();
    }

    public function closeImportMovements(): void
    {
        $this->viewingImportId = null;
        $this->viewingImportMovements = [];
    }

    public function deleteImport(int $importId): void
    {
        $this->authorizeImport();
        $import = BankStatementImport::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($importId);

        $deleted = app(BankImportService::class)->deleteImport($import);
        if ($this->viewingImportId === $importId) {
            $this->closeImportMovements();
        }

        $this->dispatch('toast', variant: 'success', text: "Importação eliminada: {$deleted['expenses']} despesas e {$deleted['incomes']} receitas removidas.");
    }

    public function saveCategorizationRule(): void
    {
        $this->authorizeImport();
        $this->validate([
            'ruleKeyword' => 'required|string|min:2|max:120',
            'ruleCategoryId' => 'required|integer',
            'rulePriority' => 'required|integer|min:1|max:9999',
        ]);

        $workspaceId = auth()->user()->current_workspace_id;

        $categoryExists = Category::query()
            ->where('workspace_id', $workspaceId)
            ->where('id', $this->ruleCategoryId)
            ->exists();

        if (! $categoryExists) {
            $this->dispatch('toast', variant: 'danger', text: 'Categoria inválida para esta workspace.');

            return;
        }

        CategorizationRule::create([
            'workspace_id' => $workspaceId,
            'user_id' => auth()->id(),
            'category_id' => $this->ruleCategoryId,
            'keyword' => trim($this->ruleKeyword),
            'priority' => $this->rulePriority,
            'is_active' => true,
        ]);

        $this->reset(['ruleKeyword', 'ruleCategoryId']);
        $this->rulePriority = 100;
        $this->dispatch('toast', variant: 'success', text: 'Regra de categorização guardada.');
    }

    public function toggleRule(int $ruleId): void
    {
        $this->authorizeImport();
        $rule = CategorizationRule::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($ruleId);

        $rule->update(['is_active' => ! $rule->is_active]);
        $this->dispatch('toast', variant: 'success', text: 'Estado da regra atualizado.');
    }

    public function deleteRule(int $ruleId): void
    {
        $this->authorizeImport();
        CategorizationRule::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($ruleId)
            ->delete();

        $this->dispatch('toast', variant: 'success', text: 'Regra removida.');
    }

    private function authorizeImport(): void
    {
        $workspace = auth()->user()->currentWorkspace;
        if ($workspace && in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('manage_financials', auth()->user(), $workspace);
        }
    }

    public function render()
    {
        $this->authorizeImport();
        $workspaceId = auth()->user()->current_workspace_id;

        $imports = BankStatementImport::where('workspace_id', $workspaceId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $categories = Category::query()
            ->where('workspace_id', $workspaceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $rules = CategorizationRule::query()
            ->with('category:id,name')
            ->where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->orderBy('priority')
            ->orderByDesc('id')
            ->get();

        return view('livewire.statement-import-hub', compact('imports', 'categories', 'rules'));
    }
}

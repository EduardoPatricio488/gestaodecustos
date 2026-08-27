<?php

namespace App\Livewire;

use App\Models\BankStatementImport;
use App\Models\CategorizationRule;
use App\Models\Category;
use App\Services\BankImportService;
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

    public string $ruleKeyword = '';
    public ?int $ruleCategoryId = null;
    public int $rulePriority = 100;

    public function generatePreview(): void
    {
        $this->validate(['statementFile' => 'required|file|mimes:csv,txt,ofx|max:5120']);

        $service = app(BankImportService::class);
        $this->preview = $service->preview($this->statementFile);
        $this->previewReady = true;
        $this->selectedSignatures = array_values(array_filter(array_map(
            fn (array $row) => $row['signature'] ?? null,
            $this->preview['rows'] ?? []
        )));

        if (($this->preview['expenses_total'] ?? 0) === 0) {
            $this->dispatch('toast', variant: 'warning', text: 'Nenhuma despesa negativa encontrada na pré-visualização.');
        } else {
            $this->dispatch('toast', variant: 'success', text: 'Pré-visualização gerada. Confirma para importar.');
        }
    }

    public function importStatement(): void
    {
        if (! $this->previewReady) {
            $this->dispatch('toast', variant: 'warning', text: 'Gera primeiro a pré-visualização.');

            return;
        }

        if (count($this->selectedSignatures) === 0) {
            $this->dispatch('toast', variant: 'warning', text: 'Seleciona pelo menos uma transação para importar.');

            return;
        }

        $this->validate(['statementFile' => 'required|file|mimes:csv,txt,ofx|max:5120']);

        $workspace = auth()->user()->currentWorkspace;
        $service = app(BankImportService::class);

        $this->lastImport = $service->import($workspace, auth()->id(), $this->statementFile, $this->selectedSignatures);

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

    public function saveCategorizationRule(): void
    {
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
        $rule = CategorizationRule::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($ruleId);

        $rule->update(['is_active' => ! $rule->is_active]);
        $this->dispatch('toast', variant: 'success', text: 'Estado da regra atualizado.');
    }

    public function deleteRule(int $ruleId): void
    {
        CategorizationRule::query()
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($ruleId)
            ->delete();

        $this->dispatch('toast', variant: 'success', text: 'Regra removida.');
    }

    public function render()
    {
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

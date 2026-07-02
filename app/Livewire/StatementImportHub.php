<?php

namespace App\Livewire;

use App\Models\BankStatementImport;
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

    public function importStatement(): void
    {
        $this->validate(['statementFile' => 'required|file|mimes:csv,txt|max:5120']);

        $workspace = auth()->user()->currentWorkspace;
        $service = app(BankImportService::class);

        $this->lastImport = $service->import($workspace, auth()->id(), $this->statementFile);

        if ($this->lastImport->status === 'completed') {
            $this->dispatch('toast', variant: 'success', text: "Importadas {$this->lastImport->transactions_imported} transações!");
        } else {
            $this->dispatch('toast', variant: 'danger', text: 'Erro na importação. Verifica o formato do ficheiro.');
        }

        $this->reset('statementFile');
    }

    public function render()
    {
        $imports = BankStatementImport::where('workspace_id', auth()->user()->current_workspace_id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('livewire.statement-import-hub', compact('imports'));
    }
}

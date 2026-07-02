<?php

namespace App\Livewire\Business;

use App\Models\AtInvoice;
use App\Services\AtInvoiceService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class AtInvoiceHub extends Component
{
    use WithFileUploads;

    public $importFile;
    public string $nifToValidate = '';
    public ?bool $nifValid = null;
    public ?array $lastImport = null;
    public int $vatYear;
    public int $vatQuarter = 1;

    public function mount(): void
    {
        $this->vatYear = now()->year;
        $this->vatQuarter = (int) ceil(now()->month / 3);
    }

    public function import(): void
    {
        $this->validate(['importFile' => 'required|file|mimes:csv,txt|max:10240']);

        $workspace = auth()->user()->currentWorkspace;
        $this->lastImport = app(AtInvoiceService::class)->importCsv(
            $workspace,
            auth()->id(),
            $this->importFile
        );

        $this->reset('importFile');
        $this->dispatch('toast', variant: 'success', text: "Importadas {$this->lastImport['imported']} faturas AT.");
    }

    public function validateNif(): void
    {
        $this->nifValid = app(AtInvoiceService::class)->validateNif($this->nifToValidate);
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $service = app(AtInvoiceService::class);

        return view('livewire.business.at-invoice-hub', [
            'invoices' => AtInvoice::where('workspace_id', $workspace->id)->orderByDesc('issued_at')->limit(20)->get(),
            'vatSummary' => $service->getQuarterlyVatSummary($workspace, $this->vatYear, $this->vatQuarter),
        ]);
    }
}

<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\BusinessDocument;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
class DocumentVault extends Component
{
    use WithFileUploads;

    public $search = '';
    public $categoryFilter = '';
    public $name, $category = 'Legal', $expiry_date, $file, $notes;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|in:Legal,RH,Seguros,Impostos,Outros',
        // ADICIONADO: Validação explícita de PDF e Imagens
        'file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
        'expiry_date' => 'nullable|date',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'workspace_id' => auth()->user()->current_workspace_id,
            'name' => $this->name,
            'category' => $this->category,
            'expiry_date' => $this->expiry_date,
            'notes' => $this->notes,
        ];

        if ($this->file) {
            $data['file_path'] = $this->file->store('business_vault', 'public');
        }

        BusinessDocument::updateOrCreate(['id' => $this->editingId], $data);

        $this->reset(['name', 'category', 'expiry_date', 'file', 'notes', 'editingId']);
        $this->dispatch('modal-close', name: 'document-modal');
        $this->dispatch('toast', text: 'Documento arquivado!');
    }

    public function delete($id)
    {
        $doc = BusinessDocument::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        if ($doc->file_path) { Storage::disk('public')->delete($doc->file_path); }
        $doc->delete();
        $this->dispatch('toast', text: 'Documento removido.', variant: 'warning');
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $query = $workspace->documents()
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->categoryFilter, fn($q) => $q->where('category', $this->categoryFilter));

        return view('livewire.business.document-vault', [
            'documents' => $query->latest()->get(),
            'totalDocs' => $workspace->documents()->count(),
            'expiringSoonCount' => $workspace->documents()->get()->filter(fn($doc) => $doc->isExpiringSoon())->count(),
            'expiredCount' => $workspace->documents()->get()->filter(fn($doc) => $doc->isExpired())->count(),
        ]);
    }
}

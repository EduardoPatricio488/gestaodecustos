<?php

namespace App\Livewire\Business;

use App\Models\BusinessDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class DocumentVault extends Component
{
    use WithFileUploads;

    public $search = '';

    public $categoryFilter = '';

    public $name;

    public $category = 'Legal';

    public $expiry_date;

    public $file;

    public $notes;

    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|in:Legal,RH,Seguros,Impostos,Outros',
        'file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240',
        'expiry_date' => 'nullable|date',
    ];

    public function create()
    {
        $this->reset(['name', 'category', 'expiry_date', 'file', 'editingId']);
        $this->category = 'Legal';
        $this->dispatch('modal-show', name: 'document-modal');
    }

    public function save()
    {
        $this->validate();
        $workspaceId = auth()->user()->current_workspace_id;
        $data = ['workspace_id' => $workspaceId, 'name' => $this->name, 'category' => $this->category, 'expiry_date' => $this->expiry_date, 'notes' => $this->notes];

        if ($this->file) {
            if ($this->editingId) {
                $oldDoc = BusinessDocument::where('workspace_id', $workspaceId)->findOrFail($this->editingId);
                if ($oldDoc->file_path) {
                    Storage::disk('public')->delete($oldDoc->file_path);
                }
            }
            $data['file_path'] = $this->file->store('business_vault', 'public');
        }

        BusinessDocument::updateOrCreate(['id' => $this->editingId, 'workspace_id' => $workspaceId], $data);
        $this->reset(['name', 'category', 'expiry_date', 'file', 'editingId', 'notes']);
        $this->dispatch('modal-close', name: 'document-modal');
        $this->dispatch('toast', text: 'Documento arquivado com segurança.');
    }

    public function delete(int $id)
    {
        $doc = BusinessDocument::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        if ($doc->file_path) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $doc->delete();
        $this->dispatch('toast', text: 'Documento removido do arquivo.', variant: 'warning');
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $now = Carbon::now();
        $soon = Carbon::now()->addDays(30);
        $documents = BusinessDocument::where('workspace_id', $workspaceId)->where('name', 'like', '%'.$this->search.'%')->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))->latest()->get();
        $totalDocs = BusinessDocument::where('workspace_id', $workspaceId)->count();
        $expiredCount = BusinessDocument::where('workspace_id', $workspaceId)->whereNotNull('expiry_date')->where('expiry_date', '<', $now)->count();
        $expiringSoonCount = BusinessDocument::where('workspace_id', $workspaceId)->whereNotNull('expiry_date')->whereBetween('expiry_date', [$now, $soon])->count();

        return view('livewire.business.document-vault', compact('documents', 'totalDocs', 'expiredCount', 'expiringSoonCount'));
    }
}

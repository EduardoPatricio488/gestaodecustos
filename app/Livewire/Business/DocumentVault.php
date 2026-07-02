<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use App\Models\BusinessDocument;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class DocumentVault extends Component
{
    use WithFileUploads;

    // Filtros e Pesquisa
    public $search = '';
    public $categoryFilter = '';

    // Campos do formulário
    public $name;
    public $category = 'Legal';
    public $expiry_date;
    public $file;
    public $notes;

    // Estado de Edição
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|in:Legal,RH,Seguros,Impostos,Outros',
        'file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:10240', // Máx 10MB
        'expiry_date' => 'nullable|date',
    ];

    /**
     * Limpa o formulário e abre o modal para criação
     */
    public function create()
    {
        $this->reset(['name', 'category', 'expiry_date', 'file', 'editingId']);
        $this->dispatch('modal-show', name: 'document-modal');
    }

    /**
     * Arquivar ou atualizar documento
     */
    public function save()
    {
        $this->validate();

        $workspaceId = auth()->user()->current_workspace_id;

        $data = [
            'workspace_id' => $workspaceId,
            'name' => $this->name,
            'category' => $this->category,
            'expiry_date' => $this->expiry_date,
        ];

        // Se houver um novo ficheiro, faz o upload e apaga o antigo se estiver a editar
        if ($this->file) {
            if ($this->editingId) {
                $oldDoc = BusinessDocument::find($this->editingId);
                if ($oldDoc && $oldDoc->file_path) {
                    Storage::disk('public')->delete($oldDoc->file_path);
                }
            }
            $data['file_path'] = $this->file->store('business_vault', 'public');
        }

        BusinessDocument::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        $this->reset(['name', 'category', 'expiry_date', 'file', 'editingId']);
        $this->dispatch('modal-close', name: 'document-modal');
        $this->dispatch('toast', text: 'Documento arquivado com sucesso!');
    }

    /**
     * Remover documento do cofre e apagar ficheiro físico
     */
    public function delete($id)
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $doc = BusinessDocument::where('workspace_id', $workspaceId)->findOrFail($id);

        if ($doc->file_path) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();
        $this->dispatch('toast', text: 'Documento removido do arquivo.', variant: 'warning');
    }

    /**
     * Renderização com consulta otimizada e KPIs
     */
    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $now = Carbon::now();
        $soon = Carbon::now()->addDays(30);

        // 1. Query Principal para a Lista
        $documents = BusinessDocument::where('workspace_id', $workspaceId)
            ->where('name', 'like', '%' . $this->search . '%')
            ->when($this->categoryFilter, fn($q) => $q->where('category', $this->categoryFilter))
            ->latest()
            ->get();

        // 2. Cálculos de KPIs via SQL (Mais rápido que collection filter)
        $totalDocs = BusinessDocument::where('workspace_id', $workspaceId)->count();

        $expiredCount = BusinessDocument::where('workspace_id', $workspaceId)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', $now)
            ->count();

        $expiringSoonCount = BusinessDocument::where('workspace_id', $workspaceId)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$now, $soon])
            ->count();

        return view('livewire.business.document-vault', [
            'documents' => $documents,
            'totalDocs' => $totalDocs,
            'expiredCount' => $expiredCount,
            'expiringSoonCount' => $expiringSoonCount,
        ]);
    }
}

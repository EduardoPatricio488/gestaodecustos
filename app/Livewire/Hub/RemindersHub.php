<?php

namespace App\Livewire\Hub;

use Livewire\Component;
use App\Models\Reminder;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Computed, Title, Url};
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Terminal de Lembretes - Finance Connect')]
class RemindersHub extends Component
{
    use WithPagination;

    // --- ESTADO DO FORMULÁRIO (CAMPOS DO MODELO) ---
    public $title = '';
    public $remind_at = '';
    public $priority = 'medium';
    public $frequency = 'once';
    public $category = 'finance';
    public $notes = '';

    // --- FILTROS & NAVEGAÇÃO (PROPRIEDADES EM FALTA QUE DAVAM ERRO) ---
    #[Url(as: 'q')]
    public $search = '';

    #[Url(as: 'tab')]
    public $activeTab = 'pending'; // Controla as abas: pending, history, all

    #[Url(as: 'prioridade')]
    public $filterPriority = 'all'; // Controla o seletor de prioridade

    // --- UI CONTROLS ---
    public $openModal = false; // Sincronizado com x-data no Blade
    public $editingReminderId = null;

    /**
     * ESTATÍSTICAS PARA OS CARDS DO TOPO (STATS)
     */
    #[Computed]
    public function stats()
    {
        $base = Reminder::where('workspace_id', Auth::user()->current_workspace_id);

        return [
            'total_active'    => (clone $base)->where('is_completed', false)->count(),
            'high_priority'   => (clone $base)->where('is_completed', false)->where('priority', 'high')->count(),
            'completed_today' => (clone $base)->where('is_completed', true)->whereDate('completed_at', now())->count(),
            'overdue'         => (clone $base)->where('is_completed', false)->where('remind_at', '<', now())->count(),
        ];
    }

    /**
     * MOTOR DE BUSCA E LISTAGEM DE LEMBRETES
     */
    #[Computed]
    public function reminders()
    {
        $query = Reminder::where('workspace_id', Auth::user()->current_workspace_id)
            ->when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->when($this->filterPriority !== 'all', fn($q) => $q->where('priority', $this->filterPriority));

        // Lógica de Abas
        if ($this->activeTab === 'pending') {
            $query->where('is_completed', false)->orderBy('remind_at', 'asc');
        } elseif ($this->activeTab === 'history') {
            $query->where('is_completed', true)->orderBy('completed_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    /**
     * SALVAR OU ATUALIZAR
     */
    public function saveReminder()
    {
        $this->validate([
            'title'     => 'required|min:3|max:100',
            'remind_at' => 'required',
            'priority'  => 'required|in:low,medium,high',
            'category'  => 'required',
        ]);

        $data = [
            'user_id'      => Auth::id(),
            'workspace_id' => Auth::user()->current_workspace_id,
            'title'        => $this->title,
            'remind_at'    => $this->remind_at,
            'priority'     => $this->priority,
            'frequency'    => $this->frequency,
            'category'     => $this->category,
            'notes'        => $this->notes,
        ];

        if ($this->editingReminderId) {
            Reminder::find($this->editingReminderId)->update($data);
            $this->dispatch('toast', text: 'Lembrete atualizado! ⚡');
        } else {
            Reminder::create($data);
            $this->dispatch('toast', text: 'Agendado com sucesso! 🟢');
        }

        $this->closeModal();
    }

    /**
     * INTERAÇÕES
     */
    public function toggleComplete($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->update([
            'is_completed' => !$reminder->is_completed,
            'completed_at' => !$reminder->is_completed ? now() : null
        ]);

        $this->dispatch('toast', text: $reminder->is_completed ? 'Tarefa concluída! 🎯' : 'Reativado.');
    }

    public function deleteReminder($id)
    {
        Reminder::destroy($id);
        $this->dispatch('toast', text: 'Lembrete removido.');
    }

    public function openReminderModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $reminder = Reminder::find($id);
            $this->editingReminderId = $id;
            $this->title = $reminder->title;
            $this->remind_at = $reminder->remind_at->format('Y-m-d\TH:i');
            $this->priority = $reminder->priority;
            $this->frequency = $reminder->frequency;
            $this->category = $reminder->category;
            $this->notes = $reminder->notes;
        }
        $this->openModal = true; // Abre o modal no Alpine.js
    }

    public function closeModal()
    {
        $this->openModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['title', 'remind_at', 'priority', 'frequency', 'category', 'notes', 'editingReminderId', 'openModal']);
    }

    public function render()
    {
        return view('livewire.hub.reminders');
    }
}

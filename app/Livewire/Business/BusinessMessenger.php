<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\BusinessMessage;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('components.layouts.app')]
class BusinessMessenger extends Component
{
    public $content = ''; // Conteúdo da nova mensagem
    public $activeProjectId = null; // NULL = Canal Geral, ID = Canal do Projeto
    public $search = '';

    /**
     * Enviar Mensagem
     */
    public function sendMessage()
    {
        $this->validate([
            'content' => 'required|string|max:1000',
        ]);

        BusinessMessage::create([
            'workspace_id' => auth()->user()->current_workspace_id,
            'user_id' => auth()->id(),
            'project_id' => $this->activeProjectId,
            'content' => $this->content,
        ]);

        $this->content = ''; // Limpa o campo

        // Gamificação: Bónus por comunicação ativa (opcional)
        if (method_exists(auth()->user(), 'addXp')) {
            auth()->user()->addXp(10);
        }

        // Faz o scroll automático no JS (disparado via evento)
        $this->dispatch('message-sent');
    }

    /**
     * Mudar de Canal (Geral ou Projeto específico)
     */
    public function selectChannel($id = null)
    {
        $this->activeProjectId = $id;
        $this->reset('search');
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;

        // 1. Lista de Canais (Projetos Ativos)
        $channels = $workspace->projects()->where('status', 'em_curso')->get();

        // 2. Query de Mensagens do Canal Ativo
        $messages = $workspace->messages()
            ->with('user')
            ->where('project_id', $this->activeProjectId)
            ->when($this->search, fn($q) => $q->where('content', 'like', '%' . $this->search . '%'))
            ->latest()
            ->take(50)
            ->get()
            ->reverse(); // Mostra as mais antigas em cima e recentes em baixo

        return view('livewire.business.business-messenger', [
            'messages' => $messages,
            'channels' => $channels,
            'activeChannelName' => $this->activeProjectId
                ? $channels->find($this->activeProjectId)->name
                : 'Mural Geral da Empresa'
        ]);
    }
}

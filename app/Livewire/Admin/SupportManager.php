<?php

namespace App\Livewire\Admin;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SupportManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'open'; // Padrão: ver apenas abertos

    // Propriedades para resposta rápida
    public $activeTicketId;
    public $replyMessage;

    /**
     * Define o ticket que o admin está a responder no momento.
     * Carrega a relação com o workspace para identificar o contexto.
     */
    public function selectTicket($id)
    {
        $this->activeTicketId = $id;
        $this->replyMessage = '';
    }

    /**
     * Envia uma resposta do Administrador para o utilizador.
     */
    public function sendReply()
    {
        $this->validate(['replyMessage' => 'required|min:2']);

        $ticket = SupportTicket::findOrFail($this->activeTicketId);

        // 1. Criar a mensagem marcada como resposta oficial de administrador
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
            'is_admin_reply' => true,
        ]);

        // 2. Atualizar o estado do ticket
        // 'pending' indica que o suporte respondeu e aguarda ação do utilizador
        $ticket->update(['status' => 'pending']);

        $this->replyMessage = '';
        $this->dispatch('toast', text: 'Resposta enviada com sucesso!');
    }

    /**
     * Fecha o ticket (Marca como Resolvido).
     */
    public function closeTicket($id)
    {
        SupportTicket::findOrFail($id)->update(['status' => 'closed']);
        $this->dispatch('toast', text: 'O caso foi encerrado e marcado como resolvido.');
    }

    public function render()
    {
        // IMPORTANTE: Adicionada a relação 'workspace' no eager loading
        $tickets = SupportTicket::with(['user', 'workspace', 'messages'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, function($q) {
                $q->where('subject', 'like', '%'.$this->search.'%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('workspace', fn($w) => $w->where('name', 'like', '%'.$this->search.'%'));
            })
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.admin.support-manager', [
            'tickets' => $tickets,
            // Carrega o ticket ativo com o seu utilizador e workspace de origem
            'activeTicket' => $this->activeTicketId
                ? SupportTicket::with(['messages.user', 'workspace', 'user'])->find($this->activeTicketId)
                : null
        ]);
    }
}

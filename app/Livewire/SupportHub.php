<?php

namespace App\Livewire;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SupportHub extends Component
{
    public $subject;

    public $message;

    public $priority = 'normal';

    public $activeTicket;

    public $replyMessage;

    /**
     * O contexto é sempre derivado da rota atual.
     * Não é uma propriedade pública do Livewire, evitando que o cliente
     * consiga alterar o contexto durante a hidratação.
     */
    private function isBusinessContext(): bool
    {
        return request()->routeIs('hub.business.*');
    }

    /**
     * Filtro centralizado e obrigatório para todas as operações sobre tickets.
     */
    private function getContextQuery()
    {
        $query = SupportTicket::where('user_id', auth()->id());

        if ($this->isBusinessContext()) {
            return $query->where('workspace_id', auth()->user()->current_workspace_id);
        }

        return $query->whereNull('workspace_id');
    }

    public function viewConversation($ticketId)
    {
        $this->activeTicket = $this->getContextQuery()
            ->with(['messages.user'])
            ->findOrFail($ticketId);

        $this->dispatch('open-chat-modal');
    }

    public function sendReply()
    {
        $this->validate(['replyMessage' => 'required|min:2']);

        // Nunca confiar no modelo mantido pelo cliente entre requests Livewire.
        abort_unless($this->activeTicket?->id, 404);
        $ticket = $this->getContextQuery()->findOrFail($this->activeTicket->id);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
            'is_admin_reply' => false,
        ]);

        $ticket->update(['status' => 'open']);
        $this->replyMessage = '';
        $this->activeTicket = $ticket->load('messages.user');

        $this->dispatch('toast', text: 'Mensagem enviada!');
    }

    public function openTicket()
    {
        $this->validate([
            'subject' => 'required|min:5',
            'message' => 'required|min:10',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'workspace_id' => $this->isBusinessContext() ? auth()->user()->current_workspace_id : null,
            'subject' => $this->subject,
            'priority' => $this->priority,
            'status' => 'open',
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $this->message,
            'is_admin_reply' => false,
        ]);

        $this->reset(['subject', 'message', 'priority']);
        $this->dispatch('close-ticket-modal');
        $this->dispatch('toast', text: 'Ticket criado com sucesso!');
    }

    public function closeTicket($id)
    {
        $this->getContextQuery()
            ->findOrFail($id)
            ->update(['status' => 'closed']);

        $this->dispatch('toast', text: 'Ticket encerrado.');
    }

    public function render()
    {
        return view('livewire.support-hub', [
            'myTickets' => $this->getContextQuery()
                ->withCount('messages')
                ->latest()
                ->get(),
        ]);
    }
}

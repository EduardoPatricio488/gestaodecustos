<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SupportHub extends Component
{
    // Esta propriedade guarda o estado (Empresa ou Pessoal) entre cliques
    public $isBusinessMode = false;

    // NOVO TICKET
    public $subject;
    public $message;
    public $priority = 'normal';

    // CHAT
    public $activeTicket;
    public $replyMessage;

    public function mount()
    {
        // No carregamento inicial, detetamos se é empresa
        $this->isBusinessMode = request()->routeIs('hub.business.*');
    }

    /**
     * Filtro centralizado
     */
    private function getContextQuery()
    {
        $query = SupportTicket::where('user_id', auth()->id());

        // Usamos a propriedade $this->isBusinessMode que persiste no Livewire
        if ($this->isBusinessMode) {
            return $query->where('workspace_id', auth()->user()->current_workspace_id);
        }

        return $query->whereNull('workspace_id');
    }

    public function viewConversation($ticketId)
    {
        $this->activeTicket = $this->getContextQuery()
            ->with(['messages.user'])
            ->findOrFail($ticketId);

        // Abre o modal de chat (Alpine ouve este evento via x-on:open-chat-modal.window)
        $this->dispatch('open-chat-modal');
    }

    public function sendReply()
    {
        $this->validate(['replyMessage' => 'required|min:2']);

        SupportMessage::create([
            'support_ticket_id' => $this->activeTicket->id,
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
            'is_admin_reply' => false,
        ]);

        $this->activeTicket->update(['status' => 'open']);
        $this->replyMessage = '';
        $this->activeTicket->load('messages.user');

        $this->dispatch('toast', text: 'Mensagem enviada!');
    }

    public function openTicket()
    {
        $this->validate([
            'subject' => 'required|min:5',
            'message' => 'required|min:10',
        ]);

        // ATENÇÃO AQUI:
        // Não usamos request()->routeIs, usamos a nossa variável $this->isBusinessMode
        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'workspace_id' => $this->isBusinessMode ? auth()->user()->current_workspace_id : null,
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

        // Fecha o modal de novo ticket (Alpine ouve via x-on:ticket-created.window)
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
                ->get()
        ]);
    }
}

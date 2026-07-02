<?php

namespace App\Livewire\Admin;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\SocialReport; // Certifica-te que este Model existe
use App\Models\SocialPost;   // Certifica-te que este Model existe
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.app')]
class SupportManager extends Component
{
    use WithPagination;

    // --- ESTADOS DE NAVEGAÇÃO E FILTRO ---
    public $activeTab = 'tickets'; // 'tickets' ou 'denuncias'
    public $search = '';
    public $statusFilter = 'open';

    // --- ESTADOS DE SELECÇÃO ---
    public $activeTicketId;
    public $selectedReportId;
    public $replyMessage;

    /**
     * Propriedade Computada: Retorna o Ticket selecionado
     */
    #[Computed]
    public function activeTicket()
    {
        if (!$this->activeTicketId) return null;

        return SupportTicket::with(['messages.user', 'workspace', 'user'])
            ->find($this->activeTicketId);
    }

    /**
     * Propriedade Computada: Retorna a Denúncia selecionada
     */
    #[Computed]
    public function selectedReport()
    {
        if (!$this->selectedReportId) return null;

        return SocialReport::with(['user', 'social_post.user'])
            ->find($this->selectedReportId);
    }

    /**
     * Seleciona um ticket para responder
     */
    public function selectTicket($id)
    {
        $this->activeTicketId = $id;
        $this->selectedReportId = null;
        $this->replyMessage = '';
    }

    /**
     * Seleciona uma denúncia para analisar
     */
    public function selectReport($id)
    {
        $this->selectedReportId = $id;
        $this->activeTicketId = null;
    }

    /**
     * AÇÃO: Enviar resposta ao utilizador (Ticket)
     */
    public function sendReply()
    {
        $this->validate(['replyMessage' => 'required|min:2']);

        SupportMessage::create([
            'support_ticket_id' => $this->activeTicketId,
            'user_id' => auth()->id(),
            'message' => $this->replyMessage,
            'is_admin_reply' => true,
        ]);

        SupportTicket::find($this->activeTicketId)->update(['status' => 'pending']);

        $this->replyMessage = '';
        $this->dispatch('toast', text: 'Resposta enviada com sucesso!');
    }

    /**
     * AÇÃO: Fechar Ticket
     */
    public function closeTicket($id)
    {
        SupportTicket::findOrFail($id)->update(['status' => 'closed']);
        $this->dispatch('toast', text: 'Ticket marcado como resolvido.');
    }

    /**
     * AÇÃO: Ignorar Denúncia
     */
    public function ignoreReport($reportId)
    {
        SocialReport::findOrFail($reportId)->update(['status' => 'ignored']);
        $this->selectedReportId = null;
        $this->dispatch('toast', text: 'Denúncia ignorada.');
    }

    /**
     * AÇÃO: Apagar Post Denunciado
     */
    public function deletePost($reportId)
    {
        $report = SocialReport::findOrFail($reportId);

        // Apaga o post original da rede social
        if ($report->social_post) {
            $report->social_post->delete();
        }

        // Marca a denúncia como resolvida
        $report->update(['status' => 'resolved']);

        $this->selectedReportId = null;
        $this->dispatch('toast', text: 'Post removido e denúncia encerrada. 🛡️');
    }

    public function render()
    {
        // Lista de Tickets
        $tickets = SupportTicket::with(['user', 'workspace'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, function($q) {
                $q->where('subject', 'like', '%'.$this->search.'%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'));
            })
            ->latest('updated_at')
            ->paginate(10);

        // Lista de Denúncias
        $reports = SocialReport::with(['user', 'social_post'])
            ->where('status', 'pending')
            ->when($this->search, function($q) {
                $q->where('reason', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->get();

        return view('livewire.admin.support-manager', [
            'tickets' => $tickets,
            'reports' => $reports,
        ]);
    }
}

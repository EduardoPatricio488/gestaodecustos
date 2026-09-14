<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ClientPortal extends Component
{
    public $client;

    public $subject = '';

    public $message = '';

    public $activeTicketId = null;

    public $replyMessage = '';

    public function mount($token)
    {
        $this->client = Client::findByPortalToken($token);

        abort_unless($this->client, 404);
    }

    public function sendTicket()
    {
        $this->validate([
            'subject' => 'required|string|min:5|max:150',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $admin = DB::table('workspace_user')
            ->where('workspace_id', $this->client->workspace_id)
            ->where('role', 'admin')
            ->first();

        $adminId = $admin ? $admin->user_id : null;

        $ticket = SupportTicket::create([
            'workspace_id' => $this->client->workspace_id,
            'client_id' => $this->client->id,
            'user_id' => $adminId,
            'subject' => '[PORTAL] '.$this->subject,
            'message' => $this->message,
            'status' => 'open',
            'priority' => 'high',
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $adminId,
            'message' => $this->message,
            'is_admin_reply' => false,
        ]);

        $this->reset(['subject', 'message']);
        $this->activeTicketId = $ticket->id;
        $this->dispatch('modal-close', name: 'support-modal');
        $this->dispatch('toast', variant: 'success', text: 'Mensagem enviada com sucesso.');
    }

    public function sendReply()
    {
        $this->validate([
            'replyMessage' => 'required|string|min:2|max:5000',
        ]);

        $ticket = $this->ticketForClient($this->activeTicketId);

        if (in_array($ticket->status, ['closed', 'fechado', 'resolved', 'resolvido'], true)) {
            $this->addError('replyMessage', 'Este pedido já foi encerrado. Cria um novo pedido de suporte se precisares de ajuda.');

            return;
        }

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'message' => $this->replyMessage,
            'is_admin_reply' => false,
        ]);

        $this->replyMessage = '';
        $this->dispatch('toast', variant: 'success', text: 'Resposta enviada com sucesso.');
    }

    public function setActiveTicket($id)
    {
        $this->activeTicketId = $this->ticketForClient($id)->id;
        $this->dispatch('modal-show', name: 'view-ticket-modal');
    }

    public function approveProposal($id): void
    {
        $proposal = Proposal::where('client_id', $this->client->id)
            ->where('status', 'pendente')
            ->findOrFail($id);

        $proposal->update(['status' => 'aceite']);
        $this->dispatch('toast', variant: 'success', text: 'Proposta aceite com sucesso.');
    }

    public function declineProposal($id): void
    {
        $proposal = Proposal::where('client_id', $this->client->id)
            ->where('status', 'pendente')
            ->findOrFail($id);

        $proposal->update(['status' => 'recusada']);
        $this->dispatch('toast', variant: 'success', text: 'Proposta recusada.');
    }

    private function ticketForClient($id): SupportTicket
    {
        return $this->client->supportTickets()->findOrFail($id);
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        $projects = Project::where('client_id', $this->client->id)
            ->withCount(['tasks' => fn ($q) => $q->where('status', '!=', 'concluida')])
            ->latest()
            ->get();

        $projectIds = $projects->pluck('id');

        $tickets = SupportTicket::where('client_id', $this->client->id)
            ->with(['messages' => fn ($q) => $q->latest()])
            ->latest()
            ->get();

        $invoices = Invoice::where('client_id', $this->client->id)
            ->latest()
            ->get();

        $proposals = Proposal::where('client_id', $this->client->id)
            ->where('status', 'pendente')
            ->latest()
            ->get();

        $recentActivity = $projectIds->isNotEmpty()
            ? Task::whereIn('project_id', $projectIds)
                ->where('status', 'concluida')
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->limit(5)
                ->get()
            : collect();

        $workspace = $this->client->workspace;
        $rawTaxNumber = preg_replace('/\D+/', '', (string) ($workspace?->tax_number ?? $workspace?->nif ?? ''));
        $formattedTaxNumber = $rawTaxNumber !== '' ? trim(chunk_split($rawTaxNumber, 3, ' ')) : null;

        return view('livewire.client-portal', [
            'projects' => $projects,
            'invoices' => $invoices,
            'proposals' => $proposals,
            'recentActivity' => $recentActivity,
            'tickets' => $tickets,
            'activeMessages' => $this->activeTicketId
                ? $this->ticketForClient($this->activeTicketId)->messages()->oldest()->get()
                : collect(),
            'workspace' => $workspace,
            'companyTaxNumber' => $formattedTaxNumber,
            'portalStats' => [
                'projects' => $projects->count(),
                'openTasks' => $projects->sum('tasks_count'),
                'pendingProposals' => $proposals->count(),
                'openTickets' => $tickets->whereIn('status', ['open', 'em_aberto', 'pending'])->count(),
                'invoiceTotal' => (float) $invoices->sum('total'),
            ],
        ]);
    }
}

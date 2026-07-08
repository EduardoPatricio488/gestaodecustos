<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\Task;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class ClientPortal extends Component
{
    public $client;
    public $subject = '';
    public $message = '';
    public $activeTicketId = null;
    public $replyMessage = '';

    public function mount($token)
    {
        $this->client = Client::where('portal_token', $token)->with('workspace')->firstOrFail();
    }

    public function sendTicket()
    {
        $this->validate(['subject' => 'required|min:5', 'message' => 'required|min:10']);

        // 1. Procurar o admin para evitar o erro de NOT NULL
        $admin = DB::table('workspace_user')
            ->where('workspace_id', $this->client->workspace_id)
            ->where('role', 'admin')
            ->first();

        $adminId = $admin ? $admin->user_id : auth()->id();

        // 2. Criar o Ticket
        $ticket = SupportTicket::create([
            'workspace_id' => $this->client->workspace_id,
            'client_id'    => $this->client->id,
            'user_id'      => $adminId,
            'subject'      => "[PORTAL] " . $this->subject,
            'message'      => $this->message,
            'status'       => 'open',
            'priority'     => 'high',
        ]);

        // 3. Criar a Mensagem no histórico (Passando o user_id do admin para satisfazer a BD)
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $adminId,
            'message'           => $this->message,
            'is_admin_reply'    => false // Importante: define que veio do cliente
        ]);

        $this->reset(['subject', 'message']);
        $this->dispatch('modal-close', name: 'support-modal');
        $this->dispatch('toast', variant: 'success', text: 'Mensagem enviada!');
    }

    public function sendReply()
    {
        $this->validate(['replyMessage' => 'required|min:2']);

        $ticket = SupportTicket::findOrFail($this->activeTicketId);

        // Enviar a resposta usando o user_id que o ticket já tem
        SupportMessage::create([
            'support_ticket_id' => $this->activeTicketId,
            'user_id'           => $ticket->user_id,
            'message'           => $this->replyMessage,
            'is_admin_reply'    => false
        ]);

        $this->replyMessage = '';
        $this->dispatch('toast', variant: 'success', text: 'Resposta enviada!');
    }

    public function setActiveTicket($id)
    {
        $this->activeTicketId = $id;
        $this->dispatch('modal-show', name: 'view-ticket-modal');
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        $projectIds = Project::where('client_id', $this->client->id)->pluck('id');

        return view('livewire.client-portal', [
            'projects' => Project::where('client_id', $this->client->id)->withCount(['tasks' => fn($q) => $q->where('status', '!=', 'concluida')])->get(),
            'invoices' => Invoice::where('client_id', $this->client->id)->latest()->get(),
            'proposals' => Proposal::where('client_id', $this->client->id)->where('status', 'pendente')->get(),
            'recentActivity' => Task::whereIn('project_id', $projectIds)->where('status', 'concluida')->whereNotNull('completed_at')->latest('completed_at')->limit(5)->get(),
            'tickets' => SupportTicket::where('client_id', $this->client->id)->with('messages')->latest()->get(),
            'activeMessages' => $this->activeTicketId ? SupportMessage::where('support_ticket_id', $this->activeTicketId)->oldest()->get() : collect(),
            'workspace' => $this->client->workspace,
        ]);
    }
}

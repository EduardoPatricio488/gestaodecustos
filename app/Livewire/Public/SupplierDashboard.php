<?php

namespace App\Livewire\Public;

use App\Models\Expense;
use App\Models\Supplier;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class SupplierDashboard extends Component
{
    use WithFileUploads;

    public $supplier;

    public $subject = '';

    public $message = '';

    public $activeTicketId = null;

    public $replyMessage = '';

    // Campos para submissão de fatura
    public $invoice_amount;

    public $invoice_doc;

    public $invoice_notes;

    public function mount($token)
    {
        // Garante que o fornecedor existe com o token fornecido
        $this->supplier = Supplier::where('portal_token', $token)->with('workspace')->firstOrFail();
    }

    public function sendTicket()
    {
        $this->validate(['subject' => 'required|min:5', 'message' => 'required|min:10']);

        $admin = DB::table('workspace_user')
            ->where('workspace_id', $this->supplier->workspace_id)
            ->where('role', 'admin')
            ->first();

        $adminId = $admin ? $admin->user_id : null;

        $ticket = SupportTicket::create([
            'workspace_id' => $this->supplier->workspace_id,
            'supplier_id' => $this->supplier->id,
            'user_id' => $adminId,
            'subject' => '[FORNECEDOR] '.$this->subject,
            'message' => $this->message,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $adminId,
            'message' => $this->message,
            'is_admin_reply' => false,
        ]);

        $this->reset(['subject', 'message']);
        $this->dispatch('modal-close', name: 'support-modal');
        $this->dispatch('toast', variant: 'success', text: 'Ticket aberto com sucesso!');
    }

    public function sendReply()
    {
        $this->validate(['replyMessage' => 'required|min:2']);
        $ticket = $this->ticketForSupplier($this->activeTicketId);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'message' => $this->replyMessage,
            'is_admin_reply' => false,
        ]);

        $this->replyMessage = '';
        $this->dispatch('toast', variant: 'success', text: 'Mensagem enviada!');
    }

    public function setActiveTicket($id)
    {
        $this->activeTicketId = $this->ticketForSupplier($id)->id;
        $this->dispatch('modal-show', name: 'view-ticket-modal');
    }

    private function ticketForSupplier($id): SupportTicket
    {
        return $this->supplier->supportTickets()->findOrFail($id);
    }

    public function submitInvoice()
    {
        $this->validate([
            'invoice_amount' => 'required|numeric|min:0.01',
            'invoice_doc' => 'required|file|mimes:pdf,jpg,png|max:10240',
        ]);

        session()->flash('success_upload', 'Fatura submetida com sucesso.');
        $this->reset(['invoice_amount', 'invoice_doc', 'invoice_notes']);
        $this->dispatch('modal-close', name: 'upload-invoice-modal');
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        // Carrega o histórico para a tabela (chamada 'history' no blade)
        $history = Expense::where('supplier_id', $this->supplier->id)
            ->where('workspace_id', $this->supplier->workspace_id)
            ->latest('spent_at')
            ->get();

        return view('livewire.public.supplier-dashboard', [
            'history' => $history,
            'tickets' => $this->supplier->supportTickets()->latest()->get(),
            'activeMessages' => $this->activeTicketId
                ? $this->ticketForSupplier($this->activeTicketId)->messages()->oldest()->get()
                : collect(),
            'workspace' => $this->supplier->workspace,
        ]);
    }
}

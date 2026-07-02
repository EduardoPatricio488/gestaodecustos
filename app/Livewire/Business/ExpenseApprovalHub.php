<?php

namespace App\Livewire\Business;

use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseApproval;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ExpenseApprovalHub extends Component
{
    public string $title = '';
    public $amount = '';
    public $category_id = null;
    public string $description = '';
    public string $spent_at = '';

    public function mount(): void
    {
        $this->spent_at = now()->format('Y-m-d');
    }

    public function submit(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'spent_at' => 'required|date',
        ]);

        ExpenseApproval::create([
            'workspace_id' => auth()->user()->current_workspace_id,
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'title' => $this->title,
            'description' => $this->description,
            'spent_at' => $this->spent_at,
            'status' => 'pending',
        ]);

        $this->reset(['title', 'amount', 'category_id', 'description']);
        $this->spent_at = now()->format('Y-m-d');
        $this->dispatch('toast', variant: 'success', text: 'Despesa submetida para aprovação!');
    }

    public function approve(int $id): void
    {
        $approval = ExpenseApproval::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $approval->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Expense::create([
            'user_id' => $approval->user_id,
            'workspace_id' => $approval->workspace_id,
            'category_id' => $approval->category_id,
            'title' => $approval->title,
            'amount' => $approval->amount,
            'description' => $approval->description,
            'spent_at' => $approval->spent_at,
            'is_company' => true,
        ]);

        AppNotification::create([
            'user_id' => $approval->user_id,
            'workspace_id' => $approval->workspace_id,
            'title' => 'Despesa Aprovada',
            'message' => "A tua despesa \"{$approval->title}\" foi aprovada.",
            'type' => 'success',
            'link' => route('hub.business.expense-approvals'),
        ]);

        $this->dispatch('toast', variant: 'success', text: 'Despesa aprovada e registada!');
    }

    public function reject(int $id): void
    {
        $approval = ExpenseApproval::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $approval->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AppNotification::create([
            'user_id' => $approval->user_id,
            'workspace_id' => $approval->workspace_id,
            'title' => 'Despesa Rejeitada',
            'message' => "A tua despesa \"{$approval->title}\" foi rejeitada.",
            'type' => 'danger',
            'link' => route('hub.business.expense-approvals'),
        ]);

        $this->dispatch('toast', variant: 'warning', text: 'Despesa rejeitada.');
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $isManager = auth()->user()->workspaces()
            ->where('workspaces.id', $workspaceId)
            ->wherePivot('role', 'admin')
            ->exists();

        return view('livewire.business.expense-approval-hub', [
            'pending' => ExpenseApproval::where('workspace_id', $workspaceId)
                ->where('status', 'pending')
                ->with(['user', 'category'])
                ->latest()
                ->get(),
            'history' => ExpenseApproval::where('workspace_id', $workspaceId)
                ->where('status', '!=', 'pending')
                ->with(['user', 'reviewer'])
                ->latest()
                ->limit(20)
                ->get(),
            'categories' => Category::where('workspace_id', $workspaceId)->get(),
            'isManager' => $isManager,
        ]);
    }
}

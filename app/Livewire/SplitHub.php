<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\ExpenseSplit;
use App\Models\ExpenseSplitParticipant;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SplitHub extends Component
{
    // Form fields
    public string $title = '';

    public float $totalAmount = 0;

    public string $splitType = 'equal';

    public string $spentAt = '';

    public ?int $categoryId = null;

    public string $notes = '';

    // Participant user IDs selected
    public array $selectedUsers = [];

    // Custom amounts keyed by user_id (used when splitType = custom)
    public array $customAmounts = [];

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $activeTab = 'mine'; // mine | group | settled

    public function mount(): void
    {
        $this->spentAt = now()->format('Y-m-d');
        // Auto-include the current user
        $this->selectedUsers = [auth()->id()];
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->title = '';
        $this->totalAmount = 0;
        $this->splitType = 'equal';
        $this->spentAt = now()->format('Y-m-d');
        $this->categoryId = null;
        $this->notes = '';
        $this->selectedUsers = [auth()->id()];
        $this->customAmounts = [];
        $this->editingId = null;
    }

    public function updatedTotalAmount(): void
    {
        $this->recalcEqual();
    }

    public function updatedSelectedUsers(): void
    {
        $this->recalcEqual();
    }

    private function recalcEqual(): void
    {
        if ($this->splitType !== 'equal' || empty($this->selectedUsers)) {
            return;
        }
        $share = $this->totalAmount / count($this->selectedUsers);
        foreach ($this->selectedUsers as $uid) {
            $this->customAmounts[$uid] = round($share, 2);
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'totalAmount' => 'required|numeric|min:0.01',
            'spentAt' => 'required|date',
            'selectedUsers' => 'required|array|min:1',
        ]);

        // Distribute amounts
        $amounts = [];
        if ($this->splitType === 'equal') {
            $share = round($this->totalAmount / count($this->selectedUsers), 2);
            foreach ($this->selectedUsers as $uid) {
                $amounts[$uid] = $share;
            }
        } else {
            foreach ($this->selectedUsers as $uid) {
                $amounts[$uid] = (float) ($this->customAmounts[$uid] ?? 0);
            }
        }

        $ws = auth()->user()->currentWorkspace;

        $split = ExpenseSplit::updateOrCreate(
            ['id' => $this->editingId],
            [
                'creator_user_id' => auth()->id(),
                'workspace_id' => $ws->id,
                'category_id' => $this->categoryId ?: null,
                'title' => $this->title,
                'total_amount' => $this->totalAmount,
                'split_type' => $this->splitType,
                'spent_at' => $this->spentAt,
                'notes' => $this->notes,
            ]
        );

        // Rebuild participants
        $split->participants()->delete();
        foreach ($amounts as $uid => $amt) {
            ExpenseSplitParticipant::create([
                'expense_split_id' => $split->id,
                'user_id' => $uid,
                'amount' => $amt,
                'paid' => ($uid === auth()->id()), // creator is pre-settled
                'paid_at' => ($uid === auth()->id()) ? now() : null,
            ]);
        }

        $this->closeModal();
        $this->dispatch('toast', variant: 'success', text: 'Divisão guardada!');
    }

    public function togglePaid(int $participantId): void
    {
        $p = ExpenseSplitParticipant::findOrFail($participantId);

        // Only the split creator or the participant themselves can mark as paid
        $split = $p->split;
        if ($split->creator_user_id !== auth()->id() && $p->user_id !== auth()->id()) {
            return;
        }

        $p->paid = ! $p->paid;
        $p->paid_at = $p->paid ? now() : null;
        $p->save();

        $this->dispatch('toast', text: $p->paid ? 'Marcado como pago!' : 'Desmarcado.');
    }

    public function deleteSplit(int $id): void
    {
        $split = ExpenseSplit::where('creator_user_id', auth()->id())->findOrFail($id);
        $split->delete();
        $this->dispatch('toast', text: 'Divisão eliminada.');
    }

    public function render()
    {
        $user = auth()->user();
        $ws = $user?->currentWorkspace;
        $uid = $user?->id;

        if (! $ws || ! $uid) {
            return view('livewire.split-hub', [
                'allSplits' => collect(),
                'iOwe' => collect(),
                'theyOwe' => collect(),
                'settled' => collect(),
                'totalIOwe' => 0,
                'totalTheyOwe' => 0,
                'members' => collect(),
                'categories' => collect(),
            ]);
        }

        $allSplits = ExpenseSplit::with(['participants.user', 'creator', 'category'])
            ->where('workspace_id', $ws->id)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $uid))
            ->orWhere('creator_user_id', $uid)
            ->where('workspace_id', $ws->id)
            ->latest()
            ->get()
            ->unique('id');

        // What I owe (I'm a participant, not the creator, and not paid)
        $iOwe = $allSplits->filter(fn ($s) => $s->creator_user_id !== $uid &&
            $s->participants->where('user_id', $uid)->where('paid', false)->isNotEmpty()
        );

        // What others owe me (I'm the creator, they haven't paid)
        $theyOwe = $allSplits->filter(fn ($s) => $s->creator_user_id === $uid &&
            $s->participants->where('user_id', '!=', $uid)->where('paid', false)->isNotEmpty()
        );

        // Settled
        $settled = $allSplits->filter(fn ($s) => $s->isFullySettled());

        $totalIOwe = $iOwe->sum(fn ($s) => $s->participants->where('user_id', $uid)->sum('amount'));
        $totalTheyOwe = $theyOwe->sum(fn ($s) => $s->participants->where('user_id', '!=', $uid)->where('paid', false)->sum('amount'));

        $members = $ws->users;
        $categories = Category::where('workspace_id', $ws->id)->get();

        return view('livewire.split-hub', compact(
            'allSplits', 'iOwe', 'theyOwe', 'settled',
            'totalIOwe', 'totalTheyOwe', 'members', 'categories'
        ));
    }
}

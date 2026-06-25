<?php

namespace App\Livewire;

use App\Models\Debt;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class DebtHub extends Component
{
    public string $type = 'owe';
    public string $person_name = '';
    public $amount = '';
    public string $description = '';
    public string $due_at = '';
    public ?int $editingId = null;

    public function save(): void
    {
        $this->validate([
            'person_name' => 'required|string|max:100',
            'amount'      => 'required|numeric|min:0.01',
            'type'        => 'required|in:owe,owed',
            'due_at'      => 'nullable|date',
        ]);

        $workspaceId = auth()->user()->current_workspace_id;

        Debt::updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id'      => auth()->id(),
                'workspace_id' => $workspaceId,
                'type'         => $this->type,
                'person_name'  => $this->person_name,
                'amount'       => $this->amount,
                'description'  => $this->description,
                'due_at'       => $this->due_at ?: null,
                'is_paid'      => false,
            ]
        );

        auth()->user()->addXp(30);

        $this->reset(['person_name', 'amount', 'description', 'due_at', 'editingId']);
        $this->type = 'owe';
        $this->dispatch('modal-close', name: 'add-debt-modal');
        $this->dispatch('toast', text: 'Registo guardado!');
    }

    public function edit(int $id): void
    {
        $debt = Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $this->editingId   = $debt->id;
        $this->type        = $debt->type;
        $this->person_name = $debt->person_name;
        $this->amount      = $debt->amount;
        $this->description = $debt->description ?? '';
        $this->due_at      = $debt->due_at ? Carbon::parse($debt->due_at)->format('Y-m-d') : '';
        $this->dispatch('modal-open', name: 'add-debt-modal');
    }

    public function togglePaid(int $id): void
    {
        $debt = Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $debt->update(['is_paid' => !$debt->is_paid]);
        $this->dispatch('toast', text: $debt->is_paid ? 'Liquidada!' : 'Reaberta!');
    }

    public function delete(int $id): void
    {
        Debt::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)
            ->delete();
        $this->dispatch('toast', text: 'Registo eliminado.');
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $allDebts    = Debt::where('workspace_id', $workspaceId)->latest()->get();

        $iOwe       = $allDebts->where('type', 'owe')->where('is_paid', false)->values();
        $theyOweMe  = $allDebts->where('type', 'owed')->where('is_paid', false)->values();
        $history    = $allDebts->where('is_paid', true)->take(10)->values();

        $totalIOwe        = $iOwe->sum('amount');
        $totalTheyOweMe   = $theyOweMe->sum('amount');
        $netBalance       = $totalTheyOweMe - $totalIOwe;

        // Dívidas a vencer nos próximos 7 dias
        $urgentCount = $allDebts->where('is_paid', false)->filter(
            fn($d) => $d->due_at && Carbon::parse($d->due_at)->isBetween(now(), now()->addDays(7))
        )->count();

        // Dívidas vencidas
        $overdueCount = $allDebts->where('is_paid', false)->filter(
            fn($d) => $d->due_at && Carbon::parse($d->due_at)->isPast()
        )->count();

        // Enriquecer com estado
        $enrich = fn($col) => $col->map(function ($d) {
            $d->daysLeft   = $d->due_at ? now()->diffInDays(Carbon::parse($d->due_at), false) : null;
            $d->isOverdue  = $d->due_at && Carbon::parse($d->due_at)->isPast();
            $d->isUrgent   = $d->due_at && Carbon::parse($d->due_at)->isBetween(now(), now()->addDays(7));
            return $d;
        });

        return view('livewire.debt-hub', [
            'iOwe'            => $enrich($iOwe),
            'theyOweMe'       => $enrich($theyOweMe),
            'history'         => $history,
            'totalIOwe'       => $totalIOwe,
            'totalTheyOweMe'  => $totalTheyOweMe,
            'netBalance'      => $netBalance,
            'urgentCount'     => $urgentCount,
            'overdueCount'    => $overdueCount,
        ]);
    }
}

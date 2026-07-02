<?php

namespace App\Livewire;

use App\Models\Debt;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class DebtHub extends Component
{
    // Propriedades do Formulário - Inicializadas para evitar erros de tipo
    public string $type = 'owe';
    public string $person_name = '';
    public $amount = '';
    public string $description = '';
    public string $due_at = '';
    public ?int $editingId = null;

    public function openCreateModal()
    {
        $this->reset(['person_name', 'amount', 'description', 'due_at', 'editingId']);
        $this->type = 'owe';
        $this->dispatch('open-debt-modal');
    }

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
                'amount'       => (float) $this->amount,
                'description'  => $this->description,
                'due_at'       => $this->due_at ?: null,
                'is_paid'      => false,
            ]
        );

        if (!$this->editingId && method_exists(auth()->user(), 'addXp')) {
            auth()->user()->addXp(30);
        }

        $this->dispatch('toast', text: $this->editingId ? 'Registo atualizado!' : 'Protocolo validado!');
        $this->dispatch('close-debt-modal');
        $this->reset(['person_name', 'amount', 'description', 'due_at', 'editingId']);
    }

    public function edit(int $id)
    {
        $debt = Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        $this->editingId   = $debt->id;
        $this->type        = $debt->type;
        $this->person_name = $debt->person_name;
        $this->amount      = $debt->amount;
        $this->description = $debt->description ?? '';
        $this->due_at      = $debt->due_at ? Carbon::parse($debt->due_at)->format('Y-m-d') : '';

        $this->dispatch('open-debt-modal');
    }

    public function togglePaid(int $id)
    {
        $debt = Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $debt->update(['is_paid' => !$debt->is_paid]);
        $this->dispatch('toast', text: $debt->is_paid ? 'Operação liquidada!' : 'Registo reaberto.');
    }

    public function delete(int $id)
    {
        Debt::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Registo eliminado.');
    }

    /**
     * Lógica de Decoração (Extraída do Render para limpeza)
     */
    private function decorateDebt($debt)
    {
        if ($debt->due_at) {
            $due = Carbon::parse($debt->due_at);
            $debt->isOverdue = $due->isPast() && !$due->isToday();
            $debt->isUrgent  = $due->isBetween(now(), now()->addDays(7));
        } else {
            $debt->isOverdue = false;
            $debt->isUrgent  = false;
        }
        return $debt;
    }

    public function render()
    {
        $wsId = auth()->user()->current_workspace_id;

        // 1. Queries Diretas (Performance: Filtrar no SQL é melhor que em PHP)
        $iOwe = Debt::where('workspace_id', $wsId)
            ->where('type', 'owe')
            ->where('is_paid', false)
            ->orderBy('due_at', 'asc')
            ->get()
            ->map(fn($d) => $this->decorateDebt($d));

        $theyOweMe = Debt::where('workspace_id', $wsId)
            ->where('type', 'owed')
            ->where('is_paid', false)
            ->orderBy('due_at', 'asc')
            ->get()
            ->map(fn($d) => $this->decorateDebt($d));

        $history = Debt::where('workspace_id', $wsId)
            ->where('is_paid', true)
            ->latest('updated_at')
            ->take(10)
            ->get();

        // 2. Cálculos de KPI baseados nas queries já filtradas
        $totalIOwe = $iOwe->sum('amount');
        $totalTheyOweMe = $theyOweMe->sum('amount');

        return view('livewire.debt-hub', [
            'iOwe'           => $iOwe,
            'theyOweMe'      => $theyOweMe,
            'history'        => $history,
            'totalIOwe'      => $totalIOwe,
            'totalTheyOweMe' => $totalTheyOweMe,
            'netBalance'     => $totalTheyOweMe - $totalIOwe,
            'overdueCount'   => $iOwe->where('isOverdue', true)->count(),
            'urgentCount'    => $iOwe->where('isUrgent', true)->count(),
        ]);
    }
}

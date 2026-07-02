<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Goal;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class GoalsHub extends Component
{
    // Formulário de Meta
    public string $name = '';
    public $target_amount = '';
    public $current_amount = 0;
    public $deadline = '';
    public ?int $editingGoalId = null;

    // Depósito rápido
    public ?int $depositGoalId = null;
    public $depositAmount = '';

    /**
     * Criar ou atualizar meta
     */
    public function save()
    {
        $this->validate([
            'name'           => 'required|string|max:255',
            'target_amount'  => 'required|numeric|min:1',
            'current_amount' => 'required|numeric|min:0',
            'deadline'       => 'nullable|date',
        ]);

        $workspaceId = auth()->user()->current_workspace_id;

        Goal::updateOrCreate(
            ['id' => $this->editingGoalId],
            [
                'user_id'        => auth()->id(),
                'workspace_id'   => $workspaceId,
                'name'           => $this->name,
                'target_amount'  => (float) $this->target_amount,
                'current_amount' => (float) $this->current_amount,
                'deadline'       => $this->deadline ?: null,
            ]
        );

        // FECHAR MODAL
        $this->dispatch('modal-close-goal');

        // TOAST
        $this->dispatch('toast', text: 'Meta guardada com sucesso!');

        // RESET
        $this->reset(['name', 'target_amount', 'current_amount', 'deadline', 'editingGoalId']);
    }

    /**
     * Abrir modal de criação de meta
     */
    public function openGoalModal(): void
    {
        $this->reset(['name', 'target_amount', 'current_amount', 'deadline', 'editingGoalId']);
        $this->dispatch('modal-show-goal');
    }

    /**
     * Editar meta
     */
    public function edit(int $id): void
    {
        $goal = Goal::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        $this->editingGoalId   = $goal->id;
        $this->name            = $goal->name;
        $this->target_amount   = $goal->target_amount;
        $this->current_amount  = $goal->current_amount;
        $this->deadline        = $goal->deadline ? Carbon::parse($goal->deadline)->format('Y-m-d') : '';

        // ABRIR MODAL
       $this->dispatch('modal-show-goal');
    }

    /**
     * Eliminar meta
     */
    public function delete(int $id): void
    {
        Goal::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id)
            ->delete();

        $this->dispatch('toast', text: 'Objetivo removido.');
    }

    /**
     * Abrir modal de depósito
     */
    public function openDeposit(int $id): void
    {
        $this->depositGoalId = $id;
        $this->depositAmount = '';

        $this->dispatch('modal-show-deposit');
    }

    /**
     * Guardar depósito
     */
    public function saveDeposit()
    {
        $this->validate([
            'depositAmount' => 'required|numeric|min:0.01',
        ]);

        $goal = Goal::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($this->depositGoalId);

        $goal->increment('current_amount', (float) $this->depositAmount);

        // FECHAR MODAL
        $this->dispatch('modal-close-deposit');

        // TOAST
        $this->dispatch('toast', text: 'Depósito registado com sucesso!');

        // RESET
        $this->reset(['depositGoalId', 'depositAmount']);
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $goalsRaw    = Goal::where('workspace_id', $workspaceId)->orderBy('deadline')->get();

        $goals = $goalsRaw->map(function ($goal) {
            $perc     = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
            $gap      = max(0, $goal->target_amount - $goal->current_amount);
            $daysLeft = $goal->deadline ? now()->diffInDays(Carbon::parse($goal->deadline), false) : null;

            $monthsLeft    = ($daysLeft !== null && $daysLeft > 0) ? max(1, ceil($daysLeft / 30)) : null;
            $monthlyNeeded = ($monthsLeft && $gap > 0) ? $gap / $monthsLeft : null;

            $goal->perc          = $perc;
            $goal->gap           = $gap;
            $goal->daysLeft      = $daysLeft;
            $goal->monthlyNeeded = $monthlyNeeded;
            $goal->isCompleted   = $perc >= 100;
            $goal->isOverdue     = $daysLeft !== null && $daysLeft < 0 && !$goal->isCompleted;
            $goal->isUrgent      = $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 30 && !$goal->isCompleted;

            return $goal;
        });

        $totalTarget  = $goals->sum('target_amount');
        $totalCurrent = $goals->sum('current_amount');
        $totalGap     = max(0, $totalTarget - $totalCurrent);
        $globalPct    = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;

        $completed    = $goals->where('isCompleted', true)->count();
        $urgent       = $goals->where('isUrgent', true)->count();
        $overdue      = $goals->where('isOverdue', true)->count();

        $sortedGoals = $goals->sortBy(fn($g) => match (true) {
            $g->isOverdue   => 0,
            $g->isUrgent    => 1,
            !$g->isCompleted && $g->deadline => 2,
            $g->isCompleted => 4,
            default         => 3,
        })->values();

        return view('livewire.goals-hub', [
            'goals'        => $sortedGoals,
            'totalTarget'  => $totalTarget,
            'totalCurrent' => $totalCurrent,
            'totalGap'     => $totalGap,
            'globalPct'    => $globalPct,
            'completed'    => $completed,
            'urgent'       => $urgent,
            'overdue'      => $overdue,
        ]);
    }
}

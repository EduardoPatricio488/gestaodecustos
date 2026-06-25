<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Goal;
use App\Models\SocialPost; // Importado para integração social
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class GoalsHub extends Component
{
    // Propriedades do Formulário
    public string $name = '';
    public $target_amount = '';
    public $current_amount = 0;
    public $deadline = '';
    public ?int $editingGoalId = null;

    // Propriedades de Depósito Rápido
    public ?int $depositGoalId = null;
    public $depositAmount = '';

    /**
     * Criar ou Atualizar Objetivo
     */
    public function save(): void
    {
        $this->validate([
            'name'           => 'required|string|max:255',
            'target_amount'  => 'required|numeric|min:1',
            'current_amount' => 'required|numeric|min:0',
            'deadline'       => 'nullable|date',
        ]);

        $workspaceId = auth()->user()->current_workspace_id;
        $isNew = !$this->editingGoalId; // Verifica se é um registo novo

        $goal = Goal::updateOrCreate(
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

        // 🚀 INTEGRAÇÃO SOCIAL: Publicar meta nova
        if ($isNew) {
            SocialPost::publishFinancialEvent(
                auth()->id(),
                "tracei um novo objetivo: " . $goal->name . ". Vamos a isto! 🚀",
                'Goal',
                $goal->id
            );
        }

        $this->reset(['name', 'target_amount', 'current_amount', 'deadline', 'editingGoalId']);
        $this->dispatch('modal-close', name: 'goal-modal');
        $this->dispatch('toast', text: 'Objetivo guardado com sucesso!');
    }

    /**
     * Carregar meta para edição
     */
    public function edit(int $id): void
    {
        $goal = Goal::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
        $this->editingGoalId   = $goal->id;
        $this->name            = $goal->name;
        $this->target_amount   = $goal->target_amount;
        $this->current_amount  = $goal->current_amount;
        $this->deadline        = $goal->deadline ? Carbon::parse($goal->deadline)->format('Y-m-d') : '';

        $this->dispatch('modal-open', name: 'goal-modal');
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
     * Abrir modal de depósito rápido
     */
    public function openDeposit(int $id): void
    {
        $this->depositGoalId = $id;
        $this->depositAmount = '';
        $this->dispatch('modal-open', name: 'deposit-modal');
    }

    /**
     * Guardar depósito e verificar conclusão
     */
    public function saveDeposit(): void
    {
        $this->validate(['depositAmount' => 'required|numeric|min:0.01']);

        $goal = Goal::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($this->depositGoalId);

        // Verifica o estado antes do incremento
        $wasCompleted = $goal->current_amount >= $goal->target_amount;

        $goal->increment('current_amount', (float) $this->depositAmount);

        // 🚀 INTEGRAÇÃO SOCIAL: Publicar se atingiu 100% agora
        if (!$wasCompleted && $goal->current_amount >= $goal->target_amount) {
            SocialPost::publishFinancialEvent(
                auth()->id(),
                "🎯 META ALCANÇADA! Acabei de completar 100% do meu objetivo: " . $goal->name . "!",
                'Goal',
                $goal->id
            );
        }

        $this->reset(['depositGoalId', 'depositAmount']);
        $this->dispatch('modal-close', name: 'deposit-modal');
        $this->dispatch('toast', text: 'Depósito registado com sucesso!');
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $goalsRaw    = Goal::where('workspace_id', $workspaceId)->orderBy('deadline')->get();

        // Processamento analítico das metas
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

        // Totais globais para os widgets
        $totalTarget  = $goals->sum('target_amount');
        $totalCurrent = $goals->sum('current_amount');
        $totalGap     = max(0, $totalTarget - $totalCurrent);
        $globalPct    = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;

        $completed    = $goals->where('isCompleted', true)->count();
        $urgent       = $goals->where('isUrgent', true)->count();
        $overdue      = $goals->where('isOverdue', true)->count();

        // Ordenação inteligente para a lista
        $sortedGoals = $goals->sortBy(fn($g) => match(true) {
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
            'overdue'      => $overdue
        ]);
    }
}

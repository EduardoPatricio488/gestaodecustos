<?php

namespace App\Livewire;

use App\Models\AutoSavingsRule;
use App\Models\Goal;
use App\Models\GoalContribution;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

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

    public ?int $autoGoalId = null;

    public string $autoProfile = 'equilibrado';

    public $autoPercent = 20;

    public $autoMinIncomeAmount = '';

    public string $autoAppliesTo = 'all';

    /**
     * Criar ou atualizar meta
     */
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'current_amount' => 'required|numeric|min:0',
            'deadline' => 'nullable|date',
        ]);

        $workspaceId = auth()->user()->current_workspace_id;

        $initialAmount = (float) $this->current_amount;
        $isCreating = $this->editingGoalId === null;

        $goal = Goal::updateOrCreate(
            ['id' => $this->editingGoalId],
            [
                'user_id' => auth()->id(),
                'workspace_id' => $workspaceId,
                'name' => $this->name,
                'target_amount' => (float) $this->target_amount,
                'current_amount' => (float) $this->current_amount,
                'deadline' => $this->deadline ?: null,
            ]
        );

        if ($isCreating && $initialAmount > 0) {
            GoalContribution::create([
                'workspace_id' => $workspaceId,
                'goal_id' => $goal->id,
                'user_id' => auth()->id(),
                'amount' => $initialAmount,
                'source' => 'initial',
                'note' => 'Valor inicial da meta.',
                'contributed_at' => now(),
            ]);
        }

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

        $this->editingGoalId = $goal->id;
        $this->name = $goal->name;
        $this->target_amount = $goal->target_amount;
        $this->current_amount = $goal->current_amount;
        $this->deadline = $goal->deadline ? Carbon::parse($goal->deadline)->format('Y-m-d') : '';

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

        DB::transaction(function () use ($goal) {
            $amount = (float) $this->depositAmount;

            GoalContribution::create([
                'workspace_id' => $goal->workspace_id,
                'goal_id' => $goal->id,
                'user_id' => auth()->id(),
                'amount' => $amount,
                'source' => 'manual',
                'note' => 'Deposito manual.',
                'contributed_at' => now(),
            ]);

            $goal->increment('current_amount', $amount);
        });

        // FECHAR MODAL
        $this->dispatch('modal-close-deposit');

        // TOAST
        $this->dispatch('toast', text: 'Depósito registado com sucesso!');

        // RESET
        $this->reset(['depositGoalId', 'depositAmount']);
    }

    public function updatedAutoProfile(string $profile): void
    {
        $defaults = $this->autoProfiles();

        if (isset($defaults[$profile])) {
            $this->autoPercent = $defaults[$profile]['percent'];
        }
    }

    public function saveAutoSavingsRule(): void
    {
        $profiles = implode(',', array_keys($this->autoProfiles()));

        $this->validate([
            'autoGoalId' => 'required|integer|exists:goals,id',
            'autoProfile' => 'required|in:'.$profiles,
            'autoPercent' => 'required|numeric|min:1|max:80',
            'autoMinIncomeAmount' => 'nullable|numeric|min:0',
            'autoAppliesTo' => 'required|in:all,recurring,one_off',
        ]);

        $workspaceId = auth()->user()->current_workspace_id;
        $goal = Goal::where('workspace_id', $workspaceId)->findOrFail($this->autoGoalId);

        AutoSavingsRule::updateOrCreate(
            [
                'workspace_id' => $workspaceId,
                'user_id' => auth()->id(),
                'goal_id' => $goal->id,
            ],
            [
                'profile' => $this->autoProfile,
                'percent' => (float) $this->autoPercent,
                'min_income_amount' => $this->autoMinIncomeAmount !== '' ? (float) $this->autoMinIncomeAmount : null,
                'applies_to' => $this->autoAppliesTo,
                'is_active' => true,
            ]
        );

        $this->autoProfile = 'equilibrado';
        $this->autoPercent = 20;
        $this->autoMinIncomeAmount = '';
        $this->autoAppliesTo = 'all';

        $this->dispatch('toast', variant: 'success', text: 'Regra de autopoupanca guardada!');
    }

    public function toggleAutoSavingsRule(int $id): void
    {
        $rule = AutoSavingsRule::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $rule->update(['is_active' => ! $rule->is_active]);
    }

    public function deleteAutoSavingsRule(int $id): void
    {
        AutoSavingsRule::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($id)
            ->delete();

        $this->dispatch('toast', text: 'Regra removida.');
    }

    private function autoProfiles(): array
    {
        return [
            'conservador' => ['label' => 'Conservador', 'percent' => 10],
            'equilibrado' => ['label' => 'Equilibrado', 'percent' => 20],
            'agressivo' => ['label' => 'Agressivo', 'percent' => 30],
        ];
    }

    public function render()
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $goalsRaw = Goal::with(['contributions.user'])
            ->where('workspace_id', $workspaceId)
            ->orderBy('deadline')
            ->get();

        $goals = $goalsRaw->map(function ($goal) {
            $perc = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
            $gap = max(0, $goal->target_amount - $goal->current_amount);
            $daysLeft = $goal->deadline ? now()->diffInDays(Carbon::parse($goal->deadline), false) : null;
            $recentContributions = $goal->contributions
                ->sortByDesc('contributed_at')
                ->take(3)
                ->values();

            $monthsLeft = ($daysLeft !== null && $daysLeft > 0) ? max(1, ceil($daysLeft / 30)) : null;
            $monthlyNeeded = ($monthsLeft && $gap > 0) ? $gap / $monthsLeft : null;
            $last90DaysTotal = (float) $goal->contributions
                ->filter(fn ($contribution) => $contribution->contributed_at && $contribution->contributed_at->gte(now()->subDays(90)))
                ->sum('amount');
            $monthlyPace = $last90DaysTotal > 0 ? $last90DaysTotal / 3 : 0.0;
            $predictedCompletionDate = null;

            if ($perc >= 100) {
                $predictedCompletionDate = now();
            } elseif ($gap > 0 && $monthlyPace > 0) {
                $monthsNeeded = (int) ceil($gap / $monthlyPace);
                // Evita travar o Carbon com valores absurdos quando o ritmo mensal é ínfimo face ao gap
                $predictedCompletionDate = $monthsNeeded < 1200 ? now()->copy()->addMonths($monthsNeeded) : null;
            }

            $goal->perc = $perc;
            $goal->gap = $gap;
            $goal->daysLeft = $daysLeft;
            $goal->monthlyNeeded = $monthlyNeeded;
            $goal->monthlyPace = $monthlyPace;
            $goal->predictedCompletionDate = $predictedCompletionDate;
            $goal->isLateByForecast = $goal->deadline
                && $predictedCompletionDate
                && $predictedCompletionDate->gt(Carbon::parse($goal->deadline))
                && $perc < 100;
            $goal->isCompleted = $perc >= 100;
            $goal->isOverdue = $daysLeft !== null && $daysLeft < 0 && ! $goal->isCompleted;
            $goal->isUrgent = $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 30 && ! $goal->isCompleted;
            $goal->contributors = $goal->contributions
                ->groupBy('user_id')
                ->map(fn ($rows) => [
                    'name' => $rows->first()->user?->name ?? 'Membro',
                    'amount' => (float) $rows->sum('amount'),
                ])
                ->sortByDesc('amount')
                ->values();
            $goal->recentContributions = $recentContributions;

            return $goal;
        });

        $totalTarget = $goals->sum('target_amount');
        $totalCurrent = $goals->sum('current_amount');
        $totalGap = max(0, $totalTarget - $totalCurrent);
        $globalPct = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;

        $completed = $goals->where('isCompleted', true)->count();
        $urgent = $goals->where('isUrgent', true)->count();
        $overdue = $goals->where('isOverdue', true)->count();

        $sortedGoals = $goals->sortBy(fn ($g) => match (true) {
            $g->isOverdue => 0,
            $g->isUrgent => 1,
            ! $g->isCompleted && $g->deadline => 2,
            $g->isCompleted => 4,
            default => 3,
        })->values();

        return view('livewire.goals-hub', [
            'goals' => $sortedGoals,
            'totalTarget' => $totalTarget,
            'totalCurrent' => $totalCurrent,
            'totalGap' => $totalGap,
            'globalPct' => $globalPct,
            'completed' => $completed,
            'urgent' => $urgent,
            'overdue' => $overdue,
            'autoProfiles' => $this->autoProfiles(),
            'autoSavingsRules' => AutoSavingsRule::with('goal')
                ->where('workspace_id', $workspaceId)
                ->where('user_id', auth()->id())
                ->latest()
                ->get(),
        ]);
    }
}

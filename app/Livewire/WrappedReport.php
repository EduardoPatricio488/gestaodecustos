<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Category;
use App\Services\FinanceScoreService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class WrappedReport extends Component
{
    public int $year;

    public function mount(): void
    {
        $this->year = now()->year;
    }

    public function shareToSocial(): void
    {
        $this->dispatch('toast', variant: 'success', text: 'Em breve: partilhar no Finance Connect!');
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $user = auth()->user();

        $spent = (float) Expense::where('workspace_id', $workspace->id)
            ->where('is_company', false)
            ->whereYear('spent_at', $this->year)
            ->sum('amount');

        $earned = (float) Income::where('workspace_id', $workspace->id)
            ->whereYear('received_at', $this->year)
            ->sum('amount');

        $saved = max(0, $earned - $spent);

        $topCategory = Category::where('workspace_id', $workspace->id)
            ->withSum(['expenses' => fn ($q) => $q->whereYear('spent_at', $this->year)->where('is_company', false)], 'amount')
            ->orderByDesc('expenses_sum_amount')
            ->first();

        $goalsCompleted = Goal::where('workspace_id', $workspace->id)
            ->whereYear('updated_at', $this->year)
            ->get()
            ->filter(fn ($g) => $g->current_amount >= $g->target_amount)
            ->count();

        $monthlySpending = collect(range(1, 12))->map(fn ($m) => (float) Expense::where('workspace_id', $workspace->id)
            ->where('is_company', false)
            ->whereYear('spent_at', $this->year)
            ->whereMonth('spent_at', $m)
            ->sum('amount'));

        $scoreService = app(FinanceScoreService::class);
        $currentScore = $scoreService->calculate($workspace)['score'];

        return view('livewire.wrapped-report', [
            'spent' => $spent,
            'earned' => $earned,
            'saved' => $saved,
            'topCategory' => $topCategory,
            'goalsCompleted' => $goalsCompleted,
            'monthlySpending' => $monthlySpending,
            'level' => $user->level ?? 1,
            'xp' => $user->xp ?? 0,
            'score' => $currentScore,
            'scoreGrade' => $scoreService->getGrade($currentScore),
        ]);
    }
}

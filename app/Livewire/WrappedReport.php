<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Category;
use App\Models\Investment;
use App\Models\Subscription;
use App\Models\SocialPost;
use App\Services\FinanceScoreService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class WrappedReport extends Component
{
    public string $view = 'year';
    public int $year;
    public int $month;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function setView($mode)
    {
        $this->view = $mode;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function shareToSocial(): void
    {
        $user = auth()->user();
        $data = $this->getWrappedData();

        try {
            SocialPost::create([
                'user_id' => $user->id,
                'content' => "💰 O meu Wrapped de " . ($this->view === 'year' ? $this->year : Carbon::create()->month($this->month)->translatedFormat('F')) . " está aqui!\n\n" .
                             "🔹 Gastei: " . number_format($data['spent'], 2) . "€\n" .
                             "🔹 Poupei: " . number_format($data['saved'], 2) . "€\n" .
                             "🏆 Score Financeiro: " . $data['score'] . " (" . $data['scoreGrade'] . ")\n\n" .
                             "#FinanceProWrapped #FinancialFreedom",
                'type' => 'wrapped',
                'metadata' => json_encode($data),
            ]);

            $user->increment('xp', 50);
            $this->dispatch('toast', variant: 'success', text: 'Publicado no Finance Connect! +50 XP 🚀');
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', text: 'Erro ao publicar.');
        }
    }

    private function getWrappedData()
    {
        $workspaceId = auth()->user()->current_workspace_id;

        $expenseQuery = Expense::where('workspace_id', $workspaceId)
            ->where('is_company', false)
            ->whereYear('spent_at', $this->year);

        $incomeQuery = Income::where('workspace_id', $workspaceId)
            ->whereYear('received_at', $this->year);

        if ($this->view === 'month') {
            $expenseQuery->whereMonth('spent_at', $this->month);
            $incomeQuery->whereMonth('received_at', $this->month);
        }

        $spent = (float) $expenseQuery->sum('amount');
        $earned = (float) $incomeQuery->sum('amount');
        $transactionCount = (clone $expenseQuery)->count();

        $saved = max(0, $earned - $spent);
        $savingsRate = $earned > 0 ? ($saved / $earned) * 100 : 0;

        // Investimentos
        $investments = Investment::where('workspace_id', $workspaceId)->get();
        $totalInvested = (float) $investments->sum('amount');
        $currentPortfolioValue = (float) $investments->sum('current_value');
        $portfolioGain = $currentPortfolioValue - $totalInvested;

        // Subscrições
        $activeSubsCount = Subscription::where('workspace_id', $workspaceId)->count();
        $subsMonthlyCost = (float) Subscription::where('workspace_id', $workspaceId)->sum('price');

        // Padrão Mensal (Corrigido para Coleção)
        $monthlyPattern = collect();
        if ($this->view === 'year') {
            $monthlyPattern = Expense::where('workspace_id', $workspaceId)
                ->where('is_company', false)
                ->whereYear('spent_at', $this->year)
                ->selectRaw('strftime("%m", spent_at) as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('total', 'desc')
                ->get();
        }

        $biggestExpense = (clone $expenseQuery)->orderByDesc('amount')->first();

        $topCategories = Category::where('workspace_id', $workspaceId)
            ->withSum(['expenses' => function($q) {
                $q->whereYear('spent_at', $this->year)->where('is_company', false);
                if ($this->view === 'month') $q->whereMonth('spent_at', $this->month);
            }], 'amount')
            ->orderByDesc('expenses_sum_amount')
            ->take(3)
            ->get();

        $scoreService = app(FinanceScoreService::class);
        $scoreData = $scoreService->calculate(auth()->user()->currentWorkspace);

        return [
            'spent' => $spent,
            'earned' => $earned,
            'saved' => $saved,
            'savingsRate' => round($savingsRate, 1),
            'transactionCount' => $transactionCount,
            'avgSpending' => $this->view === 'year' ? ($spent / 12) : $spent,
            'biggestExpense' => $biggestExpense,
            'topCategories' => $topCategories,
            'totalInvested' => $totalInvested,
            'portfolioValue' => $currentPortfolioValue,
            'portfolioGain' => $portfolioGain,
            'activeSubsCount' => $activeSubsCount,
            'subsYearlyCost' => $subsMonthlyCost * 12,
            'bestMonth' => $monthlyPattern->isNotEmpty() ? $monthlyPattern->last() : null,
            'worstMonth' => $monthlyPattern->isNotEmpty() ? $monthlyPattern->first() : null,
            'score' => $scoreData['score'],
            'scoreGrade' => $scoreService->getGrade($scoreData['score']),
            'scoreFactors' => $scoreData['factors'] ?? [],
        ];
    }

    public function render()
    {
        $data = $this->getWrappedData();
        $user = auth()->user();

        $years = range(now()->year, 2023);

        return view('livewire.wrapped-report', array_merge($data, [
            'level' => $user->level ?? 1,
            'xp' => $user->xp ?? 0,
            'availableYears' => $years,
            'goalsCompleted' => Goal::where('workspace_id', $user->current_workspace_id)
                ->where('current_amount', '>=', DB::raw('target_amount'))
                ->whereYear('updated_at', $this->year)
                ->count(),
        ]));
    }
}

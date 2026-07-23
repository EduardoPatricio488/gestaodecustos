<?php

namespace App\Livewire;

use App\Models\BudgetChallenge;
use App\Models\Category;
use App\Services\BudgetService;
use App\Services\FinanceScoreService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BudgetHub extends Component
{
    public string $selectedMonth;
    public string $activeTab = 'overview';

    public string $challengeTitle = '';
    public $challengeCategoryId = null;
    public $challengeTarget = '';
    public int $challengeDays = 30;

    public function mount(): void
{
    // 1. SEGURANÇA: Verificar se o utilizador atual tem permissão
    $isRestricted = \App\Models\FamilyBudgetPermission::where('user_id', auth()->id())
        ->where('workspace_id', auth()->user()->current_workspace_id)
        ->whereNull('category_id') // Puxar o registo de permissões globais
        ->where('restrict_budget', true)
        ->exists();

    if ($isRestricted) {
        // Se estiver bloqueado, trava a entrada imediatamente com erro 403
        abort(403, 'Área Restrita: O teu administrador bloqueou o acesso ao Orçamento.');
    }

    // 2. LÓGICA ORIGINAL: Definir o mês selecionado
    $this->selectedMonth = now()->format('Y-m');
}

    public function previousMonth(): void
    {
        $this->selectedMonth = Carbon::parse($this->selectedMonth.'-01')->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $month = Carbon::parse($this->selectedMonth.'-01')->addMonth();
        if ($month->lte(now()->addMonth())) {
            $this->selectedMonth = $month->format('Y-m');
        }
    }

    public function createChallenge(): void
    {
        $this->validate([
            'challengeTitle' => 'required|string|max:255',
            'challengeTarget' => 'required|numeric|min:1',
            'challengeDays' => 'required|integer|min:7|max:90',
        ]);

        $workspace = auth()->user()->currentWorkspace;

        BudgetChallenge::create([
            'workspace_id' => $workspace->id,
            'user_id' => auth()->id(),
            'category_id' => $this->challengeCategoryId ?: null,
            'title' => $this->challengeTitle,
            'target_amount' => $this->challengeTarget,
            'start_date' => now(),
            'end_date' => now()->addDays($this->challengeDays),
            'status' => 'active',
        ]);

        $this->reset(['challengeTitle', 'challengeCategoryId', 'challengeTarget']);
        $this->challengeDays = 30;
        $this->dispatch('toast', variant: 'success', text: 'Desafio criado! Boa sorte!');
    }

    public function checkChallenges(): void
    {
        $user = auth()->user();
        $challenges = BudgetChallenge::where('workspace_id', $user->current_workspace_id)
            ->where('status', 'active')
            ->get();

        foreach ($challenges as $challenge) {
            if ($challenge->isFailed()) {
                $challenge->update(['status' => 'failed']);
            } elseif ($challenge->isCompleted() && ! $challenge->xp_awarded) {
                $challenge->update(['status' => 'completed', 'xp_awarded' => true]);
                $user->addXp(200);
                $user->awardBadge('Desafio Concluído');
                $this->dispatch('toast', variant: 'success', text: 'Desafio concluído! +200 XP');
            }
        }
    }

    public function render()
    {
        $workspace = auth()->user()->currentWorkspace;
        $month = Carbon::parse($this->selectedMonth.'-01');

        $budgetService = app(BudgetService::class);
        $scoreService = app(FinanceScoreService::class);

        $this->checkChallenges();

        $scoreData = $scoreService->calculate($workspace, $month);
        $scoreService->snapshot($workspace, auth()->id(), $month);

        return view('livewire.budget-hub', [
            'overview' => $budgetService->getMonthlyOverview($workspace, $month),
            'categories' => $budgetService->getCategoryBreakdown($workspace, $month),
            'alerts' => $budgetService->getAlerts($workspace, $month),
            'score' => $scoreData,
            'scoreGrade' => $scoreService->getGrade($scoreData['score']),
            'scoreTrend' => $scoreService->getTrend($workspace),
            'challenges' => BudgetChallenge::where('workspace_id', $workspace->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
            'categoryOptions' => Category::where('workspace_id', $workspace->id)->orderBy('name')->get(),
        ]);
    }
}

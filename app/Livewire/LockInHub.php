<?php

namespace App\Livewire;

use App\Models\{BankAccount, Expense, Income, Goal, Subscription, Invoice};
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, DB};
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.lockin')]
class LockInHub extends Component
{
    public int $sessionSeconds = 0;
    public int $xpEarned = 0;
    public int $lastXpMilestone = 0;

    public function mount(): void
    {
        if (! session()->has('lock_in_start')) {
            session(['lock_in_start' => now()->timestamp]);
        }
        $this->sessionSeconds = now()->timestamp - (int) session('lock_in_start', now()->timestamp);
        $this->lastXpMilestone = (int) floor($this->sessionSeconds / 600);
    }

    /**
     * Chamado pelo wire:poll a cada segundo via Alpine.
     */
    public function tick(): void
    {
        $start = (int) session('lock_in_start', now()->timestamp);
        $this->sessionSeconds = now()->timestamp - $start;

        $milestone = (int) floor($this->sessionSeconds / 600);
        if ($milestone > $this->lastXpMilestone) {
            $gained = ($milestone - $this->lastXpMilestone) * 20;
            $this->lastXpMilestone = $milestone;
            $this->xpEarned += $gained;
            $user = Auth::user();
            if (method_exists($user, 'addXp')) {
                $user->addXp($gained);
            }
            $this->dispatch('toast', variant: 'success', text: "+{$gained} XP de Foco conquistados! 🎯");
        }
    }

    public function unlock(): void
    {
        session()->forget('lock_in_start');
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function breakGlassAction(string $action): void
    {
        session()->forget('lock_in_start');
        match ($action) {
            'subscriptions' => $this->redirect(route('hub.subscriptions'), navigate: true),
            'debts'         => $this->redirect(route('hub.debts'), navigate: true),
            'reserve'       => $this->redirect(route('hub.networth'), navigate: true),
            default         => $this->redirect(route('dashboard'), navigate: true),
        };
    }

    public function render()
    {
        $user  = Auth::user();
        $ws    = $user->currentWorkspace;
        $wsId  = $ws?->id ?? 0;
        $now   = now();
        $som   = $now->copy()->startOfMonth();
        $eom   = $now->copy()->endOfMonth();

        // ═══════════════════════════════════════════
        // PERSONAL KPIs
        // ═══════════════════════════════════════════

        $personalBalance = BankAccount::where('workspace_id', $wsId)
            ->where('is_business', false)
            ->sum('balance');

        $totalBalance = BankAccount::where('workspace_id', $wsId)->sum('balance');

        $mainGoal    = Goal::where('workspace_id', $wsId)->first();
        $goalName    = $mainGoal?->name    ?? 'Poupança';
        $goalTarget  = (float) ($mainGoal?->target_amount  ?? 0);
        $goalCurrent = (float) ($mainGoal?->current_amount ?? 0);
        $goalProgress = $goalTarget > 0 ? min(100, round(($goalCurrent / $goalTarget) * 100)) : 0;

        $upcomingBills = Subscription::where('workspace_id', $wsId)
            ->where('is_active', true)
            ->orderBy('billing_day')
            ->limit(3)
            ->get(['name', 'amount', 'billing_day', 'cycle']);

        $personalIncomeMonth = Income::where('workspace_id', $wsId)
            ->whereMonth('received_at', $now->month)
            ->whereYear('received_at', $now->year)
            ->sum('amount');

        $personalExpenseMonth = Expense::where('workspace_id', $wsId)
            ->whereMonth('spent_at', $now->month)
            ->whereYear('spent_at', $now->year)
            ->sum('amount');

        $personalNetMonth = $personalIncomeMonth - $personalExpenseMonth;

        // ═══════════════════════════════════════════
        // BUSINESS KPIs
        // ═══════════════════════════════════════════

        $businessWs   = $user->workspaces()->where('type', '!=', 'personal')->first();
        $bizId        = $businessWs?->id ?? 0;

        $monthlyRevenue = $bizId
            ? Invoice::where('workspace_id', $bizId)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$som, $eom])
                ->sum('total_amount')
            : 0;

        $monthlyExpenses = $bizId
            ? Expense::where('workspace_id', $bizId)
                ->whereBetween('spent_at', [$som, $eom])
                ->sum('amount')
            : 0;

        $pnl      = $monthlyRevenue - $monthlyExpenses;
        $cashFlow = $pnl;

        $accountsReceivable = $bizId
            ? Invoice::where('workspace_id', $bizId)
                ->where('status', 'pending')
                ->sum('total_amount')
            : 0;

        $overdueAR = $bizId
            ? Invoice::where('workspace_id', $bizId)
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->sum('total_amount')
            : 0;

        // ═══════════════════════════════════════════
        // SURVIVAL METRICS
        // ═══════════════════════════════════════════

        $totalSpend3m = Expense::where('workspace_id', $wsId)
            ->where('spent_at', '>=', $now->copy()->subMonths(3))
            ->sum('amount');

        $avgMonthlySpend = $totalSpend3m > 0 ? ($totalSpend3m / 3) : 500;

        $runwayMonths = $avgMonthlySpend > 0 ? (int) floor($totalBalance / $avgMonthlySpend) : 0;
        $runwayDays   = $avgMonthlySpend > 0
            ? (int) floor((fmod((float) $totalBalance, (float) $avgMonthlySpend) / $avgMonthlySpend) * 30)
            : 0;

        $burnRateDaily  = $avgMonthlySpend / 30;
        $burnRateActual = $now->day > 0 ? $personalExpenseMonth / $now->day : 0;

        // ═══════════════════════════════════════════
        // AI CFO INSIGHTS
        // ═══════════════════════════════════════════

        $aiInsight = $this->generateAiInsight(
            $burnRateActual,
            $avgMonthlySpend,
            $totalBalance,
            $accountsReceivable,
            $pnl
        );

        return view('livewire.lock-in-hub', [
            // Personal
            'personalBalance'      => $personalBalance,
            'totalBalance'         => $totalBalance,
            'goalName'             => $goalName,
            'goalProgress'         => $goalProgress,
            'goalTarget'           => $goalTarget,
            'goalCurrent'          => $goalCurrent,
            'upcomingBills'        => $upcomingBills,
            'personalIncomeMonth'  => $personalIncomeMonth,
            'personalExpenseMonth' => $personalExpenseMonth,
            'personalNetMonth'     => $personalNetMonth,
            // Business
            'monthlyRevenue'       => $monthlyRevenue,
            'monthlyExpenses'      => $monthlyExpenses,
            'pnl'                  => $pnl,
            'cashFlow'             => $cashFlow,
            'accountsReceivable'   => $accountsReceivable,
            'overdueAR'            => $overdueAR,
            'hasBusinessWs'        => $bizId > 0,
            // Survival
            'runwayMonths'         => $runwayMonths,
            'runwayDays'           => $runwayDays,
            'burnRateDaily'        => $burnRateDaily,
            'burnRateActual'       => $burnRateActual,
            'avgMonthlySpend'      => $avgMonthlySpend,
            // AI
            'aiInsight'            => $aiInsight,
            // Session
            'sessionSeconds'       => $this->sessionSeconds,
            'xpEarned'             => $this->xpEarned,
        ]);
    }

    // ═══════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════

    private function generateAiInsight(
        float $burnRate,
        float $avgSpend,
        float $balance,
        float $ar,
        float $pnl
    ): array {
        $risk    = null;
        $suggest = null;

        if ($burnRate > $avgSpend / 30 * 1.25) {
            $pct  = $this->pct($burnRate, $avgSpend / 30);
            $risk = "Burn rate diário {$pct}% acima do histórico — o mês está a escapar-se.";
            $suggest = "Congela gastos discricionários hoje. Revê assinaturas e faz uma auditoria rápida.";
        } elseif ($ar > $balance * 0.25 && $ar > 0) {
            $risk    = number_format($ar, 2, ',', '.') . "€ presos em faturas por cobrar — liquidez em risco.";
            $suggest = "Envia lembretes de pagamento agora. Cada dia adiado é dinheiro perdido.";
        } elseif ($pnl < 0) {
            $risk    = "P&L empresarial negativo este mês. As despesas superam as receitas.";
            $suggest = "Acelera a faturação pendente e suspende gastos não essenciais na empresa.";
        } else {
            $risk    = "Sem alertas críticos detectados. Perfil financeiro estável.";
            $suggest = "Mantém o foco. Verifica se a reserva de emergência está acima de 3 meses de despesas.";
        }

        return ['risk' => $risk, 'suggest' => $suggest];
    }

    private function pct(float $a, float $b): int
    {
        if ($b == 0) return 0;
        return (int) abs(round((($a - $b) / $b) * 100));
    }
}

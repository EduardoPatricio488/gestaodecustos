<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Expense, Category, Income, Goal, Investment};
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class AiInsights extends Component
{
    public bool $isAnalyzing = false;
    public string $aiAnalysis = '';
    public ?string $lastGeneratedAt = null;

    public function mount()
    {
        $cached = Cache::get($this->cacheKey());
        if ($cached) {
            $this->aiAnalysis = $cached['text'];
            $this->lastGeneratedAt = $cached['at'];
        }
    }

    private function cacheKey(): string
    {
        return "ai-insights:" . auth()->id();
    }

    /**
     * Ganhos e gastos de um mês específico (reutilizável para mês atual e anterior).
     */
    private function getMonthlyTotals(int $month, int $year): array
    {
        $user = auth()->user();

        $earned = (float) Income::where('user_id', $user->id)
                ->whereMonth('received_at', $month)
                ->whereYear('received_at', $year)
                ->sum('amount')
            + (float) $user->recurringIncomes()->where('is_active', true)->sum('amount');

        $spent = (float) Expense::where('user_id', $user->id)
            ->where('is_company', false)
            ->whereMonth('spent_at', $month)
            ->whereYear('spent_at', $year)
            ->sum('amount');

        return [$earned, $spent];
    }

    /**
     * Variação percentual entre dois valores. Devolve null se não houver
     * base de comparação válida (evita divisão por zero / % absurdas).
     */
    private function percentDelta(float $current, float $previous): ?float
    {
        if ($previous == 0) return null;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Guarda um snapshot do património no início do mês (cache, sem migração nova)
     * e devolve a variação % vs o snapshot do mês anterior, se existir.
     */
    private function trackNetWorthSnapshot(float $currentNetWorth, int $year, int $month): ?float
    {
        $userId = auth()->id();
        $key = "networth-snapshot:{$userId}:{$year}-{$month}";

        if (!Cache::has($key)) {
            Cache::put($key, $currentNetWorth, now()->addMonths(13));
        }

        $prevDate = Carbon::createFromDate($year, $month, 1)->subMonth();
        $prevKey = "networth-snapshot:{$userId}:{$prevDate->year}-{$prevDate->month}";
        $prevNetWorth = Cache::get($prevKey);

        if ($prevNetWorth === null || $prevNetWorth == 0) {
            return null; // sem dados do mês anterior — não inventamos comparação
        }

        return round((($currentNetWorth - $prevNetWorth) / $prevNetWorth) * 100, 1);
    }

    public function generateInsights()
    {
        set_time_limit(120);
        $this->isAnalyzing = true;
        $this->aiAnalysis = '';

        $user = auth()->user();
        $month = now()->month;
        $year = now()->year;

        [$totalEarned, $totalSpent] = $this->getMonthlyTotals($month, $year);

        $expensesByCategory = Expense::selectRaw('categories.name as category, sum(expenses.amount) as total')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $user->id)
            ->where('expenses.is_company', false)
            ->whereMonth('expenses.spent_at', $month)
            ->whereYear('expenses.spent_at', $year)
            ->groupBy('categories.name')->get()->pluck('total', 'category')->toArray();

        $invValue = (float) $user->investments->sum(fn($i) => $i->quantity * $i->current_price);
        $savings = $totalEarned - $totalSpent;
        $savingsRate = $totalEarned > 0 ? ($savings / $totalEarned) * 100 : 0;

        $prompt = "Age como um Diretor Financeiro Pessoal (CFO). Analisa os meus dados deste mês:
        - Rendimento Total: {$totalEarned}€
        - Gasto Total: {$totalSpent}€
        - Taxa de Poupança: " . round($savingsRate, 1) . "%
        - Distribuição por Categoria: " . json_encode($expensesByCategory) . "
        - Valor em Ativos/Investimentos: {$invValue}€

        Tarefa:
        1. Dá um diagnóstico sincero sobre a minha saúde financeira.
        2. Identifica a categoria mais problemática.
        3. Dá 3 dicas práticas para aumentar a taxa de poupança.
        Responde em Português de Portugal, usa Markdown e emojis.";

        try {
            $apiKey = env('OPENROUTER_API_KEY');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'HTTP-Referer'  => config('app.url'),
                'X-Title'       => config('app.name'),
            ])->timeout(60)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'google/gemini-2.5-flash',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 2000,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->aiAnalysis = $data['choices'][0]['message']['content'] ?? "Resposta vazia da IA.";
                $this->lastGeneratedAt = now()->toIso8601String();

                Cache::put($this->cacheKey(), [
                    'text' => $this->aiAnalysis,
                    'at' => $this->lastGeneratedAt,
                ], now()->addDays(7));

                if (method_exists($user, 'addXp')) $user->addXp(150);
            } else {
                $this->aiAnalysis = "Erro HTTP " . $response->status() . ": " . $response->body();
            }
        } catch (\Throwable $e) {
            $this->aiAnalysis = "Erro: " . $e->getMessage();
        }

        $this->isAnalyzing = false;
    }

    public function render()
    {
        $user = auth()->user();
        $month = now()->month;
        $year = now()->year;

        [$earned, $spent] = $this->getMonthlyTotals($month, $year);

        $prevDate = now()->subMonth();
        [$prevEarned, $prevSpent] = $this->getMonthlyTotals($prevDate->month, $prevDate->year);
        $hasPrevData = $prevEarned > 0 || $prevSpent > 0;

        $netWorth = (float) $user->currentWorkspace->getLiquidezAtual()
                  + (float) $user->investments->sum(fn($i) => $i->quantity * $i->current_price);

        $healthScore = $this->calculateHealthScore($earned, $spent);
        $prevHealthScore = $this->calculateHealthScore($prevEarned, $prevSpent);

        $manualInsights = [];
        if ($spent > $earned && $earned > 0) {
            $manualInsights[] = ['type' => 'danger', 'icon' => 'arrow-trending-down', 'title' => 'Saldo Negativo', 'text' => 'Estás a gastar mais do que ganhas este mês.'];
        }
        if ($earned > 0 && ($spent / $earned) > 0.9) {
            $manualInsights[] = ['type' => 'warning', 'icon' => 'bell', 'title' => 'Margem Crítica', 'text' => 'Resta-te menos de 10% do teu rendimento livre.'];
        }

        return view('livewire.ai-insights', [
    'totalEarned' => $earned,
    'totalSpent' => $spent,
    'netWorth' => $netWorth,
    'healthScore' => $healthScore,
    'healthScoreDelta' => $hasPrevData ? ($healthScore - $prevHealthScore) : null,
    'earnedDelta' => $this->percentDelta($earned, $prevEarned),
    'spentDelta' => $this->percentDelta($spent, $prevSpent),
    'netWorthDelta' => $this->trackNetWorthSnapshot($netWorth, $year, $month),
    'insights' => $manualInsights,
    'reportGeneratedAt' => $this->lastGeneratedAt ? Carbon::parse($this->lastGeneratedAt) : null,  // 👈 nome novo
]);
    }

    private function calculateHealthScore($earned, $spent)
    {
        if ($earned <= 0) return 0;
        $ratio = ($spent / $earned) * 100;
        $score = 100 - $ratio;
        return (int) max(0, min(100, $score + 20));
    }
}

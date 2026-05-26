<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Expense, Category, Income, Goal, Investment};
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
class AiInsights extends Component
{
    public bool $isAnalyzing = false;
    public string $aiAnalysis = '';

    /**
     * Lógica de Inteligência Artificial via Gemini API
     */
    public function generateInsights()
    {
        $this->isAnalyzing = true;
        $this->aiAnalysis = '';

        $user = auth()->user();
        $month = now()->month;
        $year = now()->year;

        // 1. RECOLHA DE DADOS FINANCEIROS TOTAIS
        $totalEarned = (float) Income::where('user_id', $user->id)->whereMonth('received_at', $month)->whereYear('received_at', $year)->sum('amount')
                     + (float) $user->recurringIncomes()->where('is_active', true)->sum('amount');

        $totalSpent = (float) Expense::where('user_id', $user->id)->where('is_company', false)->whereMonth('spent_at', $month)->whereYear('spent_at', $year)->sum('amount');

        $expensesByCategory = Expense::selectRaw('categories.name as category, sum(expenses.amount) as total')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $user->id)
            ->where('expenses.is_company', false)
            ->whereMonth('expenses.spent_at', $month)
            ->groupBy('categories.name')->get()->pluck('total', 'category')->toArray();

        $invValue = (float) $user->investments->sum(fn($i) => $i->quantity * $i->current_price);
        $savings = $totalEarned - $totalSpent;
        $savingsRate = $totalEarned > 0 ? ($savings / $totalEarned) * 100 : 0;

        // 2. CONSTRUÇÃO DO PROMPT ESTRATÉGICO
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

        // 3. CHAMADA À API GEMINI
        try {
            $apiKey = env('GEMINI_API_KEY');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

            $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(30)->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->aiAnalysis = $data['candidates'][0]['content']['parts'][0]['text'] ?? "IA indisponível no momento.";

                // Bónus de XP por consultar a IA
                if(method_exists($user, 'addXp')) $user->addXp(150);

            } else {
                $this->aiAnalysis = "Erro na resposta da IA. Verifica a tua chave API.";
            }
        } catch (\Exception $e) {
            $this->aiAnalysis = "Erro de ligação: " . $e->getMessage();
        }

        $this->isAnalyzing = false;
    }

    public function render()
    {
        $user = auth()->user();
        $month = now()->month;

        // Dados para os Widgets Manuais
        $earned = (float) Income::where('user_id', $user->id)->whereMonth('received_at', $month)->sum('amount')
                + (float) $user->recurringIncomes()->where('is_active', true)->sum('amount');

        $spent = (float) Expense::where('user_id', $user->id)->where('is_company', false)->whereMonth('spent_at', $month)->sum('amount');

        $netWorth = (float) $user->currentWorkspace->getLiquidezAtual()
                  + (float) $user->investments->sum(fn($i) => $i->quantity * $i->current_price);

        // Lógica de Alertas Manuais (Fallback da IA)
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
            'healthScore' => $this->calculateHealthScore($earned, $spent),
            'insights' => $manualInsights
        ]);
    }

    private function calculateHealthScore($earned, $spent)
    {
        if ($earned <= 0) return 0;
        $ratio = ($spent / $earned) * 100;
        $score = 100 - $ratio;
        return (int) max(0, min(100, $score + 20)); // Bónus de estabilidade
    }
}

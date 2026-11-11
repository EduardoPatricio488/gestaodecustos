<?php

namespace App\Livewire;

use App\Models\Investment;
use App\Models\InvestmentPortfolioAnalysis;
use App\Models\BankAccount;
use App\Models\InvestmentIncome;
use App\Models\Income;
use App\Models\Expense;
use App\Models\RecurringIncome;
use App\Services\DebtInstrumentCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class InvestmentsHub extends Component
{
    use WithFileUploads;

    public $editingId = null;

    public array $suggestions = [];

    public $search = '';

    public bool $showNetValues = false;

    public $filterType = 'Todos';

    public bool $isRefreshing = false;

    public ?string $lastUpdated = null;

    public $symbol;

    public $name;

    public $isin;

    public $type = 'Acao';

    public $exchange;

    public $network;

    public $provider;

    public $broker;

    public ?int $bank_account_id = null;

    public $operation_date;

    public $quantity;

    public $average_price;

    public $fees = 0;

    public $total_amount;

    public $interest_rate;

    public $loyalty_bonus;

    public $capitalization_date;

    public $issuer;

    public $series;

    public $product_type = 'CA';

    public array $recentCompanies = [];

    public int $highlightIndex = -1;

    public $companyQuery = '';

    public $tab = 'portfolio';

    public $companyAnalysis = null;

    public ?string $portfolioAdvisorAnalysis = null;

    public bool $isAnalyzingPortfolio = false;

    public bool $portfolioAnalysisCompleted = false;

    public ?string $portfolioAnalysisDate = null;

    public ?int $selectedPortfolioAnalysisId = null;

    public string $aiProvider = 'openrouter';

    private const ALPHA_VANTAGE_KEY = null;

    private const CACHE_PRICES_TTL = 60;

    private const CACHE_TICKER_TTL = 300;

    public function mount(): void
    {
        $this->lastUpdated = now()->format('H:i');
        $this->loadLatestPortfolioAnalysis();
    }

    private function portfolioAnalysisQuery()
    {
        $user = Auth::user();

        return InvestmentPortfolioAnalysis::query()
            ->where('user_id', $user->id)
            ->where('workspace_id', $user->current_workspace_id);
    }

    private function loadLatestPortfolioAnalysis(): void
    {
        $latest = $this->portfolioAnalysisQuery()->latest('created_at')->first();

        if ($latest) {
            $this->selectedPortfolioAnalysisId = $latest->id;
            $this->portfolioAdvisorAnalysis = $latest->analysis;
            $this->portfolioAnalysisDate = $latest->created_at?->format('d/m/Y H:i');
        }
    }

    public function showPortfolioAnalysis(int $analysisId): void
    {
        $analysis = $this->portfolioAnalysisQuery()->findOrFail($analysisId);

        $this->selectedPortfolioAnalysisId = $analysis->id;
        $this->portfolioAdvisorAnalysis = $analysis->analysis;
        $this->portfolioAnalysisDate = $analysis->created_at?->format('d/m/Y H:i');
        $this->portfolioAnalysisCompleted = false;
    }

    private array $companyList = [
        ['ticker' => 'AAPL', 'name' => 'Apple', 'logo' => 'https://logo.clearbit.com/apple.com'],
        ['ticker' => 'MSFT', 'name' => 'Microsoft', 'logo' => 'https://logo.clearbit.com/microsoft.com'],
        ['ticker' => 'GOOGL', 'name' => 'Alphabet (Google)', 'logo' => 'https://logo.clearbit.com/google.com'],
        ['ticker' => 'AMZN', 'name' => 'Amazon', 'logo' => 'https://logo.clearbit.com/amazon.com'],
        ['ticker' => 'META', 'name' => 'Meta Platforms', 'logo' => 'https://logo.clearbit.com/meta.com'],
        ['ticker' => 'TSLA', 'name' => 'Tesla', 'logo' => 'https://logo.clearbit.com/tesla.com'],
        ['ticker' => 'NVDA', 'name' => 'Nvidia', 'logo' => 'https://logo.clearbit.com/nvidia.com'],
        ['ticker' => 'NFLX', 'name' => 'Netflix', 'logo' => 'https://logo.clearbit.com/netflix.com'],
        ['ticker' => 'AMD', 'name' => 'AMD', 'logo' => 'https://logo.clearbit.com/amd.com'],
        ['ticker' => 'INTC', 'name' => 'Intel', 'logo' => 'https://logo.clearbit.com/intel.com'],
        ['ticker' => 'ORCL', 'name' => 'Oracle', 'logo' => 'https://logo.clearbit.com/oracle.com'],
        ['ticker' => 'IBM', 'name' => 'IBM', 'logo' => 'https://logo.clearbit.com/ibm.com'],
        ['ticker' => 'ADBE', 'name' => 'Adobe', 'logo' => 'https://logo.clearbit.com/adobe.com'],
        ['ticker' => 'CRM', 'name' => 'Salesforce', 'logo' => 'https://logo.clearbit.com/salesforce.com'],
        ['ticker' => 'V', 'name' => 'Visa', 'logo' => 'https://logo.clearbit.com/visa.com'],
        ['ticker' => 'MA', 'name' => 'Mastercard', 'logo' => 'https://logo.clearbit.com/mastercard.com'],
    ];

    public function updatedCompanyQuery(): void
    {
        $query = strtoupper(trim($this->companyQuery));
        if ($query === '') {
            $this->suggestions = [];
            $this->highlightIndex = -1;

            return;
        }
        $this->suggestions = collect($this->companyList)
            ->filter(fn ($item) => str_contains(strtoupper($item['ticker']), $query) || str_contains(strtoupper($item['name']), $query))
            ->take(6)->values()->toArray();
    }

    public function getCompanyData($ticker)
    {
        return collect($this->companyList)->firstWhere('ticker', $ticker);
    }

    public function selectSuggestion($ticker): void
    {
        $this->companyQuery = $ticker;
        $this->suggestions = [];
        $this->highlightIndex = -1;
        if (! in_array($ticker, $this->recentCompanies, true)) {
            array_unshift($this->recentCompanies, $ticker);
            $this->recentCompanies = array_slice($this->recentCompanies, 0, 5);
        }
        $this->analyzeCompany();
    }

    public function moveHighlight($direction): void
    {
        if (empty($this->suggestions)) {
            return;
        }
        $this->highlightIndex = $direction === 'up'
            ? ($this->highlightIndex <= 0 ? count($this->suggestions) - 1 : $this->highlightIndex - 1)
            : ($this->highlightIndex >= count($this->suggestions) - 1 ? 0 : $this->highlightIndex + 1);
    }

    public function confirmSelection(): void
    {
        if ($this->highlightIndex >= 0 && isset($this->suggestions[$this->highlightIndex])) {
            $this->selectSuggestion($this->suggestions[$this->highlightIndex]['ticker']);

            return;
        }
        $this->analyzeCompany();
    }

    private function analyzeWithOpenRouter(string $company, array $marketData): array
    {
        $apiKey = config('services.openrouter.api_key');
        if (blank($apiKey)) {
            return ['analysis_message' => 'A análise IA não está configurada neste ambiente.'];
        }
        if (blank($marketData['price'] ?? null)) {
            return ['analysis_message' => 'Não existem dados de mercado verificados suficientes para analisar esta empresa. A IA não irá inventar fundamentais, rácios ou uma recomendação.'];
        }

        $marketJson = json_encode($marketData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $prompt = "Analisa '{$company}' usando EXCLUSIVAMENTE estes dados de mercado verificados pela aplicação: {$marketJson}. Não inventes receita, margens, balanço, cashflow, P/E, P/B, dados sectoriais ou qualquer outro indicador ausente. Para cada indicador ausente usa null e indica dados insuficientes. Não atribuas Comprar/Manter/Vender quando os dados forem insuficientes. Responde em JSON estruturado com: market_summary, fundamentals, valuation, risks, strategy, score, recommendation, data_limitations.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])->connectTimeout((int) config('limits.ai.connect_timeout_seconds', 5))->timeout((int) config('limits.ai.request_timeout_seconds', 30))->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => config('services.openrouter.model', 'openai/gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => 'És um analista financeiro. Regra absoluta: nunca inventes dados. Só podes afirmar métricas presentes nos dados verificados fornecidos pela aplicação.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.1,
            'max_tokens' => 2000,
        ]);

        if (! $response->successful()) {
            Log::error('InvestmentsHub: erro na análise OpenRouter', ['status' => $response->status(), 'model' => config('services.openrouter.model', 'openai/gpt-4o-mini')]);

            return ['analysis_message' => 'Não foi possível obter a análise IA neste momento.'];
        }

        $content = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', (string) ($response->json('choices.0.message.content') ?? '')) ?? '');
        $json = json_decode($content, true);
        if (! is_array($json) || $json === []) {
            return ['analysis_message' => 'A IA devolveu uma resposta sem dados estruturados para esta empresa.'];
        }

        return $json;
    }

    private function analyzeWithGemini(string $company, array $marketData): array
    {
        $apiKey = config('services.gemini.api_key');
        if (blank($apiKey)) {
            return ['analysis_message' => 'A análise Gemini não está configurada neste ambiente.'];
        }
        if (blank($marketData['price'] ?? null)) {
            return ['analysis_message' => 'Não existem dados de mercado verificados suficientes para analisar esta empresa.'];
        }

        $marketJson = json_encode($marketData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $prompt = "Analisa '{$company}' apenas com estes dados verificados: {$marketJson}. Não inventes fundamentais, rácios ou recomendação. Dados ausentes devem ficar null. Responde em JSON.";
        $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key='.$apiKey, [
            'contents' => [['parts' => [['text' => $prompt]]]],
        ]);
        if (! $response->successful()) {
            return ['analysis_message' => 'Não foi possível obter a análise Gemini neste momento.'];
        }
        $raw = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        return json_decode($raw, true) ?: ['analysis_message' => 'A IA devolveu uma resposta sem dados estruturados.'];
    }

    private function fetchMarketDataYahoo15(string $symbol): array
    {
        try {
            $key = config('services.market_data.yahoo_api_key');
            if (filled($key)) {
                $response = Http::withHeaders(['X-RapidAPI-Key' => $key, 'X-RapidAPI-Host' => 'yahoo-finance15.p.rapidapi.com'])->timeout(15)->get('https://yahoo-finance15.p.rapidapi.com/api/yahoo/qu/quote/'.urlencode($symbol));
                $body = $response->json()['body'] ?? [];
                if (is_array($body) && ! empty($body)) {
                    return $this->normalizeMarketData($body, $symbol);
                }
            }

            return $this->fetchMarketDataFromYahooChart($symbol);
        } catch (\Throwable $e) {
            Log::warning('InvestmentsHub: fonte Yahoo indisponível', ['symbol' => $symbol, 'message' => $e->getMessage()]);

            return $this->fetchMarketDataFromYahooChart($symbol);
        }
    }

    private function normalizeMarketData(array $body, string $symbol): array
    {
        $primary = $body['primaryData'] ?? [];

        return [
            'symbol' => $body['symbol'] ?? $symbol,
            'name' => $body['companyName'] ?? null,
            'price' => $primary['lastSalePrice'] ?? null,
            'change' => $primary['percentageChange'] ?? null,
            'netChange' => $primary['netChange'] ?? null,
            'volume' => $primary['volume'] ?? null,
            'day_range' => $body['keyStats']['dayrange']['value'] ?? null,
            '52w_range' => $body['keyStats']['fiftyTwoWeekHighLow']['value'] ?? null,
            'marketStatus' => $body['marketStatus'] ?? null,
        ];
    }

    private function fetchMarketDataFromYahooChart(string $symbol): array
    {
        try {
            $response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0 FinanceProIA/1.0'])->timeout(15)->get('https://query1.finance.yahoo.com/v8/finance/chart/'.urlencode($symbol), ['range' => '1d', 'interval' => '1d', 'events' => 'history']);
            if (! $response->successful()) {
                return $this->fetchMarketDataFromAlphaVantage($symbol);
            }
            $meta = $response->json('chart.result.0.meta', []);
            $price = $meta['regularMarketPrice'] ?? null;
            $previous = $meta['previousClose'] ?? $meta['chartPreviousClose'] ?? null;
            $change = is_numeric($price) && is_numeric($previous) && (float) $previous !== 0.0 ? ((float) $price - (float) $previous) / (float) $previous * 100 : null;
            $data = [
                'symbol' => $meta['symbol'] ?? $symbol,
                'name' => $meta['longName'] ?? $meta['shortName'] ?? null,
                'price' => $price,
                'change' => $change !== null ? sprintf('%+.2f%%', $change) : null,
                'netChange' => is_numeric($price) && is_numeric($previous) ? (float) $price - (float) $previous : null,
                'volume' => $meta['regularMarketVolume'] ?? null,
                'day_range' => isset($meta['regularMarketDayLow'], $meta['regularMarketDayHigh']) ? $meta['regularMarketDayLow'].' - '.$meta['regularMarketDayHigh'] : null,
                '52w_range' => isset($meta['fiftyTwoWeekLow'], $meta['fiftyTwoWeekHigh']) ? $meta['fiftyTwoWeekLow'].' - '.$meta['fiftyTwoWeekHigh'] : null,
                'marketStatus' => $meta['marketState'] ?? null,
            ];

            return filled($data['price']) ? $data : $this->fetchMarketDataFromAlphaVantage($symbol);
        } catch (\Throwable) {
            return $this->fetchMarketDataFromAlphaVantage($symbol);
        }
    }

    private function fetchMarketDataFromAlphaVantage(string $symbol): array
    {
        $key = config('services.market_data.alpha_vantage_api_key');
        if (blank($key)) {
            return $this->fetchMarketDataFromStooq($symbol);
        }
        try {
            $response = Http::timeout(15)->get('https://www.alphavantage.co/query', ['function' => 'GLOBAL_QUOTE', 'symbol' => strtoupper($symbol), 'apikey' => $key]);
            $quote = $response->json('Global Quote', []);
            $price = $quote['05. price'] ?? null;
            if (! $response->successful() || blank($price)) {
                return $this->fetchMarketDataFromStooq($symbol);
            }

            return [
                'symbol' => $quote['01. symbol'] ?? strtoupper($symbol), 'name' => null,
                'price' => is_numeric($price) ? (float) $price : $price,
                'change' => $quote['10. change percent'] ?? null,
                'netChange' => $quote['09. change'] ?? null,
                'volume' => $quote['06. volume'] ?? null,
                'day_range' => isset($quote['04. low'], $quote['03. high']) ? $quote['04. low'].' - '.$quote['03. high'] : null,
                '52w_range' => null, 'marketStatus' => 'closed',
            ];
        } catch (\Throwable) {
            return $this->fetchMarketDataFromStooq($symbol);
        }
    }

    private function fetchMarketDataFromStooq(string $symbol): array
    {
        $response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0 FinanceProIA/1.0'])->timeout(15)->get('https://stooq.com/q/d/l/', ['s' => strtolower(trim($symbol)).'.us', 'i' => 'd', 'd1' => now()->subYear()->format('Ymd')]);
        if (! $response->successful()) {
            return ['error' => 'Fonte de cotações indisponível.'];
        }
        $rows = collect(preg_split('/\r\n|\r|\n/', trim($response->body())))->filter(fn ($line) => $line !== '' && ! str_starts_with($line, 'No data'))->values();
        if ($rows->count() < 2) {
            return ['error' => 'Fonte de cotações indisponível.'];
        }
        $header = str_getcsv($rows->shift());
        $records = $rows->map(fn ($row) => array_combine($header, str_getcsv($row)) ?: [])->filter(fn ($row) => isset($row['Close']))->values();
        $latest = $records->last();
        $previous = $records->count() > 1 ? $records->get($records->count() - 2) : null;
        $price = is_numeric($latest['Close'] ?? null) ? (float) $latest['Close'] : null;
        $previousPrice = is_numeric($previous['Close'] ?? null) ? (float) $previous['Close'] : null;
        $change = $price !== null && $previousPrice ? (($price - $previousPrice) / $previousPrice) * 100 : null;
        $high = $records->max(fn ($row) => (float) ($row['High'] ?? 0));
        $low = $records->min(fn ($row) => (float) ($row['Low'] ?? 0));

        return [
            'symbol' => strtoupper($symbol), 'name' => null, 'price' => $price,
            'change' => $change !== null ? sprintf('%+.2f%%', $change) : null,
            'netChange' => $price !== null && $previousPrice !== null ? $price - $previousPrice : null,
            'volume' => $latest['Volume'] ?? null,
            'day_range' => isset($latest['Low'], $latest['High']) ? $latest['Low'].' - '.$latest['High'] : null,
            '52w_range' => $low > 0 && $high > 0 ? $low.' - '.$high : null,
            'marketStatus' => 'closed',
        ];
    }

    public function analyzeCompany(): void
    {
        $company = trim($this->companyQuery);
        if (! $company) {
            $this->companyAnalysis = ['error' => 'Introduz o nome ou ticker da empresa.'];

            return;
        }
        $market = $this->fetchMarketDataYahoo15($company);
        $analysis = $this->aiProvider === 'gemini' ? $this->analyzeWithGemini($company, $market) : $this->analyzeWithOpenRouter($company, $market);
        $analysis['market_data'] = $market;
        $this->companyAnalysis = $analysis;
        $this->dispatch('companyAnalyzed', $analysis);
    }


    /**
     * Gera uma leitura global da carteira e da capacidade financeira do utilizador.
     * A análise usa apenas movimentos e posições existentes no workspace.
     */
    private function buildPortfolioAdvisorData(): array
    {
        $user = Auth::user();
        $workspaceId = $user->current_workspace_id;
        $isPersonalWorkspace = $user->currentWorkspace?->type === 'personal';
        $from = now()->subDays(90)->startOfDay();
        $to = now()->endOfDay();

        $assets = Investment::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->get()
            ->map(function ($asset) {
                $cost = ((float) $asset->quantity * (float) $asset->average_price) + (float) ($asset->fees ?? 0);
                $price = (float) ($asset->current_price ?: $asset->average_price);
                $value = (float) $asset->quantity * $price;

                return [
                    'name' => $asset->name ?: $asset->symbol ?: 'Ativo sem nome',
                    'symbol' => $asset->symbol,
                    'type' => $asset->type ?: 'Outro',
                    'quantity' => (float) $asset->quantity,
                    'cost' => $cost,
                    'value' => $value,
                    'profit' => $value - $cost,
                    'profit_percent' => $cost > 0 ? (($value - $cost) / $cost) * 100 : 0,
                ];
            });

        $invested = (float) $assets->sum('cost');
        $currentValue = (float) $assets->sum('value');
        $profit = $currentValue - $invested;
        $topAsset = $assets->sortByDesc('value')->first();
        $composition = $assets->groupBy('type')->map(fn ($group, $type) => [
            'type' => $type,
            'value' => (float) $group->sum('value'),
            'percentage' => $currentValue > 0 ? round(($group->sum('value') / $currentValue) * 100, 1) : 0,
        ])->values()->all();

        // O salário líquido vem do contrato de emprego configurado em Rendimentos Fixos.
        // Exclui o subsídio de alimentação para não o confundir com salário disponível.
        $salaryContracts = RecurringIncome::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->where('user_id', $user->id)
            ->where('source', 'emprego')
            ->where('is_active', true)
            ->get()
            ->reject(fn (RecurringIncome $income) => ($income->metadata['income_type'] ?? null) === 'meal_card_allowance');

        $monthlyIncome = (float) $salaryContracts->sum(function (RecurringIncome $income): float {
            $metadata = $income->metadata ?? [];
            $salaryNet = (float) $income->amount;
            $hasSeparateMealCard = ! empty($metadata['meal_card_income_id']);

            if (! $hasSeparateMealCard) {
                $salaryNet -= (float) ($metadata['calculated_sa'] ?? 0);
            }

            return max(0, $salaryNet);
        });

        $expenseQuery = Expense::where('workspace_id', $workspaceId)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->whereBetween('spent_at', [$from->toDateString(), $to->toDateString()]);

        $expenseCount = (clone $expenseQuery)->count();
        $expense90 = (float) (clone $expenseQuery)->sum('amount_converted');
        $monthlyExpense = $expense90 / 3;
        $monthlySurplus = $monthlyIncome - $monthlyExpense;

        return [
            'period_days' => 90,
            'income_records' => $salaryContracts->count(),
            'salary_configured' => $salaryContracts->isNotEmpty(),
            'expense_records' => $expenseCount,
            'monthly_income' => $monthlyIncome,
            'monthly_expenses' => $monthlyExpense,
            'monthly_surplus' => $monthlySurplus,
            'suggested_monthly_min' => max(0, $monthlySurplus * 0.20),
            'suggested_monthly_max' => max(0, $monthlySurplus * 0.30),
            'invested' => $invested,
            'current_value' => $currentValue,
            'profit' => $profit,
            'profit_percent' => $invested > 0 ? ($profit / $invested) * 100 : 0,
            'asset_count' => $assets->count(),
            'composition' => $composition,
            'top_asset' => $topAsset,
            'top_asset_percentage' => $topAsset && $currentValue > 0 ? round(($topAsset['value'] / $currentValue) * 100, 1) : 0,
            'assets' => $assets->values()->all(),
        ];
    }

    public function analyzePortfolio(): void
    {
        if ($this->isAnalyzingPortfolio) {
            return;
        }

        $this->isAnalyzingPortfolio = true;
        // O router gratuito escolhe o modelo disponível; a lista seguinte funciona como fallback automático em caso de erro/limite do fornecedor.
        $portfolioModel = 'openrouter/free';
        $portfolioFallbackModels = [
            'google/gemma-4-26b-a4b-it:free',
            'qwen/qwen3-4b:free',
            'meta-llama/llama-3.3-70b-instruct:free',
        ];

        // A análise completa pode demorar mais em modelos gratuitos do OpenRouter.
        if (function_exists('set_time_limit')) {
            @set_time_limit(180);
        }
        $this->portfolioAnalysisCompleted = false;
        $this->portfolioAdvisorAnalysis = 'A preparar os dados da tua carteira e a contactar o consultor IA...';

        try {
            $data = $this->buildPortfolioAdvisorData();
            $apiKey = config('services.openrouter.api_key');

            if (blank($apiKey)) {
                $this->portfolioAdvisorAnalysis = 'A ligação à IA não está configurada. Falta definir OPENROUTER_API_KEY no ficheiro .env deste ambiente.';
                return;
            }

            $context = [
                'currency' => Auth::user()->currentWorkspace?->currency ?? 'EUR',
                'portfolio' => [
                    'invested' => round($data['invested'], 2),
                    'current_value' => round($data['current_value'], 2),
                    'profit' => round($data['profit'], 2),
                    'profit_percent' => round($data['profit_percent'], 2),
                    'asset_count' => $data['asset_count'],
                    'composition' => $data['composition'],
                    'largest_position_share_percent' => $data['top_asset_percentage'],
                    'positions' => array_map(fn ($asset) => [
                        'name' => $asset['name'],
                        'symbol' => $asset['symbol'],
                        'type' => $asset['type'],
                        'current_value' => round($asset['value'], 2),
                        'profit' => round($asset['profit'], 2),
                        'profit_percent' => round($asset['profit_percent'], 2),
                    ], $data['assets']),
                ],
                'cashflow_last_90_days' => [
                    'monthly_average_income' => round($data['monthly_income'], 2),
                    'monthly_average_expenses' => round($data['monthly_expenses'], 2),
                    'monthly_average_surplus' => round($data['monthly_surplus'], 2),
                    'income_records' => $data['income_records'],
                    'expense_records' => $data['expense_records'],
                    'suggested_investment_range' => [
                        'minimum' => round($data['suggested_monthly_min'], 2),
                        'maximum' => round($data['suggested_monthly_max'], 2),
                    ],
                ],
            ];

            $systemPrompt = 'És um consultor de educação financeira prudente, claro e rigoroso. Analisa exclusivamente os dados fornecidos. Nunca inventes preços, rendimentos, despesas, taxas, previsões ou características dos ativos. Distingue factos de hipóteses. Não prometas retornos nem dês ordens de compra/venda. Se faltarem dados, diz explicitamente. Recomenda manter primeiro uma reserva de emergência adequada e não investir dinheiro necessário a curto prazo. Responde em português de Portugal.';
            $userPrompt = "Analisa a minha carteira e a minha capacidade de investimento com estes dados reais da aplicação:\n"
                .json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                ."\n\nEscreve uma análise completa mas objetiva, com cerca de 350-450 palavras, usando obrigatoriamente estes títulos: Diagnóstico geral; Pontos fortes; Riscos e concentração; Capacidade mensal para investir; Próximos passos concretos; Dados em falta. Explica que 20%-30% do excedente é apenas uma referência inicial, não uma garantia. Se não existirem movimentos suficientes nos últimos 90 dias, não apresentes o excedente como rendimento disponível confirmado. Termina cada secção e não deixes frases a meio.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->connectTimeout((int) config('limits.ai.connect_timeout_seconds', 10))->timeout((int) config('limits.ai.request_timeout_seconds', 90))->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $portfolioModel,
                'models' => $portfolioFallbackModels,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.2,
                'max_tokens' => 1800,
                'reasoning' => ['effort' => 'none'],
            ]);

            if (! $response->successful()) {
                Log::warning('InvestmentsHub: falha na análise do consultor de investimentos', [
                    'status' => $response->status(),
                    'response' => mb_substr($response->body(), 0, 1500),
                ]);

                $this->portfolioAdvisorAnalysis = match ($response->status()) {
                    401, 403 => 'A chave OpenRouter foi rejeitada. Confirma se OPENROUTER_API_KEY está correta e se a conta tem acesso ao modelo configurado.',
                    402 => 'A conta OpenRouter não tem saldo ou créditos disponíveis para executar esta análise.',
                    429 => 'Os modelos gratuitos estão temporariamente sem capacidade nos fornecedores. A aplicação tentou o encaminhamento alternativo; aguarda alguns minutos ou configura uma chave própria de fornecedor (BYOK) no OpenRouter.',
                    default => 'O serviço OpenRouter respondeu com o erro HTTP '.$response->status().'. Consulta o registo de Laravel para mais detalhes e tenta novamente.',
                };
                return;
            }

            $choice = $response->json('choices.0', []);

            // Os modelos OpenRouter podem devolver content como texto ou como uma lista de blocos.
            $extractMessageText = static function ($content): string {
                if (is_string($content)) {
                    return trim($content);
                }

                if (is_array($content)) {
                    $parts = [];

                    foreach ($content as $part) {
                        if (is_string($part)) {
                            $parts[] = $part;
                        } elseif (is_array($part) && isset($part['text']) && is_string($part['text'])) {
                            $parts[] = $part['text'];
                        }
                    }

                    return trim(implode("\\n", $parts));
                }

                return '';
            };

            $analysisText = $extractMessageText($choice['message']['content'] ?? null);
            if ($analysisText === '') {
                $analysisText = $extractMessageText($response->json('output_text'));
            }

            $finishReason = $choice['finish_reason'] ?? null;

            if ($analysisText === '') {
                Log::warning('InvestmentsHub: resposta OpenRouter sem conteúdo textual', [
                    'model_requested' => $portfolioModel,
                    'model_returned' => $response->json('model'),
                    'provider' => $response->json('provider'),
                    'finish_reason' => $finishReason,
                    'choice_keys' => array_keys($choice),
                    'message_keys' => array_keys($choice['message'] ?? []),
                    'response' => mb_substr($response->body(), 0, 4000),
                ]);
            }

            // Alguns modelos gratuitos cortam a resposta sem indicar corretamente o motivo.
            // Também verificamos se chegou à última secção pedida antes de aceitar a análise.
            $needsContinuation = $analysisText !== '' && (
                $finishReason === 'length'
                || ! str_contains(mb_strtolower($analysisText), 'dados em falta')
            );

            if ($needsContinuation) {
                $continuation = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ])->connectTimeout((int) config('limits.ai.connect_timeout_seconds', 10))->timeout((int) config('limits.ai.request_timeout_seconds', 90))->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $portfolioModel,
                    'models' => $portfolioFallbackModels,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                        ['role' => 'assistant', 'content' => $analysisText],
                        ['role' => 'user', 'content' => 'Continua exatamente a partir do ponto onde paraste. Não repitas o texto anterior. Conclui todas as secções em falta e termina a análise.'],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 1200,
                    'reasoning' => ['effort' => 'none'],
                ]);

                if ($continuation->successful()) {
                    $continuedContent = $continuation->json('choices.0.message.content');
                    $continuedText = $extractMessageText($continuedContent);
                    if ($continuedText !== '') {
                        $analysisText .= "\n\n".$continuedText;
                    }
                    $needsContinuation = ($continuation->json('choices.0.finish_reason') ?? null) === 'length'
                        || ! str_contains(mb_strtolower($analysisText), 'dados em falta');
                } else {
                    Log::warning('InvestmentsHub: não foi possível obter a continuação da análise', [
                        'status' => $continuation->status(),
                        'response' => mb_substr($continuation->body(), 0, 2500),
                    ]);
                }
            }

            if ($analysisText === '') {
                $this->portfolioAdvisorAnalysis = 'O modelo de IA configurado respondeu sem conteúdo de texto. A resposta técnica foi guardada no log da aplicação. Experimenta selecionar outro modelo gratuito na configuração OpenRouter ou consulta storage/logs/laravel.log para identificar o motivo.';
                return;
            }

            if ($needsContinuation) {
                $analysisText .= "\n\nNota: o modelo não conseguiu concluir todas as secções nesta tentativa. Volta a gerar a análise para obter uma versão completa.";
            }

            $saved = InvestmentPortfolioAnalysis::create([
                'user_id' => Auth::id(),
                'workspace_id' => Auth::user()->current_workspace_id,
                'analysis' => $analysisText,
                'snapshot' => $context,
                'model' => $response->json('model') ?: $portfolioModel,
            ]);

            $this->selectedPortfolioAnalysisId = $saved->id;
            $this->portfolioAdvisorAnalysis = $saved->analysis;
            $this->portfolioAnalysisDate = $saved->created_at?->format('d/m/Y H:i');
            $this->portfolioAnalysisCompleted = true;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('InvestmentsHub: timeout ou falha de ligação ao consultor IA', [
                'message' => $e->getMessage(),
            ]);

            $this->portfolioAdvisorAnalysis = 'A IA demorou demasiado tempo a responder ou a ligação foi interrompida. Tenta novamente dentro de alguns instantes.';
        } catch (\Throwable $e) {
            Log::error('InvestmentsHub: exceção ao analisar carteira', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            $this->portfolioAdvisorAnalysis = 'Ocorreu um erro ao contactar o consultor IA. Consulta os registos da aplicação para identificar a causa.';
        } finally {
            $this->isAnalyzingPortfolio = false;
        }
    }

    public function switchTab($tab): void
    {
        $this->tab = $tab;
    }

    public function setType(string $newType): void
    {
        $this->type = $newType;
        $this->reset(['exchange', 'network', 'provider', 'interest_rate', 'loyalty_bonus', 'capitalization_date', 'issuer', 'series', 'product_type', 'bank_account_id']);
        if ($newType === 'Divida') {
            $this->average_price = 1.00;
            $this->fees = 0;
            $this->issuer = 'IGCP / Estado Português';
            $this->broker = 'AforroNet';
            $this->product_type = 'CA';
        }
    }

    public function toggleNetValues(): void
    {
        $this->showNetValues = ! $this->showNetValues;
    }

    public function setFilter(string $type): void
    {
        $this->filterType = $type;
    }

    public function updatedTotalAmount(): void
    {
        if (is_numeric($this->total_amount) && is_numeric($this->average_price) && $this->average_price > 0) {
            $this->quantity = round((max(0, (float) $this->total_amount - (float) ($this->fees ?? 0))) / (float) $this->average_price, 4);
        }
    }

    public function updatedQuantity(): void
    {
        if (is_numeric($this->quantity) && is_numeric($this->average_price)) {
            $this->total_amount = round((float) $this->quantity * (float) $this->average_price + (float) ($this->fees ?? 0), 2);
        }
    }

    public function updatedAveragePrice(): void
    {
        if ($this->total_amount > 0) {
            $this->updatedTotalAmount();
        } elseif ($this->quantity > 0) {
            $this->updatedQuantity();
        }
    }

    public function updatedFees(): void
    {
        if (is_numeric($this->quantity) && is_numeric($this->average_price)) {
            $this->updatedQuantity();
        }
    }

    private function fetchCryptoPrices(array $symbols): array
    {
        if (empty($symbols)) {
            return [];
        }
        $idMap = ['BTC' => 'bitcoin', 'ETH' => 'ethereum', 'SOL' => 'solana', 'ADA' => 'cardano', 'DOT' => 'polkadot', 'MATIC' => 'matic-network', 'AVAX' => 'avalanche-2', 'LINK' => 'chainlink', 'UNI' => 'uniswap', 'ATOM' => 'cosmos', 'XRP' => 'ripple', 'LTC' => 'litecoin', 'DOGE' => 'dogecoin', 'SHIB' => 'shiba-inu', 'BNB' => 'binancecoin', 'TRX' => 'tron', 'TON' => 'the-open-network', 'SUI' => 'sui', 'APT' => 'aptos', 'OP' => 'optimism', 'ARB' => 'arbitrum', 'INJ' => 'injective-protocol', 'FET' => 'fetch-ai', 'NEAR' => 'near', 'FTM' => 'fantom', 'ALGO' => 'algorand', 'XLM' => 'stellar', 'VET' => 'vechain', 'ICP' => 'internet-computer', 'HBAR' => 'hedera-hashgraph'];
        $ids = collect($symbols)->map(fn ($s) => $idMap[strtoupper($s)] ?? strtolower($s))->unique()->implode(',');

        return Cache::remember('coingecko_prices_'.md5($ids), self::CACHE_PRICES_TTL, function () use ($ids, $idMap) {
            try {
                $res = Http::timeout(10)->withHeaders(['Accept' => 'application/json'])->get('https://api.coingecko.com/api/v3/simple/price', ['ids' => $ids, 'vs_currencies' => 'eur']);
                if (! $res->ok()) {
                    return [];
                }
                $prices = [];
                $reverseMap = array_flip($idMap);
                foreach ($res->json() as $id => $data) {
                    $prices[strtoupper($reverseMap[$id] ?? $id)] = $data['eur'] ?? null;
                }

                return $prices;
            } catch (\Throwable) {
                return [];
            }
        });
    }

    private function fetchStockPrice(string $symbol): ?float
    {
        return Cache::remember('stock_price_'.strtoupper($symbol), self::CACHE_PRICES_TTL, function () use ($symbol) {
            $price = $this->fetchFromYahoo($symbol);
            if ($price !== null) {
                return $price;
            }

            return $this->fetchFromAlphaVantage($symbol);
        });
    }

    private function fetchFromYahoo(string $symbol): ?float
    {
        try {
            $res = Http::timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0', 'Accept' => 'application/json'])->get("https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}", ['interval' => '1d', 'range' => '1d']);
            if (! $res->ok()) {
                return null;
            }
            $data = $res->json();
            $price = $data['chart']['result'][0]['meta']['regularMarketPrice'] ?? null;
            if (! $price) {
                return null;
            }
            $currency = strtoupper($data['chart']['result'][0]['meta']['currency'] ?? 'EUR');
            if ($currency === 'USD') {
                $rate = $this->getEurUsdRate();
                if ($rate === null) {
                    return null;
                } $price *= $rate;
            } elseif ($currency === 'GBP') {
                $rate = $this->getEurGbpRate();
                if ($rate === null) {
                    return null;
                } $price *= $rate;
            } elseif ($currency === 'GBP' || $currency === 'GBp') {
                return null;
            }

            return (float) $price;
        } catch (\Throwable) {
            return null;
        }
    }

    private function fetchFromAlphaVantage(string $symbol): ?float
    {
        $key = config('services.market_data.alpha_vantage_api_key');
        if (blank($key)) {
            return null;
        }
        try {
            $res = Http::timeout(8)->get('https://www.alphavantage.co/query', ['function' => 'GLOBAL_QUOTE', 'symbol' => $symbol, 'apikey' => $key]);
            $price = $res->json()['Global Quote']['05. price'] ?? null;

            return $price ? (float) $price : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function getEurUsdRate(): ?float
    {
        return Cache::remember('fx_eur_usd', 3600, function () {
            try {
                $res = Http::timeout(5)->get('https://api.frankfurter.app/latest', ['from' => 'USD', 'to' => 'EUR']);
                $rate = $res->json()['rates']['EUR'] ?? null;

                return is_numeric($rate) ? (float) $rate : null;
            } catch (\Throwable) {
                return null;
            }
        });
    }

    private function getEurGbpRate(): ?float
    {
        return Cache::remember('fx_eur_gbp', 3600, function () {
            try {
                $res = Http::timeout(5)->get('https://api.frankfurter.app/latest', ['from' => 'GBP', 'to' => 'EUR']);
                $rate = $res->json()['rates']['EUR'] ?? null;

                return is_numeric($rate) ? (float) $rate : null;
            } catch (\Throwable) {
                return null;
            }
        });
    }

    public function refreshPrices(bool $silent = false): bool
    {
        $this->isRefreshing = true;

        try {
            $user = Auth::user();
            $assets = Investment::where('workspace_id', $user->current_workspace_id)
                ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->get();

            $updated = 0;
            foreach ($assets->where('type', 'Divida') as $debtAsset) {
                if (! $debtAsset->operation_date || ! $debtAsset->interest_rate) {
                    continue;
                }

                $debtAsset->update(['current_price' => DebtInstrumentCalculator::process($debtAsset)]);
                $updated++;
            }

            $cryptoSymbols = $assets->where('type', 'Cripto')->pluck('symbol')->filter()->unique()->values()->toArray();
            $ids = collect($cryptoSymbols)->map(fn ($symbol) => $this->coinGeckoId($symbol))->unique()->implode(',');
            if ($ids !== '') {
                Cache::forget('coingecko_prices_'.md5($ids));
            }

            $cryptoPrices = $this->fetchCryptoPrices($cryptoSymbols);
            $stockAssets = $assets->whereIn('type', ['Acao', 'ETF', 'Fundo']);
            $stockPrices = [];

            foreach ($stockAssets->pluck('symbol')->filter()->unique() as $symbol) {
                Cache::forget('stock_price_'.strtoupper($symbol));
                $price = $this->fetchStockPrice($symbol);
                if ($price !== null && $price > 0) {
                    $stockPrices[strtoupper($symbol)] = $price;
                }
            }

            foreach ($assets->whereIn('type', ['Acao', 'ETF', 'Fundo', 'Cripto']) as $asset) {
                $ticker = strtoupper((string) $asset->symbol);

                if (isset($cryptoPrices[$ticker]) && $cryptoPrices[$ticker] > 0) {
                    $asset->update(['current_price' => $cryptoPrices[$ticker]]);
                    $updated++;
                } elseif (isset($stockPrices[$ticker]) && $stockPrices[$ticker] > 0) {
                    $asset->update(['current_price' => $stockPrices[$ticker]]);
                    $updated++;
                }
            }

            $this->lastUpdated = now()->format('H:i');
            if (! $silent) {
                $this->dispatch('toast', text: "{$updated} ativos sincronizados com o mercado.");
            }

            return true;
        } catch (\\Throwable $e) {
            Log::warning('InvestmentsHub: falha ao atualizar cotações', ['message' => $e->getMessage()]);
            if (! $silent) {
                $this->dispatch('toast', text: 'Erro ao sincronizar preços: '.$e->getMessage(), variant: 'error');
            }

            return false;
        } finally {
            $this->isRefreshing = false;
        }
    }

    private function coinGeckoId(string $symbol): string
    {
        $map = ['BTC' => 'bitcoin', 'ETH' => 'ethereum', 'SOL' => 'solana', 'ADA' => 'cardano', 'DOT' => 'polkadot', 'MATIC' => 'matic-network'];

        return $map[strtoupper($symbol)] ?? strtolower($symbol);
    }

    private function coinGeckoSymbolFromId(string $id): string
    {
        $map = ['bitcoin' => 'BTC', 'ethereum' => 'ETH', 'solana' => 'SOL', 'cardano' => 'ADA', 'matic-network' => 'MATIC'];

        return $map[strtolower($id)] ?? strtoupper($id);
    }

    public function createAsset(): void
    {
        $this->reset(['symbol', 'name', 'isin', 'quantity', 'average_price', 'fees', 'total_amount', 'exchange', 'network', 'provider', 'broker', 'operation_date', 'editingId', 'interest_rate', 'loyalty_bonus', 'capitalization_date', 'issuer', 'series', 'product_type', 'bank_account_id']);
        $this->type = 'Acao';
        $this->bank_account_id = null;
        $this->fees = 0;
        $this->operation_date = now()->toDateString();
        $this->dispatch('modal-show-add-investment');
    }

    public function editAsset(int $id): void
    {
        $this->editingId = $id;
        $asset = Investment::where('workspace_id', Auth::user()->current_workspace_id)
            ->when(Auth::user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);
        foreach (['symbol', 'name', 'isin', 'type', 'exchange', 'network', 'provider', 'broker', 'quantity', 'average_price', 'fees', 'interest_rate', 'loyalty_bonus', 'issuer', 'series'] as $field) {
            $this->{$field} = $asset->{$field};
        }
        $this->operation_date = $asset->operation_date?->toDateString();
        $this->bank_account_id = $asset->bank_account_id;
        $this->total_amount = round((float) $asset->quantity * (float) $asset->average_price + (float) ($asset->fees ?? 0), 2);
        $this->capitalization_date = $asset->capitalization_date?->toDateString();
        $this->product_type = $asset->product_type ?? 'CA';
        $this->dispatch('modal-show-add-investment');
    }

    public function save(): void
    {
        $this->validate(['bank_account_id' => 'required|integer|exists:bank_accounts,id', 'symbol' => 'required|string|max:20', 'isin' => 'nullable|string|max:12', 'broker' => 'nullable|string|max:100', 'operation_date' => 'nullable|date', 'quantity' => 'required|numeric|gt:0', 'product_type' => 'nullable|in:CA,CT', 'average_price' => 'required|numeric|gt:0', 'fees' => 'nullable|numeric|min:0', 'interest_rate' => 'nullable|numeric|min:0', 'capitalization_date' => 'nullable|date']);
        $account = BankAccount::where('workspace_id', Auth::user()->current_workspace_id)->when(Auth::user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', Auth::id()))->findOrFail($this->bank_account_id);
        $data = ['user_id' => Auth::id(), 'workspace_id' => Auth::user()->current_workspace_id, 'bank_account_id' => $account->id, 'symbol' => strtoupper(trim($this->symbol)), 'name' => $this->name ?: strtoupper(trim($this->symbol)), 'isin' => $this->isin ? strtoupper(trim($this->isin)) : null, 'type' => $this->type, 'exchange' => $this->exchange, 'network' => $this->network, 'provider' => $this->provider, 'broker' => $this->broker, 'operation_date' => $this->operation_date ?: null, 'quantity' => (float) $this->quantity, 'average_price' => (float) $this->average_price, 'fees' => (float) ($this->fees ?? 0), 'interest_rate' => $this->type === 'Divida' ? (float) ($this->interest_rate ?? 0) : null, 'loyalty_bonus' => $this->type === 'Divida' ? (float) ($this->loyalty_bonus ?? 0) : null, 'capitalization_date' => $this->type === 'Divida' ? ($this->capitalization_date ?: null) : null, 'issuer' => $this->issuer, 'series' => $this->series, 'product_type' => $this->type === 'Divida' ? $this->product_type : null];
        if ($this->editingId) {
            Investment::where('workspace_id', Auth::user()->current_workspace_id)
                ->when(Auth::user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', Auth::id()))
                ->findOrFail($this->editingId)
                ->update($data);
            $msg = 'Ativo atualizado no cofre!';
        } else {
            $data['current_price'] = (float) $this->average_price;
            Investment::create($data);
            $user = Auth::user();
            $user->awardXp(25, 'investimento registado');
            $msg = 'Novo capital registado com sucesso! '.$user->xpToastText(25, 'investimento registado');
        }
        $this->reset(['symbol', 'name', 'isin', 'quantity', 'average_price', 'fees', 'total_amount', 'exchange', 'network', 'provider', 'broker', 'operation_date', 'editingId', 'interest_rate', 'loyalty_bonus', 'capitalization_date', 'issuer', 'series', 'product_type', 'bank_account_id']);
        $this->dispatch('modal-close-add-investment');
        $this->dispatch('toast', text: $msg);
    }

    public function deleteAsset(int $id): void
    {
        Investment::where('user_id', Auth::id())->where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id)->delete();
        $this->dispatch('toast', text: 'Ativo removido do portefólio.');
    }

    public function render()
    {
        $user = Auth::user();
        $isPersonalWorkspace = $user->currentWorkspace?->type === 'personal';
        $query = Investment::where('workspace_id', $user->current_workspace_id)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id));
        if ($this->search) {
            $term = '%'.trim($this->search).'%';
            $query->where(fn ($q) => $q->where('symbol', 'like', $term)->orWhere('name', 'like', $term));
        }
        if ($this->filterType !== 'Todos') {
            $query->where('type', $this->filterType);
        }
        $myAssets = $query->latest()->get()->map(function ($asset) {
            $cost = (float) $asset->quantity * (float) $asset->average_price + (float) ($asset->fees ?? 0);
            $currentValue = (float) $asset->quantity * ($asset->current_price ?: (float) $asset->average_price);
            $asset->cost = $cost;
            $asset->current_value = $currentValue;
            $asset->pnl = $currentValue - $cost;
            $asset->pnl_percent = $cost > 0 ? ($asset->pnl / $cost) * 100 : 0;

            return $asset;
        });
        $totalInvested = $myAssets->sum('cost');
        $currentPortfolioValue = $myAssets->sum('current_value');
        $totalEstimatedTax = $myAssets->sum(fn ($asset) => ($asset->type !== 'Divida' && $asset->pnl > 0) ? $asset->pnl * 0.28 : 0);
        $totalProfit = $currentPortfolioValue - $totalInvested;
        $totalPnlPct = $totalInvested > 0 ? ($totalProfit / $totalInvested) * 100 : 0;
        $displayValue = $this->showNetValues ? ($currentPortfolioValue - $totalEstimatedTax) : $currentPortfolioValue;
        $displayProfit = $this->showNetValues ? ($totalProfit - $totalEstimatedTax) : $totalProfit;
        $composition = $myAssets->groupBy('type')->map(fn ($group) => ['total' => $group->sum('current_value'), 'percent' => $currentPortfolioValue > 0 ? round(($group->sum('current_value') / $currentPortfolioValue) * 100, 1) : 0]);

        $bankAccounts = BankAccount::where('workspace_id', $user->current_workspace_id)
            ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
            ->where('status', '!=', 'archived')->orderBy('name')->get();

        $advisorData = $this->buildPortfolioAdvisorData();

        $portfolioAnalysisHistory = $this->portfolioAnalysisQuery()
            ->latest('created_at')
            ->take(10)
            ->get(['id', 'created_at', 'model']);

        return view('livewire.investments-hub', ['bankAccounts' => $bankAccounts, 'myAssets' => $myAssets, 'totalInvested' => $totalInvested, 'currentValue' => $displayValue, 'totalProfit' => $displayProfit, 'totalPnlPct' => $totalPnlPct, 'composition' => $composition, 'bestPerformer' => $myAssets->sortByDesc('pnl_percent')->first(), 'worstPerformer' => $myAssets->sortBy('pnl_percent')->first(), 'highestExposure' => $myAssets->sortByDesc('current_value')->first(), 'marketData' => $this->buildMarketTicker(), 'estimatedTax' => $totalEstimatedTax, 'recentIncomes' => InvestmentIncome::where('workspace_id', Auth::user()->current_workspace_id)
                ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
                ->with('investment')->latest('reference_date')->take(10)->get(), 'totalIncomeNet' => InvestmentIncome::where('workspace_id', Auth::user()->current_workspace_id)
                ->when($isPersonalWorkspace, fn ($q) => $q->where('user_id', $user->id))
                ->sum('net_amount'), 'companyAnalysis' => $this->companyAnalysis, 'advisorData' => $advisorData, 'portfolioAdvisorAnalysis' => $this->portfolioAdvisorAnalysis, 'portfolioAnalysisHistory' => $portfolioAnalysisHistory, 'portfolioAnalysisDate' => $this->portfolioAnalysisDate, 'selectedPortfolioAnalysisId' => $this->selectedPortfolioAnalysisId, 'tab' => $this->tab]);
    }

    private function buildMarketTicker(): array
    {
        $ticker = [];
        $spy = Cache::remember('ticker_spy', self::CACHE_TICKER_TTL, function () {
            try {
                $res = Http::timeout(6)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get('https://query1.finance.yahoo.com/v8/finance/chart/SPY', ['interval' => '1d', 'range' => '1d']);
                $meta = $res->json()['chart']['result'][0]['meta'] ?? [];
                $price = $meta['regularMarketPrice'] ?? null;
                $prev = $meta['chartPreviousClose'] ?? null;
                if (! $price) {
                    return null;
                } $eurRate = $this->getEurUsdRate();
                if ($eurRate === null) {
                    return null;
                } $priceEur = $price * $eurRate;
                $prevEur = $prev ? $prev * $eurRate : null;
                $change = $prevEur ? (($priceEur - $prevEur) / $prevEur) * 100 : 0;

                return ['price' => number_format($priceEur, 2, '.', ' '), 'change' => ($change >= 0 ? '+' : '').number_format($change, 2).'%'];
            } catch (\Throwable) {
                return null;
            }
        });
        if ($spy) {
            $ticker['S&P500'] = $spy;
        }

        $xtb = Cache::remember('ticker_xtb', self::CACHE_TICKER_TTL, function () {
            try {
                $res = Http::timeout(6)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get('https://query1.finance.yahoo.com/v8/finance/chart/XTB.WA', ['interval' => '1d', 'range' => '1d']);
                $meta = $res->json()['chart']['result'][0]['meta'] ?? [];
                $price = $meta['regularMarketPrice'] ?? null;
                $prev = $meta['chartPreviousClose'] ?? null;
                if (! $price) {
                    return null;
                }
                $rate = Cache::remember('fx_eur_pln', 3600, function () {
                    try {
                        $res = Http::timeout(5)->get('https://api.frankfurter.app/latest', ['from' => 'PLN', 'to' => 'EUR']);
                        $value = $res->json()['rates']['EUR'] ?? null;

                        return is_numeric($value) ? (float) $value : null;
                    } catch (\Throwable) {
                        return null;
                    }
                });
                if ($rate === null) {
                    return null;
                } $priceEur = $price * $rate;
                $prevEur = $prev ? $prev * $rate : null;
                $change = $prevEur ? (($priceEur - $prevEur) / $prevEur) * 100 : 0;

                return ['price' => number_format($priceEur, 2, '.', ' '), 'change' => ($change >= 0 ? '+' : '').number_format($change, 2).'%'];
            } catch (\Throwable) {
                return null;
            }
        });
        if ($xtb) {
            $ticker['XTB'] = $xtb;
        }

        $crypto = Cache::remember('ticker_crypto_top', self::CACHE_TICKER_TTL, function () {
            try {
                $res = Http::timeout(8)->get('https://api.coingecko.com/api/v3/simple/price', ['ids' => 'bitcoin,ethereum,solana', 'vs_currencies' => 'eur', 'include_24hr_change' => 'true']);
                $data = $res->json();

                return ['BTC' => ['price' => number_format($data['bitcoin']['eur'] ?? 0, 0, '.', ' '), 'change' => (($data['bitcoin']['eur_24h_change'] ?? 0) >= 0 ? '+' : '').number_format($data['bitcoin']['eur_24h_change'] ?? 0, 2).'%'], 'ETH' => ['price' => number_format($data['ethereum']['eur'] ?? 0, 2, '.', ' '), 'change' => (($data['ethereum']['eur_24h_change'] ?? 0) >= 0 ? '+' : '').number_format($data['ethereum']['eur_24h_change'] ?? 0, 2).'%'], 'SOL' => ['price' => number_format($data['solana']['eur'] ?? 0, 2, '.', ' '), 'change' => (($data['solana']['eur_24h_change'] ?? 0) >= 0 ? '+' : '').number_format($data['solana']['eur_24h_change'] ?? 0, 2).'%']];
            } catch (\Throwable) {
                return [];
            }
        });

        return array_merge($ticker, $crypto);
    }
}
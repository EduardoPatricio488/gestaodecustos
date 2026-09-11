<?php

namespace App\Livewire;

use App\Models\Investment;
use App\Models\InvestmentIncome;
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

    public string $aiProvider = 'openrouter';

    private const ALPHA_VANTAGE_KEY = null;

    private const CACHE_PRICES_TTL = 900;

    private const CACHE_TICKER_TTL = 300;

    public function mount(): void
    {
        $this->lastUpdated = now()->format('H:i');
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
        ])->timeout(60)->post('https://openrouter.ai/api/v1/chat/completions', [
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

    public function switchTab($tab): void
    {
        $this->tab = $tab;
    }

    public function setType(string $newType): void
    {
        $this->type = $newType;
        $this->reset(['exchange', 'network', 'provider', 'interest_rate', 'loyalty_bonus', 'capitalization_date', 'issuer', 'series', 'product_type']);
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

    public function refreshPrices(): void
    {
        $this->isRefreshing = true;
        try {
            $assets = Investment::where('workspace_id', Auth::user()->current_workspace_id)->get();
            $debtAssets = $assets->where('type', 'Divida');
            $updated = 0;
            foreach ($debtAssets as $debtAsset) {
                if (! $debtAsset->operation_date || ! $debtAsset->interest_rate) {
                    continue;
                }
                $debtAsset->update(['current_price' => DebtInstrumentCalculator::process($debtAsset)]);
                $updated++;
            }
            $cryptoSymbols = $assets->where('type', 'Cripto')->pluck('symbol')->unique()->values()->toArray();
            $cryptoPrices = $this->fetchCryptoPrices($cryptoSymbols);
            $stockAssets = $assets->whereIn('type', ['Acao', 'ETF', 'Fundo']);
            $stockPrices = [];
            foreach ($stockAssets->pluck('symbol')->unique() as $sym) {
                Cache::forget('stock_price_'.strtoupper($sym));
                $price = $this->fetchStockPrice($sym);
                if ($price !== null) {
                    $stockPrices[strtoupper($sym)] = $price;
                }
            }
            $ids = collect($cryptoSymbols)->map(fn ($s) => $this->coinGeckoId($s))->implode(',');
            if ($ids) {
                Cache::forget('coingecko_prices_'.md5($ids));
            }
            foreach ($assets->whereIn('type', ['Acao', 'ETF', 'Fundo', 'Cripto']) as $asset) {
                $ticker = strtoupper($asset->symbol);
                if (isset($cryptoPrices[$ticker]) && $cryptoPrices[$ticker] > 0) {
                    $asset->update(['current_price' => $cryptoPrices[$ticker]]);
                    $updated++;
                } elseif (isset($stockPrices[$ticker]) && $stockPrices[$ticker] > 0) {
                    $asset->update(['current_price' => $stockPrices[$ticker]]);
                    $updated++;
                }
            }
            $this->lastUpdated = now()->format('H:i');
            $this->dispatch('toast', text: "{$updated} ativos sincronizados com o mercado.");
        } catch (\Throwable $e) {
            $this->dispatch('toast', text: 'Erro ao sincronizar preços: '.$e->getMessage(), variant: 'error');
        }
        $this->isRefreshing = false;
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
        $this->reset(['symbol', 'name', 'isin', 'quantity', 'average_price', 'fees', 'total_amount', 'exchange', 'network', 'provider', 'broker', 'operation_date', 'editingId', 'interest_rate', 'loyalty_bonus', 'capitalization_date', 'issuer', 'series', 'product_type']);
        $this->type = 'Acao';
        $this->fees = 0;
        $this->operation_date = now()->toDateString();
        $this->dispatch('modal-show-add-investment');
    }

    public function editAsset(int $id): void
    {
        $this->editingId = $id;
        $asset = Investment::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);
        foreach (['symbol', 'name', 'isin', 'type', 'exchange', 'network', 'provider', 'broker', 'quantity', 'average_price', 'fees', 'interest_rate', 'loyalty_bonus', 'issuer', 'series'] as $field) {
            $this->{$field} = $asset->{$field};
        }
        $this->operation_date = $asset->operation_date?->toDateString();
        $this->total_amount = round((float) $asset->quantity * (float) $asset->average_price + (float) ($asset->fees ?? 0), 2);
        $this->capitalization_date = $asset->capitalization_date?->toDateString();
        $this->product_type = $asset->product_type ?? 'CA';
        $this->dispatch('modal-show-add-investment');
    }

    public function save(): void
    {
        $this->validate(['symbol' => 'required|string|max:20', 'isin' => 'nullable|string|max:12', 'broker' => 'nullable|string|max:100', 'operation_date' => 'nullable|date', 'quantity' => 'required|numeric|gt:0', 'product_type' => 'nullable|in:CA,CT', 'average_price' => 'required|numeric|gt:0', 'fees' => 'nullable|numeric|min:0', 'interest_rate' => 'nullable|numeric|min:0', 'capitalization_date' => 'nullable|date']);
        $data = ['user_id' => Auth::id(), 'workspace_id' => Auth::user()->current_workspace_id, 'symbol' => strtoupper(trim($this->symbol)), 'name' => $this->name ?: strtoupper(trim($this->symbol)), 'isin' => $this->isin ? strtoupper(trim($this->isin)) : null, 'type' => $this->type, 'exchange' => $this->exchange, 'network' => $this->network, 'provider' => $this->provider, 'broker' => $this->broker, 'operation_date' => $this->operation_date ?: null, 'quantity' => (float) $this->quantity, 'average_price' => (float) $this->average_price, 'fees' => (float) ($this->fees ?? 0), 'interest_rate' => $this->type === 'Divida' ? (float) ($this->interest_rate ?? 0) : null, 'loyalty_bonus' => $this->type === 'Divida' ? (float) ($this->loyalty_bonus ?? 0) : null, 'capitalization_date' => $this->type === 'Divida' ? ($this->capitalization_date ?: null) : null, 'issuer' => $this->issuer, 'series' => $this->series, 'product_type' => $this->type === 'Divida' ? $this->product_type : null];
        if ($this->editingId) {
            Investment::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($this->editingId)->update($data);
            $msg = 'Ativo atualizado no cofre!';
        } else {
            $data['current_price'] = (float) $this->average_price;
            Investment::create($data);
            $user = Auth::user();
            $user->awardXp(25, 'investimento registado');
            $msg = 'Novo capital registado com sucesso! '.$user->xpToastText(25, 'investimento registado');
        }
        $this->reset(['symbol', 'name', 'isin', 'quantity', 'average_price', 'fees', 'total_amount', 'exchange', 'network', 'provider', 'broker', 'operation_date', 'editingId', 'interest_rate', 'loyalty_bonus', 'capitalization_date', 'issuer', 'series', 'product_type']);
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
        $query = Investment::where('workspace_id', Auth::user()->current_workspace_id);
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

        return view('livewire.investments-hub', ['myAssets' => $myAssets, 'totalInvested' => $totalInvested, 'currentValue' => $displayValue, 'totalProfit' => $displayProfit, 'totalPnlPct' => $totalPnlPct, 'composition' => $composition, 'bestPerformer' => $myAssets->sortByDesc('pnl_percent')->first(), 'worstPerformer' => $myAssets->sortBy('pnl_percent')->first(), 'highestExposure' => $myAssets->sortByDesc('current_value')->first(), 'marketData' => $this->buildMarketTicker(), 'estimatedTax' => $totalEstimatedTax, 'recentIncomes' => InvestmentIncome::where('workspace_id', Auth::user()->current_workspace_id)->with('investment')->latest('reference_date')->take(10)->get(), 'totalIncomeNet' => InvestmentIncome::where('workspace_id', Auth::user()->current_workspace_id)->sum('net_amount'), 'companyAnalysis' => $this->companyAnalysis, 'tab' => $this->tab]);
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

<?php

namespace App\Livewire;

use App\Models\Investment;
use Livewire\Component;
use App\Models\InvestmentIncome;
use App\Services\DebtInstrumentCalculator;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

#[Layout('components.layouts.app')]
class InvestmentsHub extends Component
{
    use WithFileUploads;

    public $editingId = null;
    public $search = '';
    public $filterType = 'Todos';
    public bool $isRefreshing = false;
    public ?string $lastUpdated = null;

    public $symbol, $name, $isin, $type = 'Acao';
    public $exchange, $network, $provider, $broker;
    public $operation_date, $quantity, $average_price, $fees = 0, $total_amount;
    public $interest_rate;        // Taxa de juro anual bruta (%)
public $loyalty_bonus;        // Prémio de permanência (ex: +0,25%)
public $capitalization_date;  // Data de capitalização trimestral
public $issuer;               // Entidade emitente (ex: IGCP)
public $series;               // Série (ex: Série F)
public $product_type = 'CA';

    // ─── APIs ────────────────────────────────────────────────────────────────
    const ALPHA_VANTAGE_KEY = 'WPP1D4IXEJ1U7J4V';
    const CACHE_PRICES_TTL  = 900;   // 15 min para preços de ativos
    const CACHE_TICKER_TTL  = 300;   // 5 min para o ticker do topo

    public function mount(): void
    {
        $this->lastUpdated = now()->format('H:i');
    }

    // ─── FORM ────────────────────────────────────────────────────────────────

    public function setType(string $newType): void
{
     $this->type = $newType;
    $this->reset(['exchange', 'network', 'provider', 'interest_rate',
                  'loyalty_bonus', 'capitalization_date', 'issuer', 'series', 'product_type']);

    if ($newType === 'Divida') {
        $this->average_price = 1.00;
        $this->fees          = 0;
        $this->issuer        = 'IGCP / Estado Português';
        $this->broker        = 'AforroNet';
        $this->product_type  = 'CA';
    }
}


    public function setFilter(string $type): void
    {
        $this->filterType = $type;
    }

    public function updatedTotalAmount(): void
    {
        if (is_numeric($this->total_amount) && is_numeric($this->average_price) && $this->average_price > 0) {
            $fees = (float) ($this->fees ?? 0);
            $this->quantity = round((max(0, (float) $this->total_amount - $fees)) / (float) $this->average_price, 4);
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
        if ($this->total_amount > 0) $this->updatedTotalAmount();
        elseif ($this->quantity > 0)  $this->updatedQuantity();
    }

    public function updatedFees(): void
    {
        if (is_numeric($this->quantity) && is_numeric($this->average_price)) {
            $this->updatedQuantity();
        }
    }

    // ─── PRICE FETCHING ──────────────────────────────────────────────────────

    /**
     * Cripto: CoinGecko — suporta centenas de moedas, gratuito sem chave.
     * Devolve ['BTC' => 62000.5, 'ETH' => 3400.0, ...]
     */
    private function fetchCryptoPrices(array $symbols): array
    {
        if (empty($symbols)) return [];

        // Mapear símbolo → id CoinGecko
        $idMap = [
            'BTC'   => 'bitcoin',       'ETH'   => 'ethereum',
            'SOL'   => 'solana',        'ADA'   => 'cardano',
            'DOT'   => 'polkadot',      'MATIC' => 'matic-network',
            'AVAX'  => 'avalanche-2',   'LINK'  => 'chainlink',
            'UNI'   => 'uniswap',       'ATOM'  => 'cosmos',
            'XRP'   => 'ripple',        'LTC'   => 'litecoin',
            'DOGE'  => 'dogecoin',      'SHIB'  => 'shiba-inu',
            'BNB'   => 'binancecoin',   'TRX'   => 'tron',
            'TON'   => 'the-open-network', 'SUI' => 'sui',
            'APT'   => 'aptos',         'OP'    => 'optimism',
            'ARB'   => 'arbitrum',      'INJ'   => 'injective-protocol',
            'FET'   => 'fetch-ai',      'NEAR'  => 'near',
            'FTM'   => 'fantom',        'ALGO'  => 'algorand',
            'XLM'   => 'stellar',       'VET'   => 'vechain',
            'ICP'   => 'internet-computer', 'HBAR' => 'hedera-hashgraph',
        ];

        $ids = collect($symbols)
            ->map(fn($s) => $idMap[strtoupper($s)] ?? strtolower($s))
            ->unique()
            ->implode(',');

        $cacheKey = 'coingecko_prices_' . md5($ids);

        return Cache::remember($cacheKey, self::CACHE_PRICES_TTL, function () use ($ids, $symbols, $idMap) {
            try {
                $res = Http::timeout(10)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->get('https://api.coingecko.com/api/v3/simple/price', [
                        'ids'           => $ids,
                        'vs_currencies' => 'eur',
                    ]);

                if (!$res->ok()) return [];

                $prices = [];
                $reverseMap = array_flip($idMap);

                foreach ($res->json() as $id => $data) {
                    $sym = strtoupper($reverseMap[$id] ?? $id);
                    $prices[$sym] = $data['eur'] ?? null;
                }
                return $prices;
            } catch (\Exception) {
                return [];
            }
        });
    }

    /**
     * Ações/ETFs/Fundos: tenta Yahoo Finance primeiro (sem chave, ilimitado),
     * com fallback para Alpha Vantage (limitado mas oficial).
     *
     * Yahoo Finance endpoint não-oficial mas amplamente usado:
     * https://query1.finance.yahoo.com/v8/finance/chart/{symbol}
     */
    private function fetchStockPrice(string $symbol): ?float
    {
        $cacheKey = 'stock_price_' . strtoupper($symbol);

        return Cache::remember($cacheKey, self::CACHE_PRICES_TTL, function () use ($symbol) {

            // 1. Tentar Yahoo Finance (sem limite, sem chave)
            $price = $this->fetchFromYahoo($symbol);
            if ($price) return $price;

            // 2. Fallback: Alpha Vantage
            $price = $this->fetchFromAlphaVantage($symbol);
            if ($price) return $price;

            return null;
        });
    }

    private function fetchFromYahoo(string $symbol): ?float
    {
        try {
            // Yahoo usa '.' para sufixos europeus (ex: VUAA.DE, SXR8.DE)
            $res = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept'     => 'application/json',
                ])
                ->get("https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}", [
                    'interval' => '1d',
                    'range'    => '1d',
                ]);

            if (!$res->ok()) return null;

            $data = $res->json();
            $price = $data['chart']['result'][0]['meta']['regularMarketPrice'] ?? null;

            if (!$price) return null;

            // Converter para EUR se necessário
            $currency = $data['chart']['result'][0]['meta']['currency'] ?? 'EUR';
            if (strtoupper($currency) === 'USD') {
                $price = $price * $this->getEurUsdRate();
            } elseif (strtoupper($currency) === 'GBp') {
                // Pence → EUR
                $price = ($price / 100) * $this->getEurGbpRate();
            } elseif (strtoupper($currency) === 'GBP') {
                $price = $price * $this->getEurGbpRate();
            }

            return (float) $price;
        } catch (\Exception) {
            return null;
        }
    }

    private function fetchFromAlphaVantage(string $symbol): ?float
    {
        try {
            $res = Http::timeout(8)->get('https://www.alphavantage.co/query', [
                'function' => 'GLOBAL_QUOTE',
                'symbol'   => $symbol,
                'apikey'   => self::ALPHA_VANTAGE_KEY,
            ]);

            $price = $res->json()['Global Quote']['05. price'] ?? null;
            return $price ? (float) $price : null;
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Taxa EUR/USD em cache (1h) — usada para converter preços USD → EUR
     */
    private function getEurUsdRate(): float
    {
        return Cache::remember('fx_eur_usd', 3600, function () {
            try {
                $res = Http::timeout(5)->get('https://api.frankfurter.app/latest', [
                    'from' => 'USD',
                    'to'   => 'EUR',
                ]);
                return $res->json()['rates']['EUR'] ?? 0.92;
            } catch (\Exception) {
                return 0.92; // fallback razoável
            }
        });
    }

    private function getEurGbpRate(): float
    {
        return Cache::remember('fx_eur_gbp', 3600, function () {
            try {
                $res = Http::timeout(5)->get('https://api.frankfurter.app/latest', [
                    'from' => 'GBP',
                    'to'   => 'EUR',
                ]);
                return $res->json()['rates']['EUR'] ?? 1.17;
            } catch (\Exception) {
                return 1.17;
            }
        });
    }

    // ─── REFRESH PÚBLICO (botão "Atualizar Preços") ──────────────────────────

    public function refreshPrices(): void
{
    $this->isRefreshing = true;

    try {
        $assets = Investment::where('workspace_id', Auth::user()->current_workspace_id)->get();

        // 0. Dívida Pública — calcular juros e capitalizar (TEM DE SER O PRIMEIRO)
        $debtAssets = $assets->where('type', 'Divida');
        $updated = 0;
        foreach ($debtAssets as $debtAsset) {
            if (!$debtAsset->operation_date || !$debtAsset->interest_rate) continue;
            $newPrice = DebtInstrumentCalculator::process($debtAsset);
            $debtAsset->update(['current_price' => $newPrice]);
            $updated++;
        }

        // 1. Cripto em batch
        $cryptoSymbols = $assets->where('type', 'Cripto')->pluck('symbol')->unique()->values()->toArray();
        $cryptoPrices  = $this->fetchCryptoPrices($cryptoSymbols);

        // 2. Ações/ETFs/Fundos
        $stockAssets = $assets->whereIn('type', ['Acao', 'ETF', 'Fundo']);
        $stockPrices = [];
        foreach ($stockAssets->pluck('symbol')->unique() as $sym) {
            Cache::forget('stock_price_' . strtoupper($sym));
            $price = $this->fetchStockPrice($sym);
            if ($price) $stockPrices[strtoupper($sym)] = $price;
        }

        $ids = collect($cryptoSymbols)->map(fn($s) => $this->coinGeckoId($s))->implode(',');
        if ($ids) Cache::forget('coingecko_prices_' . md5($ids));

        $allPrices = array_merge($cryptoPrices, $stockPrices);

        foreach ($assets->whereIn('type', ['Acao', 'ETF', 'Fundo', 'Cripto']) as $asset) {
            $ticker = strtoupper($asset->symbol);
            if (isset($allPrices[$ticker]) && $allPrices[$ticker] > 0) {
                $asset->update(['current_price' => $allPrices[$ticker]]);
                $updated++;
            }
        }

        $this->lastUpdated = now()->format('H:i');
        $this->dispatch('toast', text: "{$updated} ativos sincronizados com o mercado.");

    } catch (\Exception $e) {
        $this->dispatch('toast', text: 'Erro ao sincronizar preços: ' . $e->getMessage(), variant: 'error');
    }

    $this->isRefreshing = false;
}

    // helpers legados mantidos por compatibilidade
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

    // ─── CRUD ────────────────────────────────────────────────────────────────

    public function createAsset(): void
    {
     $this->reset([
    'symbol', 'name', 'isin', 'quantity', 'average_price', 'fees',
    'total_amount', 'exchange', 'network', 'provider', 'broker',
    'operation_date', 'editingId', 'interest_rate', 'loyalty_bonus',
    'capitalization_date', 'issuer', 'series', 'product_type',
]);
        $this->type = 'Acao';
        $this->fees = 0;
        $this->operation_date = now()->toDateString();
        $this->dispatch('modal-show-add-investment');
    }

    public function editAsset(int $id): void
    {
        $this->editingId = $id;
        $asset = Investment::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);

        $this->symbol         = $asset->symbol;
        $this->name           = $asset->name;
        $this->isin           = $asset->isin;
        $this->type           = $asset->type;
        $this->exchange       = $asset->exchange;
        $this->network        = $asset->network;
        $this->provider       = $asset->provider;
        $this->broker         = $asset->broker;
        $this->operation_date = $asset->operation_date?->toDateString();
        $this->quantity       = $asset->quantity;
        $this->average_price  = $asset->average_price;
        $this->fees           = $asset->fees ?? 0;
        $this->total_amount   = round((float) $asset->quantity * (float) $asset->average_price + (float) ($asset->fees ?? 0), 2);
        $this->interest_rate       = $asset->interest_rate;
$this->loyalty_bonus       = $asset->loyalty_bonus;
$this->capitalization_date = $asset->capitalization_date?->toDateString();
$this->issuer              = $asset->issuer;
$this->series              = $asset->series;
$this->product_type = $asset->product_type ?? 'CA';

        $this->dispatch('modal-show-add-investment');
    }

    public function save(): void
    {
        $this->validate([
            'symbol'         => 'required|string|max:20',
            'isin'           => 'nullable|string|max:12',
            'broker'         => 'nullable|string|max:100',
            'operation_date' => 'nullable|date',
            'quantity'       => 'required|numeric|gt:0',
            'product_type' => 'nullable|in:CA,CT',
            'average_price'  => 'required|numeric|gt:0',
            'fees'           => 'nullable|numeric|min:0',
            'interest_rate'       => 'nullable|numeric|min:0',
'capitalization_date' => 'nullable|date',
        ]);

        $data = [
            'user_id'        => Auth::id(),
            'workspace_id'   => Auth::user()->current_workspace_id,
            'symbol'         => strtoupper(trim($this->symbol)),
            'name'           => $this->name ?: strtoupper(trim($this->symbol)),
            'isin'           => $this->isin ? strtoupper(trim($this->isin)) : null,
            'type'           => $this->type,
            'exchange'       => $this->exchange,
            'network'        => $this->network,
            'provider'       => $this->provider,
            'broker'         => $this->broker,
            'operation_date' => $this->operation_date ?: null,
            'quantity'       => (float) $this->quantity,
            'average_price'  => (float) $this->average_price,
            'fees'           => (float) ($this->fees ?? 0),
            'interest_rate'       => $this->type === 'Divida' ? (float)($this->interest_rate ?? 0) : null,
'loyalty_bonus'       => $this->type === 'Divida' ? (float)($this->loyalty_bonus ?? 0) : null,
'capitalization_date' => $this->type === 'Divida' ? ($this->capitalization_date ?: null) : null,
'issuer'              => $this->issuer,
'series'              => $this->series,
'product_type' => $this->type === 'Divida' ? $this->product_type : null,
        ];

        if ($this->editingId) {
            Investment::find($this->editingId)->update($data);
            $msg = 'Ativo atualizado no cofre!';
        } else {
            $data['current_price'] = (float) $this->average_price;
            Investment::create($data);
            $msg = 'Novo capital registado com sucesso!';
        }

        $this->reset([
    'symbol', 'name', 'isin', 'quantity', 'average_price', 'fees',
    'total_amount', 'exchange', 'network', 'provider', 'broker',
    'operation_date', 'editingId', 'interest_rate', 'loyalty_bonus',
    'capitalization_date', 'issuer', 'series', 'product_type',
]);
        $this->dispatch('modal-close-add-investment');
        $this->dispatch('toast', text: $msg);
    }

    public function deleteAsset(int $id): void
    {
        Investment::where('user_id', Auth::id())
            ->where('workspace_id', Auth::user()->current_workspace_id)
            ->findOrFail($id)
            ->delete();

        $this->dispatch('toast', text: 'Ativo removido do portefólio.');
    }

    // ─── RENDER ──────────────────────────────────────────────────────────────

    public function render()
    {
        $query = Investment::where('workspace_id', Auth::user()->current_workspace_id);

        if ($this->search) {
            $term = '%' . trim($this->search) . '%';
            $query->where(fn($q) => $q->where('symbol', 'like', $term)->orWhere('name', 'like', $term));
        }

        if ($this->filterType !== 'Todos') {
            $query->where('type', $this->filterType);
        }

        $myAssets = $query->latest()->get()->map(function ($asset) {
            $cost         = (float) $asset->quantity * (float) $asset->average_price + (float) ($asset->fees ?? 0);
            $currentValue = (float) $asset->quantity * ($asset->current_price ?: (float) $asset->average_price);

            $asset->cost          = $cost;
            $asset->current_value = $currentValue;
            $asset->pnl           = $currentValue - $cost;
            $asset->pnl_percent   = $cost > 0 ? ($asset->pnl / $cost) * 100 : 0;

            return $asset;
        });

        $totalInvested         = $myAssets->sum('cost');
        $currentPortfolioValue = $myAssets->sum('current_value');
        $totalProfit           = $currentPortfolioValue - $totalInvested;
        $totalPnlPct           = $totalInvested > 0 ? ($totalProfit / $totalInvested) * 100 : 0;

        $composition = $myAssets->groupBy('type')->map(fn($group) => [
            'total'   => $group->sum('current_value'),
            'percent' => $currentPortfolioValue > 0
                ? round(($group->sum('current_value') / $currentPortfolioValue) * 100, 1)
                : 0,
        ]);

        $bestPerformer   = $myAssets->sortByDesc('pnl_percent')->first();
        $worstPerformer  = $myAssets->sortBy('pnl_percent')->first();
        $highestExposure = $myAssets->sortByDesc('current_value')->first();

        // ── TICKER: S&P500 real + XTB real + outros simulados ──
        $marketData = $this->buildMarketTicker();
$recentIncomes = InvestmentIncome::where('workspace_id', Auth::user()->current_workspace_id)
    ->with('investment:id,name,symbol,product_type')
    ->orderByDesc('reference_date')
    ->take(10)
    ->get();

$totalIncomeNet = InvestmentIncome::where('workspace_id', Auth::user()->current_workspace_id)
    ->sum('net_amount');



        return view('livewire.investments-hub', [
            'myAssets'        => $myAssets,
            'totalInvested'   => $totalInvested,
            'currentValue'    => $currentPortfolioValue,
            'totalProfit'     => $totalProfit,
            'totalPnlPct'     => $totalPnlPct,
            'composition'     => $composition,
            'bestPerformer'   => $bestPerformer,
            'worstPerformer'  => $worstPerformer,
            'highestExposure' => $highestExposure,
            'marketData'      => $marketData,
            'recentIncomes'  => $recentIncomes,
'totalIncomeNet' => $totalIncomeNet,
        ]);
    }

    private function buildMarketTicker(): array
    {
        $ticker = [];

        // S&P 500 via Yahoo Finance (SPY como proxy)
        $spy = Cache::remember('ticker_spy', self::CACHE_TICKER_TTL, function () {
            try {
                $res = Http::timeout(6)->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://query1.finance.yahoo.com/v8/finance/chart/SPY', ['interval' => '1d', 'range' => '1d']);

                $meta   = $res->json()['chart']['result'][0]['meta'] ?? [];
                $price  = $meta['regularMarketPrice'] ?? null;
                $prev   = $meta['chartPreviousClose']  ?? null;

                if (!$price) return null;

                $eurRate = $this->getEurUsdRate();
                $priceEur = $price * $eurRate;
                $prevEur  = $prev  ? $prev * $eurRate : null;
                $change   = $prevEur ? (($priceEur - $prevEur) / $prevEur) * 100 : 0;

                return [
                    'price'  => number_format($priceEur, 2, '.', ' '),
                    'change' => ($change >= 0 ? '+' : '') . number_format($change, 2) . '%',
                ];
            } catch (\Exception) {
                return null;
            }
        });
        if ($spy) $ticker['S&P500'] = $spy;

        // XTB via Yahoo Finance (XTB.WA — Bolsa de Varsóvia)
        $xtb = Cache::remember('ticker_xtb', self::CACHE_TICKER_TTL, function () {
            try {
                $res = Http::timeout(6)->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://query1.finance.yahoo.com/v8/finance/chart/XTB.WA', ['interval' => '1d', 'range' => '1d']);

                $meta  = $res->json()['chart']['result'][0]['meta'] ?? [];
                $price = $meta['regularMarketPrice'] ?? null;
                $prev  = $meta['chartPreviousClose']  ?? null;

                if (!$price) return null;

                // XTB.WA cotada em PLN — converter para EUR
                $plnEurRate = Cache::remember('fx_eur_pln', 3600, function () {
                    try {
                        $res = Http::timeout(5)->get('https://api.frankfurter.app/latest', ['from' => 'PLN', 'to' => 'EUR']);
                        return $res->json()['rates']['EUR'] ?? 0.23;
                    } catch (\Exception) { return 0.23; }
                });

                $priceEur = $price * $plnEurRate;
                $prevEur  = $prev ? $prev * $plnEurRate : null;
                $change   = $prevEur ? (($priceEur - $prevEur) / $prevEur) * 100 : 0;

                return [
                    'price'  => number_format($priceEur, 2, '.', ' '),
                    'change' => ($change >= 0 ? '+' : '') . number_format($change, 2) . '%',
                ];
            } catch (\Exception) {
                return null;
            }
        });

        // Fallback para o valor que mencionaste caso a API falhe
        $ticker['XTB'] = $xtb ?? ['price' => '125,77', 'change' => '+0.00%'];

        // BTC e ETH em tempo real via CoinGecko
        $crypto = Cache::remember('ticker_crypto_top', self::CACHE_TICKER_TTL, function () {
            try {
                $res = Http::timeout(8)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids'                    => 'bitcoin,ethereum,solana',
                    'vs_currencies'          => 'eur',
                    'include_24hr_change'    => 'true',
                ]);
                $data = $res->json();
                return [
                    'BTC' => [
                        'price'  => number_format($data['bitcoin']['eur'] ?? 0, 0, '.', ' '),
                        'change' => (($data['bitcoin']['eur_24h_change'] ?? 0) >= 0 ? '+' : '') . number_format($data['bitcoin']['eur_24h_change'] ?? 0, 2) . '%',
                    ],
                    'ETH' => [
                        'price'  => number_format($data['ethereum']['eur'] ?? 0, 2, '.', ' '),
                        'change' => (($data['ethereum']['eur_24h_change'] ?? 0) >= 0 ? '+' : '') . number_format($data['ethereum']['eur_24h_change'] ?? 0, 2) . '%',
                    ],
                    'SOL' => [
                        'price'  => number_format($data['solana']['eur'] ?? 0, 2, '.', ' '),
                        'change' => (($data['solana']['eur_24h_change'] ?? 0) >= 0 ? '+' : '') . number_format($data['solana']['eur_24h_change'] ?? 0, 2) . '%',
                    ],
                ];
            } catch (\Exception) {
                return [];
            }
        });

        return array_merge($ticker, $crypto);
    }
}

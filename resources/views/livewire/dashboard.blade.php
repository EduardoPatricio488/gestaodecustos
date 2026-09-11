<div class="dashboard-page space-y-8 pb-10">
    {{-- CSS dentro da root div para evitar o erro de Multiple Root Elements --}}
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Garante que os modais (não-flyout) desta página ficam sempre centrados no ecrã */
        .dashboard-page [data-flux-modal] > dialog:not([data-flux-flyout]) {
            position: fixed !important;
            inset: 0 !important;
            margin: auto !important;
        }
    </style>

    @php
        $firstName = explode(' ', auth()->user()->name)[0] ?? auth()->user()->name;
        $currentWs = auth()->user()->currentWorkspace;
        $others = $currentWs
            ? $currentWs->users->where('id', '!=', auth()->id())->pluck('name')->map(fn($name) => explode(' ', $name)[0])
            : collect();
        $sharedText = $others->count() > 0 ? "Partilhada com " . $others->implode(', ') : "Conta Individual";

        $tickers = [
            // Os mais conhecidos primeiro (visíveis sem scroll)
            'BTC'   => ['price' => 104850, 'change' => 1.2],
            'ETH'   => ['price' => 3920,   'change' => 2.4],
            'NVDA'  => ['price' => 1280,   'change' => 4.2],
            'AAPL'  => ['price' => 224,    'change' => 0.3],
            'SPY'   => ['price' => 548,    'change' => 0.5],
            'GOLD'  => ['price' => 3320,   'change' => 0.3],
            // Resto das cryptos
            'SOL'   => ['price' => 178,    'change' => 3.8],
            'BNB'   => ['price' => 712,    'change' => 0.9],
            'XRP'   => ['price' => 2.45,   'change' => -1.1],
            'ADA'   => ['price' => 0.62,   'change' => 1.5],
            'AVAX'  => ['price' => 38,     'change' => 2.7],
            'DOT'   => ['price' => 6.80,   'change' => -0.5],
            'LINK'  => ['price' => 18.20,  'change' => 1.8],
            'DOGE'  => ['price' => 0.19,   'change' => -2.3],
            'MATIC' => ['price' => 0.52,   'change' => 1.1],
            'UNI'   => ['price' => 8.90,   'change' => 0.6],
            // Resto das ações
            'MSFT'  => ['price' => 475,    'change' => 0.7],
            'AMZN'  => ['price' => 210,    'change' => 1.6],
            'GOOGL' => ['price' => 185,    'change' => 1.1],
            'META'  => ['price' => 620,    'change' => 2.0],
            'TSLA'  => ['price' => 248,    'change' => -1.4],
            'NFLX'  => ['price' => 920,    'change' => 0.8],
            'AMD'   => ['price' => 162,    'change' => 3.5],
            'TSM'   => ['price' => 195,    'change' => 1.2],
            // ETFs
            'QQQ'   => ['price' => 478,    'change' => 0.9],
            'VTI'   => ['price' => 265,    'change' => 0.4],
            'VOO'   => ['price' => 502,    'change' => 0.5],
            'IUSA'  => ['price' => 48,     'change' => 0.6],
            'CSPX'  => ['price' => 545,    'change' => 0.5],
            'VWCE'  => ['price' => 128,    'change' => 0.4],
            // Commodities
            'OIL'   => ['price' => 78,     'change' => -0.7],
        ];

        // Substituir pelos preços reais da API (quando disponíveis)
        foreach ($marketPrices as $symbol => $data) {

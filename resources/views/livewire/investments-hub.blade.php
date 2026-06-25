<div class="space-y-8 pb-20 p-4 lg:p-10">

    {{-- 2. HEADER SaaS PREMIUM --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 px-2">
        <div class="flex items-center gap-4 sm:gap-6">
            <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-xl shadow-indigo-500/10">
                <flux:icon name="chart-bar-square" class="w-8 h-8 sm:w-10 sm:h-10 text-indigo-600" />
            </div>
            <div class="min-w-0">
                <h1 class="text-3xl sm:text-4xl font-black uppercase italic tracking-tighter leading-none text-zinc-900 dark:text-white">
                    Investimentos
                </h1>
                @if($lastUpdated)
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1 italic">
                        Update: {{ $lastUpdated }}
                    </p>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 w-full lg:w-auto justify-end">
            <flux:button
                wire:click="refreshPrices"
                variant="ghost"
                class="w-full sm:w-auto rounded-xl border border-zinc-200 dark:border-zinc-800 h-11 sm:h-12 text-[10px] font-black uppercase tracking-widest">
                <flux:icon name="arrow-path" class="size-4 mr-2 {{ $isRefreshing ? 'animate-spin' : '' }}" />
                Atualizar Preços
            </flux:button>

            <flux:button
                @click="open = true; $wire.createAsset()"
                variant="primary"
                icon="plus"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 rounded-2xl px-6 sm:px-8 font-black uppercase shadow-lg shadow-indigo-500/20 h-11 sm:h-12 transition-all hover:scale-[1.02]">
                Novo Ativo
            </flux:button>

            <button
                wire:click="toggleNetValues"
                @class([
                    'w-full sm:w-auto flex items-center justify-center sm:justify-start gap-2 px-4 h-11 sm:h-12 rounded-2xl border transition-all font-black uppercase text-[9px] tracking-widest',
                    'bg-emerald-500/10 border-emerald-500 text-emerald-600 shadow-lg shadow-emerald-500/10' => $showNetValues,
                    'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-400' => !$showNetValues
                ])>
                <flux:icon name="{{ $showNetValues ? 'eye' : 'eye-slash' }}" class="size-4" />
                {{ $showNetValues ? 'Ver Valor Líquido (Pós-IRS)' : 'Simular Saída (IRS 28%)' }}
            </button>
        </div>
    </div>

    {{-- 1. MARKET TICKER --}}
    <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 py-3">

        {{-- Fade nas extremidades --}}
        <div class="pointer-events-none absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white dark:from-zinc-900 to-transparent z-10"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white dark:from-zinc-900 to-transparent z-10"></div>

        {{-- Indicador LIVE --}}
        <div class="absolute top-1/2 -translate-y-1/2 left-3 z-20 flex items-center gap-1.5 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm pr-2 rounded-full pl-2 py-1">
            <span class="relative flex size-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
            </span>
            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Live</span>
        </div>

        {{-- Faixa animada (duplicada para loop contínuo) --}}
        <div class="flex w-max animate-[ticker_40s_linear_infinite] gap-4 pl-24 hover:[animation-play-state:paused]">
            @for ($i = 0; $i < 2; $i++)
                @foreach($marketData as $ticker => $data)
                    @php $isUp = str_contains($data['change'], '+'); @endphp
                    <div
                        wire:key="ticker-{{ $ticker }}-{{ $i }}"
                        class="flex-shrink-0 flex items-center gap-3 px-3 sm:px-4 border-r border-zinc-100 dark:border-zinc-800 last:border-0">
                        <div class="flex items-center gap-2">
                            <flux:icon
                                name="{{ $isUp ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                                class="size-4 {{ $isUp ? 'text-emerald-500' : 'text-red-500' }}" />
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                                {{ $ticker }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-black dark:text-white italic tracking-tighter">
                            {{ $data['price'] }} <span class="text-[10px]">€</span>
                        </p>
                        <span class="text-[10px] font-black {{ $isUp ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $data['change'] }}
                        </span>
                    </div>
                @endforeach
            @endfor
        </div>
    </div>

    @once
        <style>
            @keyframes ticker {
                from { transform: translateX(0); }
                to { transform: translateX(-50%); }
            }
        </style>
    @endonce

    {{-- 3. CARDS DE PERFORMANCE (WEALTH DASHBOARD) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-2">
        {{-- Card Principal: Avaliação Total --}}
        <div class="stat-card bg-zinc-950 text-white p-6 sm:p-8 lg:p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 lg:col-span-2 group">
            <div class="absolute top-0 right-0 w-64 sm:w-80 h-64 sm:h-80 bg-indigo-500/10 blur-[100px] rounded-full -mr-16 sm:-mr-20 -mt-16 sm:-mt-20 group-hover:bg-indigo-500/20 transition-all duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-52 sm:w-64 h-52 sm:h-64 {{ $totalProfit >= 0 ? 'bg-emerald-500/5' : 'bg-red-500/5' }} blur-[100px] rounded-full -ml-16 sm:-ml-20 -mb-16 sm:-mb-20 transition-all duration-1000"></div>

            <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <h2 class="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 italic">
                            Avaliação do Portefólio
                        </h2>

                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full {{ $totalProfit >= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                            <flux:icon
                                name="{{ $totalProfit >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                                class="size-3.5" />
                            <span class="text-[10px] font-black tracking-tighter">
                                {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalPnlPct, 2) }}%
                            </span>
                        </div>
                    </div>

                    <p class="text-5xl sm:text-6xl lg:text-7xl font-black text-white tracking-tighter italic leading-none break-words">
                        {{ number_format($currentValue, 2, ',', ' ') }}
                        <span class="text-2xl sm:text-3xl text-indigo-500 font-bold uppercase not-italic">€</span>
                    </p>

                    {{-- AVISO DE IMPOSTO --}}
                    @if($showNetValues)
                        <p class="text-[9px] sm:text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] mt-4 flex flex-wrap items-center gap-2">
                            <flux:icon name="shield-check" class="size-3" />
                            Já descontado o IRS estimado de {{ number_format($estimatedTax, 2, ',', ' ') }} €
                        </p>
                    @endif
                </div>

                <div class="mt-10 sm:mt-12 flex flex-wrap gap-4 items-stretch">
                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:icon name="banknotes" class="size-3.5 text-zinc-500" />
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest italic">
                                Custo Aquisição
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-zinc-200 tracking-tighter">
                            {{ number_format($totalInvested, 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:icon
                                name="{{ $totalProfit >= 0 ? 'plus-circle' : 'minus-circle' }}"
                                class="size-3.5 {{ $totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }}" />
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest italic">
                                Lucro / Prejuízo
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black tracking-tighter {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfit, 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 rounded-2xl border backdrop-blur-md {{ $totalProfit >= 0 ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-red-500/10 border-red-500/20' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:icon
                                name="chart-bar-square"
                                class="size-3.5 {{ $totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }}" />
                            <p class="text-[9px] uppercase font-black tracking-widest italic {{ $totalProfit >= 0 ? 'text-emerald-400/80' : 'text-red-400/80' }}">
                                Rentabilidade Global
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black tracking-tighter {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalPnlPct, 2) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Secundário: Diversificação --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-6 sm:p-8 flex flex-col justify-center text-center shadow-sm group">
            <div class="relative inline-block mx-auto mb-6">
                <div class="absolute inset-0 bg-indigo-500/20 blur-xl rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative p-5 sm:p-6 bg-indigo-50 dark:bg-indigo-950/40 rounded-[2rem] text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 shadow-inner">
                    <flux:icon name="shield-check" variant="outline" class="size-9 sm:size-10" />
                </div>
            </div>
            <h3 class="font-black text-[9px] sm:text-[10px] uppercase tracking-[0.2em] text-zinc-400 italic">
                Posições Ativas
            </h3>
            <p class="text-4xl sm:text-5xl font-black dark:text-white uppercase italic tracking-tighter mt-1">
                {{ $myAssets->count() }}
            </p>
            <p class="mt-4 text-[10px] text-zinc-400 font-medium italic leading-relaxed px-2 sm:px-4">
                Capital distribuído por {{ $myAssets->count() }} frentes.
            </p>
        </div>
    </div>

    {{-- 4. SMART INSIGHTS DASHBOARD --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-2 mt-8">
        @if($bestPerformer)
            <button
                type="button"
                wire:click="editAsset({{ $bestPerformer->id }})"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-emerald-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <flux:icon name="arrow-trending-up" class="size-5 sm:size-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Melhor Performance
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            {{ $bestPerformer->type }}
                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        {{ $bestPerformer->symbol }}
                        <span class="text-emerald-500 ml-1">
                            +{{ number_format($bestPerformer->pnl_percent, 1) }}%
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        {{ $bestPerformer->name }}
                    </p>
                </div>
            </button>
        @endif

        @if($worstPerformer)
            <button
                type="button"
                wire:click="editAsset({{ $worstPerformer->id }})"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-red-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <flux:icon name="arrow-trending-down" class="size-5 sm:size-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Pior Performance
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            {{ $worstPerformer->type }}
                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        {{ $worstPerformer->symbol }}
                        <span class="text-red-500 ml-1">
                            {{ number_format($worstPerformer->pnl_percent, 1) }}%
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        {{ $worstPerformer->name }}
                    </p>
                </div>
            </button>
        @endif

        @if($highestExposure)
            <button
                type="button"
                wire:click="editAsset({{ $highestExposure->id }})"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-indigo-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <flux:icon name="chart-pie" class="size-5 sm:size-6" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Maior Exposição
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            {{ $highestExposure->type }}
                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        {{ $highestExposure->symbol }}
                        <span class="text-zinc-500 ml-1">
                            {{ number_format($highestExposure->current_value, 0, ',', ' ') }}€
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        {{ $highestExposure->name }}
                    </p>
                </div>
            </button>
        @endif
    </div>

    {{-- 5. DISTRIBUIÇÃO POR CLASSE --}}
    <div class="px-2 mt-8">
        <div class="p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">

            {{-- Cabeçalho --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <div class="size-9 sm:size-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner">
                        <flux:icon name="chart-pie" class="size-4 sm:size-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase italic tracking-tighter dark:text-white">
                            Distribuição por Classe
                        </h3>
                        <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                            Composição do Portefólio
                        </p>
                    </div>
                </div>
                <p class="text-lg sm:text-xl font-black dark:text-white italic tracking-tighter">
                    {{ number_format($currentValue, 2, ',', ' ') }} €
                </p>
            </div>

            {{-- Barra Combinada --}}
            <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner flex mb-8 sm:mb-10">
                @foreach(['Acao', 'Cripto', 'ETF', 'Fundo', 'Divida'] as $key)
                    @php
                        $pct = $composition->get($key)['percent'] ?? 0;
                        $barClass = match($key) {
                            'Acao'   => 'bg-emerald-500',
                            'Cripto' => 'bg-indigo-500',
                            'ETF'    => 'bg-amber-500',
                            'Fundo'  => 'bg-pink-500',
                            'Divida' => 'bg-blue-500',
                            default  => 'bg-zinc-500',
                        };
                    @endphp
                    @if($pct > 0)
                        <div
                            class="h-full {{ $barClass }} transition-all duration-1000 first:rounded-l-full last:rounded-r-full"
                            style="width: {{ $pct }}%">
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Categorias --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 sm:gap-8">
                @foreach([
                    'Acao'   => ['Ações',  'chart-bar-square'],
                    'Cripto' => ['Cripto', 'cube'],
                    'ETF'    => ['ETF',    'squares-2x2'],
                    'Fundo'  => ['Fundo',  'briefcase'],
                    'Divida' => ['Dívida', 'building-library'],
                ] as $key => [$label, $icon])
                    @php
                        $data  = $composition->get($key) ?? ['percent' => 0, 'total' => 0];
                        $classes = match($key) {
                            'Acao'   => [
                                'hover'  => 'hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10',
                                'iconBg' => 'bg-emerald-50 dark:bg-emerald-900/30',
                                'iconFg' => 'text-emerald-600 dark:text-emerald-400',
                                'bar'    => 'bg-emerald-500',
                            ],
                            'Cripto' => [
                                'hover'  => 'hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10',
                                'iconBg' => 'bg-indigo-50 dark:bg-indigo-900/30',
                                'iconFg' => 'text-indigo-600 dark:text-indigo-400',
                                'bar'    => 'bg-indigo-500',
                            ],
                            'ETF'    => [
                                'hover'  => 'hover:bg-amber-50/50 dark:hover:bg-amber-900/10',
                                'iconBg' => 'bg-amber-50 dark:bg-amber-900/30',
                                'iconFg' => 'text-amber-600 dark:text-amber-400',
                                'bar'    => 'bg-amber-500',
                            ],
                            'Fundo'  => [
                                'hover'  => 'hover:bg-pink-50/50 dark:hover:bg-pink-900/10',
                                'iconBg' => 'bg-pink-50 dark:bg-pink-900/30',
                                'iconFg' => 'text-pink-600 dark:text-pink-400',
                                'bar'    => 'bg-pink-500',
                            ],
                            'Divida' => [
                                'hover'  => 'hover:bg-blue-50/50 dark:hover:bg-blue-900/10',
                                'iconBg' => 'bg-blue-50 dark:bg-blue-900/30',
                                'iconFg' => 'text-blue-600 dark:text-blue-400',
                                'bar'    => 'bg-blue-500',
                            ],
                            default  => [
                                'hover'  => 'hover:bg-zinc-50/50 dark:hover:bg-zinc-900/10',
                                'iconBg' => 'bg-zinc-50 dark:bg-zinc-900/30',
                                'iconFg' => 'text-zinc-600 dark:text-zinc-400',
                                'bar'    => 'bg-zinc-500',
                            ],
                        };
                    @endphp
                    <button
                        type="button"
                        wire:click="setFilter('{{ $key }}')"
                        wire:key="comp-{{ $key }}"
                        class="space-y-3 text-left p-4 -m-2 sm:-m-4 rounded-2xl transition-all {{ $classes['hover'] }} group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="size-7 sm:size-8 rounded-xl {{ $classes['iconBg'] }} {{ $classes['iconFg'] }} flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                    <flux:icon name="{{ $icon }}" class="size-4" />
                                </div>
                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-500 tracking-widest">
                                    {{ $label }}
                                </span>
                            </div>
                            <span class="text-xs sm:text-sm font-black dark:text-white italic tracking-tighter">
                                {{ $data['percent'] }}%
                            </span>
                        </div>

                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner">
                            <div
                                class="h-full {{ $classes['bar'] }} rounded-full transition-all duration-1000"
                                style="width: {{ $data['percent'] }}%">
                            </div>
                        </div>

                        <p class="text-[10px] font-bold text-zinc-400 italic">
                            {{ number_format($data['total'], 2, ',', ' ') }} €
                        </p>
                    </button>
                @endforeach
            </div>

        </div>
    </div>

    {{-- 6. PESQUISA E FILTROS --}}
    <div class="flex flex-col md:flex-row gap-4 md:gap-6 items-center justify-between px-2 mt-10 sm:mt-12">
        <div class="relative w-full md:w-96 group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-indigo-500">
                <flux:icon name="magnifying-glass" class="size-4" />
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="PESQUISAR TICKER..."
                class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl h-11 sm:h-12 pl-12 pr-4 text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" />
        </div>

        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full md:w-auto">
            @foreach(['Todos', 'Acao', 'Cripto', 'ETF', 'Fundo', 'Divida'] as $tab)
                <button
                    type="button"
                    wire:click="setFilter('{{ $tab }}')"
                    class="px-4 sm:px-5 py-2.5 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap
                        {{ $filterType === $tab
                            ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/20 ring-1 ring-indigo-500'
                            : 'bg-white dark:bg-zinc-900 text-zinc-400 border border-zinc-200 dark:border-zinc-800 hover:border-indigo-500/50' }}">
                    {{ match($tab) { 'Acao' => 'Ações', 'Divida' => 'Dívida', default => $tab } }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- 7. TABELA DE POSIÇÕES --}}
    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm mx-2 mt-6"
        x-data="{ open: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800 text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                    <tr>
                        <th class="p-4 sm:p-6">Ativo</th>
                        <th class="p-4 sm:p-6 text-center">Qtd</th>
                        <th class="p-4 sm:p-6 text-right">Preço Compra</th>
                        <th class="p-4 sm:p-6 text-right">Atual</th>
                        <th class="p-4 sm:p-6 text-right">Valor Investido</th>
                        <th class="p-4 sm:p-6 text-right sm:px-10">Lucro ou Prejuízo</th>
                        <th class="p-4 sm:p-6 text-center w-24">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($myAssets as $asset)
                        @php $isGain = $asset->pnl >= 0; @endphp

                        {{-- LINHA PRINCIPAL --}}
                        <tr
                            wire:key="asset-row-{{ $asset->id }}"
                            @click="open = open === {{ $asset->id }} ? null : {{ $asset->id }}"
                            class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all cursor-pointer select-none"
                            :class="open === {{ $asset->id }} ? 'bg-indigo-50/40 dark:bg-indigo-500/5' : ''">

                            {{-- COLUNA: ATIVO --}}
                            <td class="p-4 sm:p-6">
                                <div class="flex items-center gap-3 sm:gap-4">

                                    {{-- Seta expansível --}}
                                    <div class="size-4 sm:size-5 text-zinc-400 transition-transform duration-300 shrink-0"
                                         :class="open === {{ $asset->id }} ? 'rotate-90' : ''">
                                        <flux:icon name="chevron-right" class="size-4" />
                                    </div>

                                    {{-- Ícone / Sigla --}}
                                    <div class="size-10 sm:size-11 rounded-2xl bg-white dark:bg-zinc-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-[10px] sm:text-[11px] shadow-sm border border-zinc-100 dark:border-zinc-700 shrink-0">
                                        {{ substr($asset->symbol, 0, 3) }}
                                    </div>

                                    {{-- Nome + detalhes --}}
                                    <div class="min-w-0">
                                        <p class="text-sm sm:text-base font-black dark:text-white uppercase leading-none tracking-tight truncate">
                                            {{ $asset->name }}
                                        </p>

                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-1">
                                            <span class="text-[8px] sm:text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                                {{ $asset->symbol }}
                                            </span>

                                            {{-- Tipo --}}
                                            <span class="text-[7px] sm:text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md
                                                {{ match($asset->type) {
                                                    'ETF'    => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                                                    'Cripto' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
                                                    'Acao'   => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                                    'Fundo'  => 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400',
                                                    'Divida' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                                    default  => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500',
                                                } }}">
                                                {{ match($asset->type) {
                                                    'Acao' => 'Ação',
                                                    'Divida' => 'Dívida',
                                                    default => $asset->type,
                                                } }}
                                            </span>

                                            {{-- Corretora --}}
                                            @if($asset->broker)
                                                <span class="text-[8px] sm:text-[9px] text-zinc-400 font-medium">
                                                    · {{ $asset->broker }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- QTD --}}
                            <td class="p-4 sm:p-6 text-center font-black dark:text-zinc-200 text-xs sm:text-sm italic">
                                {{ number_format($asset->quantity, 4, ',', ' ') }}
                            </td>

                            {{-- PREÇO COMPRA --}}
                            <td class="p-4 sm:p-6 text-right font-bold text-zinc-500 text-[10px] sm:text-xs italic">
                                {{ number_format($asset->average_price, 2, ',', ' ') }} €
                            </td>

                            {{-- PREÇO ATUAL --}}
                            <td class="p-4 sm:p-6 text-right font-black dark:text-white italic tracking-tighter text-xs sm:text-sm">
                                {{ number_format($asset->current_price ?: $asset->average_price, 2, ',', ' ') }} €
                            </td>

                            {{-- VALOR INVESTIDO --}}
                            <td class="p-4 sm:p-6 text-right font-black dark:text-white text-sm sm:text-base italic tracking-tighter">
                                {{ number_format($asset->current_value, 2, ',', ' ') }} €
                            </td>

                            {{-- LUCRO / PREJUÍZO --}}
                            <td class="p-4 sm:p-6 text-right sm:px-10">
                                <div class="inline-flex flex-col items-end">
                                    <span class="text-sm font-black {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic tracking-tighter">
                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl, 2, ',', ' ') }} €
                                    </span>
                                    <span class="text-[8px] sm:text-[9px] font-black {{ $isGain ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }} px-1.5 py-0.5 rounded uppercase mt-1">
                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl_percent, 2) }}%
                                    </span>
                                </div>
                            </td>

                            {{-- AÇÕES --}}
                            <td class="p-4 sm:p-6 text-center" @click.stop>
                                <div class="flex items-center justify-center gap-2">

                                    <flux:button
                                        wire:click="editAsset({{ $asset->id }})"
                                        variant="ghost"
                                        icon="pencil-square"
                                        size="sm"
                                        class="text-zinc-400 hover:text-indigo-600 rounded-xl" />

                                    <flux:button
                                        wire:click="deleteAsset({{ $asset->id }})"
                                        wire:confirm="Remover ativo?"
                                        variant="ghost"
                                        icon="trash"
                                        size="sm"
                                        class="text-zinc-400 hover:text-red-600 rounded-xl" />

                                </div>
                            </td>
                        </tr>

                        {{-- PAINEL EXPANSÍVEL --}}
                        <tr
                            wire:key="asset-detail-{{ $asset->id }}"
                            x-show="open === {{ $asset->id }}"
                            x-collapse
                            class="bg-indigo-50/30 dark:bg-indigo-950/10">
                            <td colspan="7" class="px-6 sm:px-10 py-6">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                    {{-- BLOCO 1: IDENTIFICAÇÃO --}}
                                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 italic">
                                            Identificação
                                        </p>

                                        <div class="space-y-3">

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Ticker</span>
                                                <span class="text-xs font-black dark:text-white uppercase">{{ $asset->symbol }}</span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">ISIN</span>
                                                <span class="text-xs font-mono font-bold dark:text-white">{{ $asset->isin ?: '—' }}</span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Tipo</span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ $asset->type === 'Acao' ? 'Ação' : $asset->type }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Corretora</span>
                                                <span class="text-xs font-black dark:text-white">{{ $asset->broker ?: '—' }}</span>
                                            </div>

                                            {{-- CAMPOS ESPECIAIS PARA DÍVIDA --}}
                                            @if($asset->type === 'Divida')

                                                @if($asset->series)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Série</span>
                                                        <span class="text-xs font-black dark:text-white">{{ $asset->series }}</span>
                                                    </div>
                                                @endif

                                                @if($asset->interest_rate)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Taxa Anual Bruta</span>
                                                        <span class="text-xs font-black text-blue-600 dark:text-blue-400">
                                                            {{ number_format($asset->interest_rate, 3, ',', '') }}%
                                                        </span>
                                                    </div>
                                                @endif

                                                @if($asset->loyalty_bonus)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Prémio Permanência</span>
                                                        <span class="text-xs font-black text-blue-500">
                                                            +{{ number_format($asset->loyalty_bonus, 3, ',', '') }}%
                                                        </span>
                                                    </div>
                                                @endif

                                                @if($asset->capitalization_date)
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Próx. Capitalização</span>
                                                        <span class="text-xs font-black dark:text-white">
                                                            {{ \Carbon\Carbon::parse($asset->capitalization_date)->format('d/m/Y') }}
                                                        </span>
                                                    </div>
                                                @endif

                                            @endif

                                            {{-- Exchange / Rede / Gestora --}}
                                            @if($asset->exchange ?? $asset->network ?? $asset->provider ?? null)
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        {{ $asset->type === 'Acao' ? 'Bolsa' : ($asset->type === 'Cripto' ? 'Rede' : 'Gestora') }}
                                                    </span>
                                                    <span class="text-xs font-black dark:text-white">
                                                        {{ $asset->exchange ?? $asset->network ?? $asset->provider ?? '—' }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- DATA DE COMPRA --}}
                                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Data de Compra
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ $asset->operation_date ? \Carbon\Carbon::parse($asset->operation_date)->format('d/m/Y') : '—' }}
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                                                        {{-- BLOCO 2: TRANSAÇÃO --}}
                                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 italic">
                                            Transação
                                        </p>

                                        <div class="space-y-3">

                                            @if($asset->operation_date)
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        Data
                                                    </span>
                                                    <span class="text-xs font-black dark:text-white">
                                                        {{ \Carbon\Carbon::parse($asset->operation_date)->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Quantidade
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->quantity, 4, ',', ' ') }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Preço / Unidade
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->average_price, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Taxas
                                                </span>
                                                <span class="text-xs font-black {{ ($asset->fees ?? 0) > 0 ? 'text-amber-500' : 'text-zinc-400' }}">
                                                    {{ number_format($asset->fees ?? 0, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Custo Total
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->cost, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                    {{-- BLOCO 3: PERFORMANCE --}}
                                    <div class="p-5 rounded-2xl border space-y-4
                                        {{ $isGain
                                            ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30'
                                            : 'bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900/30' }}">

                                        <p class="text-[9px] font-black uppercase tracking-widest {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic">
                                            Performance
                                        </p>

                                        <div class="space-y-3">

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Preço Atual
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->current_price ?: $asset->average_price, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Avaliação
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    {{ number_format($asset->current_value, 2, ',', ' ') }} €
                                                </span>
                                            </div>

                                            <div class="pt-2 border-t {{ $isGain ? 'border-emerald-100 dark:border-emerald-900/30' : 'border-red-100 dark:border-red-900/30' }}">

                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        P&L €
                                                    </span>
                                                    <span class="text-sm font-black {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic tracking-tighter">
                                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl, 2, ',', ' ') }} €
                                                    </span>
                                                </div>

                                                <div class="flex justify-between items-center gap-2 mt-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        P&L %
                                                    </span>
                                                    <span class="text-sm font-black {{ $isGain ? 'text-emerald-500' : 'text-red-500' }} italic tracking-tighter">
                                                        {{ $isGain ? '+' : '' }}{{ number_format($asset->pnl_percent, 2) }}%
                                                    </span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="p-16 sm:p-20 text-center text-zinc-400 italic">
                                Sem ativos registados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>























{{-- MODAL DE REGISTO (ESTILO ASSINATURAS) --}}
<div
    x-data="{
        open: false,
        show() {
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    }"
    x-on:modal-show-add-investment.window="show()"
    x-on:modal-close-add-investment.window="close()"
    x-on:keydown.escape.window="close()">

    {{-- OVERLAY PREMIUM --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/50 backdrop-blur-sm will-change-opacity">
    </div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">

        {{-- CAIXA DO MODAL --}}
        <div
            x-show="open"
            @click.stop
            x-transition:enter="transition ease-out duration-100 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.97] translate-y-1"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.97] translate-y-1"
            class="relative w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden max-h-[90vh] flex flex-col">

            {{-- FORM --}}
            <form
                wire:submit.prevent="save"
                x-data="{ selected: @entangle('type'), mode: 'unit' }"
                class="flex flex-col max-h-[90vh] overflow-hidden"
                autocomplete="off">

                {{-- SCROLL INTERNO --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-6">

                    {{-- CABEÇALHO PREMIUM --}}
                    <div class="flex items-center gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-500/20">
                            <flux:icon name="{{ $editingId ? 'pencil-square' : 'plus' }}" class="size-5" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                                {{ $editingId ? 'Editar Ativo' : 'Novo Investimento' }}
                            </h2>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                                Terminal de Registo de Capital
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="close()"
                            class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition">
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </div>

                    {{-- BARRA DE PROGRESSO --}}
                    <div class="flex items-center gap-1.5 px-1">
                        @for ($s = 1; $s <= 4; $s++)
                            <div class="flex-1 h-0.5 rounded-full bg-indigo-600 {{ $s > 1 ? 'opacity-20' : '' }}"></div>
                        @endfor
                    </div>

                    {{-- PASSO 1: CLASSE --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                1
                            </div>
                            <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                Seleciona a Classe
                            </flux:label>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                            @foreach([
                                'Acao'   => ['chart-bar-square', 'Ação'],
                                'Cripto' => ['cube',             'Cripto'],
                                'ETF'    => ['squares-2x2',      'ETF'],
                                'Fundo'  => ['briefcase',        'Fundo'],
                                'Divida' => ['building-library', 'Dívida'],
                            ] as $key => [$icon, $label])

                                <button type="button"
                                    @click="selected = '{{ $key }}'; $wire.setType('{{ $key }}')"
                                    :class="selected === '{{ $key }}'
                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-500/20'
                                        : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50'"
                                    class="relative p-4 border-2 rounded-[2rem] flex flex-col items-center gap-2 transition-all duration-300 group">

                                    {{-- CHECK --}}
                                    <div x-show="selected === '{{ $key }}'" x-transition
                                         class="absolute top-2 right-2 size-4 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <flux:icon name="check" class="size-2.5 text-white" />
                                    </div>

                                    {{-- ÍCONE --}}
                                    <flux:icon name="{{ $icon }}"
                                        ::class="selected === '{{ $key }}'
                                            ? 'text-indigo-600 dark:text-indigo-400'
                                            : 'text-zinc-400'"
                                        class="size-6 group-hover:scale-110 transition-transform" />

                                    {{-- LABEL --}}
                                    <span class="text-[10px] font-black uppercase tracking-tight"
                                          ::class="selected === '{{ $key }}'
                                            ? 'text-indigo-600 dark:text-indigo-400'
                                            : 'text-zinc-500'">
                                        {{ $label }}
                                    </span>
                                </button>

                            @endforeach
                        </div>
                    </div>
                    {{-- PASSO 2: IDENTIFICAÇÃO --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                2
                            </div>
                            <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                Identificação do Ativo
                            </flux:label>
                        </div>

                        {{-- DÍVIDA --}}
                        @if($type === 'Divida')

                            {{-- TOGGLE CA / CT --}}
                            <div class="flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                                <button type="button" wire:click="$set('product_type', 'CA')"
                                    class="flex-1 flex items-center justify-center gap-2 h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                        {{ $product_type === 'CA' ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-600' }}">
                                    <flux:icon name="building-library" class="size-3.5" />
                                    Certificados de Aforro
                                </button>

                                <button type="button" wire:click="$set('product_type', 'CT')"
                                    class="flex-1 flex items-center justify-center gap-2 h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                        {{ $product_type === 'CT' ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-600' }}">
                                    <flux:icon name="document-text" class="size-3.5" />
                                    Certificados do Tesouro
                                </button>
                            </div>

                            {{-- CAMPOS CA / CT --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:input wire:model="name" label="Nome / Descrição"
                                    placeholder="{{ $product_type === 'CA' ? 'Ex: Certificados de Aforro Série F' : 'Ex: Certificados do Tesouro Poupança Crescimento' }}"
                                    class="font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />

                                <flux:input wire:model="series" label="Série"
                                    placeholder="Ex: Série F"
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:input wire:model="issuer" label="Emitente"
                                    placeholder="IGCP / Estado Português"
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />

                                <flux:input wire:model="broker" label="Plataforma"
                                    placeholder="AforroNet, CTT..."
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>

                            {{-- TAXA + CÓDIGO --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <flux:input wire:model="interest_rate" type="number" step="0.001"
                                        label="Taxa Base Atual — Euribor 3M (%)"
                                        placeholder="Ex: 2.500"
                                        class="font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Máx. 2,5% · Mín. 0% · Prémio calculado automaticamente.
                                    </p>
                                </div>

                                <div class="space-y-1.5">
                                    <flux:input wire:model="symbol" label="Referência / Código"
                                        placeholder="Ex: CA-F ou CT-PC"
                                        class="uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Código interno da subscrição.
                                    </p>
                                </div>
                            </div>

                            {{-- BANNERS --}}
                            @if($product_type === 'CA')
                                <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-950/30 rounded-2xl border border-blue-100 dark:border-blue-900/30">
                                    <flux:icon name="information-circle" class="size-4 text-blue-500 shrink-0 mt-0.5" />
                                    <div class="text-[10px] text-blue-700 dark:text-blue-300 leading-relaxed space-y-1">
                                        <p><strong>Capitalização trimestral</strong> — juros somados ao saldo.</p>
                                        <p><strong>IRS 28%</strong> aplicado antes de capitalizar.</p>
                                        <p><strong>Prémio de permanência</strong> calculado automaticamente.</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-3 p-4 bg-indigo-50 dark:bg-indigo-950/30 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                                    <flux:icon name="information-circle" class="size-4 text-indigo-500 shrink-0 mt-0.5" />
                                    <div class="text-[10px] text-indigo-700 dark:text-indigo-300 leading-relaxed space-y-1">
                                        <p><strong>Juro anual não capitalizável</strong>.</p>
                                        <p><strong>IRS 28%</strong> sobre o juro bruto.</p>
                                        <p>Juro líquido registado em <strong>Rendimentos Recebidos</strong>.</p>
                                    </div>
                                </div>
                            @endif

                        @else
                            {{-- CAMPOS NORMAIS PARA OUTROS TIPOS --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:input wire:model="symbol" label="Ticker / Símbolo"
                                    placeholder="Ex: VUAA.DE"
                                    class="uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />

                                <flux:input wire:model="isin" label="Código ISIN"
                                    placeholder="Ex: IE00BFMXXD54"
                                    class="uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>

                            <flux:input wire:model="name" label="Nome do Ativo"
                                placeholder="Ex: Vanguard S&P 500 UCITS ETF"
                                class="font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 shadow-inner" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    @if($type === 'Acao')
                                        <flux:input wire:model="exchange" label="Bolsa (Exchange)"
                                            placeholder="NYSE, NASDAQ..."
                                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    @elseif($type === 'Cripto')
                                        <flux:input wire:model="network" label="Rede (Blockchain)"
                                            placeholder="ERC-20, Solana..."
                                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    @else
                                        <flux:input wire:model="provider" label="Gestora / Provider"
                                            placeholder="Vanguard, iShares..."
                                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                                    @endif
                                </div>

                                <flux:input wire:model="broker" label="Corretora"
                                    placeholder="Ex: XTB, DEGIRO..."
                                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14" />
                            </div>
                        @endif
                    </div>

                    {{-- PASSO 3: DADOS DA TRANSAÇÃO --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                3
                            </div>
                            <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                Dados da Transação
                            </flux:label>
                        </div>

                        <div class="p-6 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-[2rem] border border-indigo-100 dark:border-indigo-900/30 space-y-5">

                            {{-- DATA --}}
                            <flux:input wire:model="operation_date" type="date"
                                label="Data de Subscrição"
                                class="font-black !bg-white dark:!bg-zinc-950" />
                            {{-- MODO UNITÁRIO / TOTAL --}}
                            @if($type !== 'Divida')

                                <div class="flex items-center gap-1 p-1 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                                    <button type="button" @click="mode = 'unit'"
                                        :class="mode === 'unit'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'"
                                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        <flux:icon name="calculator" class="size-3.5" />
                                        Preço por unidade
                                    </button>

                                    <button type="button" @click="mode = 'total'"
                                        :class="mode === 'total'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'"
                                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        <flux:icon name="banknotes" class="size-3.5" />
                                        Valor que investi
                                    </button>
                                </div>

                                {{-- MODO UNITÁRIO --}}
                                <div x-show="mode === 'unit'" x-transition class="space-y-4">

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <flux:input wire:model.live="quantity" type="number" step="0.00001"
                                            label="Quantidade de Títulos Comprados"
                                            placeholder="Ex: 4"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Número exato de unidades adquiridas.
                                        </p>
                                    </div>

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <flux:input wire:model.live="average_price" type="number" step="0.00001"
                                            label="Preço de Compra por Unidade (€)"
                                            placeholder="Ex: 125,77"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Valor de 1 título no momento da compra.
                                        </p>
                                    </div>
                                </div>

                                {{-- MODO TOTAL --}}
                                <div x-show="mode === 'total'" x-transition class="space-y-4">

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">

                                        <flux:input
                                            wire:model.live="total_amount"
                                            type="number"
                                            step="0.01"
                                            label="Valor Total que Investiste (€)"
                                            placeholder="Ex: 503,08"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950"
                                        />

                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Montante total debitado na corretora antes de taxas.
                                        </p>
                                    </div>

                                    {{-- Preço por unidade para cálculo --}}
                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <flux:input wire:model.live="average_price" type="number" step="0.00001"
                                            label="Preço de Compra por Unidade (€)"
                                            placeholder="Ex: 125,77"
                                            class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Necessário para calcular a quantidade: Total ÷ Preço.
                                        </p>
                                    </div>

                                    {{-- Quantidade calculada --}}
                                    @if($total_amount && $average_price && (float)$average_price > 0)
                                        <div class="flex items-center justify-between px-4 py-3 bg-indigo-600/10 dark:bg-indigo-500/10 rounded-2xl border border-indigo-200 dark:border-indigo-800">
                                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                                                <flux:icon name="arrow-path" class="size-3.5" />
                                                <span class="text-[10px] font-black uppercase tracking-widest">Quantidade calculada</span>
                                            </div>
                                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400 italic tracking-tighter">
                                                ≈ {{ number_format(((float)$total_amount - (float)($fees ?? 0)) / (float)$average_price, 4, ',', ' ') }} unidades
                                            </span>
                                        </div>
                                    @endif

                                </div>

                                {{-- TAXAS --}}
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                    <flux:input wire:model.live="fees" type="number" step="0.01"
                                        label="Taxas / Comissões da Corretora (€)"
                                        placeholder="Ex: 0,00"
                                        class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Custo da execução da ordem. <span class="text-emerald-500 font-bold">XTB, Trading212 e DEGIRO Free: normalmente 0 €.</span>
                                    </p>
                                </div>

                            @else
                                {{-- DÍVIDA PÚBLICA --}}
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                    <flux:input wire:model.live="quantity" type="number" step="1"
                                        label="Capital Investido (€) = N.º de Títulos"
                                        placeholder="Ex: 5000"
                                        class="font-black !bg-zinc-50 dark:!bg-zinc-950" />
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Cada título vale exatamente <strong>1 €</strong>.
                                    </p>
                                </div>

                                {{-- Preview --}}
                                @if($quantity && (float)$quantity > 0)
                                    <div class="flex items-center justify-between px-4 py-3 bg-blue-600/10 dark:bg-blue-500/10 rounded-2xl border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                            <flux:icon name="building-library" class="size-3.5" />
                                            <span class="text-[10px] font-black uppercase tracking-widest">Capital subscrito</span>
                                        </div>
                                        <span class="text-sm font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">
                                            {{ number_format((float)$quantity, 0, ',', ' ') }} títulos · {{ number_format((float)$quantity, 2, ',', ' ') }} €
                                        </span>
                                    </div>

                                    {{-- Simulação --}}
                                    @if($interest_rate && (float)$interest_rate > 0)
                                        @php
                                            $yearsComplete  = $operation_date ? (int)\Carbon\Carbon::parse($operation_date)->diffInYears(now()) : 0;
                                            $loyaltyPreview = match(true) {
                                                $yearsComplete >= 5 => 1.25,
                                                $yearsComplete >= 4 => 1.00,
                                                $yearsComplete >= 3 => 0.75,
                                                $yearsComplete >= 2 => 0.50,
                                                $yearsComplete >= 1 => 0.25,
                                                default             => 0.00,
                                            };
                                            $totalRateGross   = (float)$interest_rate + $loyaltyPreview;
                                            $annualNetPreview = (float)$quantity * $totalRateGross / 100 * 0.72;
                                        @endphp

                                        <div class="p-4 bg-blue-50/50 dark:bg-blue-950/20 rounded-2xl border border-blue-100 dark:border-blue-900/30 space-y-3">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-blue-500 italic">Simulação Atual</p>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="text-center p-3 bg-white dark:bg-zinc-900 rounded-xl border border-blue-100 dark:border-blue-900/20">
                                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Taxa Total Bruta</p>
                                                    <p class="text-lg font-black text-blue-600 dark:text-blue-400 italic tracking-tighter mt-1">
                                                        {{ number_format($totalRateGross, 3, ',', '') }}%
                                                    </p>
                                                </div>

                                                <div class="text-center p-3 bg-white dark:bg-zinc-900 rounded-xl border border-blue-100 dark:border-blue-900/20">
                                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Juro Líquido / Ano</p>
                                                    <p class="text-lg font-black text-emerald-500 italic tracking-tighter mt-1">
                                                        +{{ number_format($annualNetPreview, 2, ',', ' ') }} €
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif

                        </div>
                    </div>
                    {{-- PASSO 4: RESUMO --}}
                    @if($quantity && $average_price && (float)$quantity > 0)
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                    4
                                </div>
                                <flux:label class="text-[11px] font-black uppercase tracking-widest text-zinc-400">
                                    Resumo
                                </flux:label>
                            </div>

                            <div class="p-6 bg-zinc-950 rounded-[2.5rem] flex items-center justify-between border border-zinc-800 shadow-2xl">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black uppercase text-zinc-500 tracking-[0.2em] italic">
                                        Investimento Total
                                    </p>

                                    <p class="text-white text-xs font-medium">
                                        <span class="text-indigo-400 font-black">
                                            {{ number_format((float)$quantity, 4, ',', ' ') }}
                                        </span> unidades

                                        <span class="text-zinc-500 mx-1">×</span>

                                        <span class="text-indigo-400 font-black">
                                            {{ number_format((float)$average_price, 2, ',', ' ') }} €
                                        </span>

                                        @if(!empty($fees) && (float)$fees > 0)
                                            <span class="text-zinc-500 mx-1">+</span>
                                            <span class="text-amber-400 font-black">
                                                {{ number_format((float)$fees, 2, ',', ' ') }} € taxas
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-2xl sm:text-3xl font-black text-white italic tracking-tighter">
                                        {{ number_format((float)$quantity * (float)$average_price + (float)($fees ?? 0), 2, ',', ' ') }} €
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- BOTÕES FINAIS (ESTILO PREMIUM) --}}
                <div class="shrink-0 p-5 sm:p-6 pt-4 flex gap-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <button
                        type="button"
                        @click="close()"
                        class="flex-1 uppercase font-black text-[10px] h-12 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="flex-[2] bg-indigo-600 h-12 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 text-white hover:bg-indigo-500 active:scale-95 transition-all disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Confirmar Posição</span>
                        <span wire:loading wire:target="save">A Processar...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
{{-- /MODAL --}}














































{{-- RENDIMENTOS RECEBIDOS --}}
@if(isset($recentIncomes) && $recentIncomes->count() > 0)
<div class="px-2 mt-10">
    <div class="p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="size-9 sm:size-10 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-inner">
                    <flux:icon name="banknotes" class="size-4 sm:size-5" />
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase italic tracking-tighter dark:text-white">
                        Rendimentos Recebidos
                    </h3>
                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                        Juros Líquidos · Dívida Pública
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest italic">
                    Total Acumulado
                </p>
                <p class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">
                    +{{ number_format($totalIncomeNet, 2, ',', ' ') }} €
                </p>
            </div>
        </div>

        <div class="overflow-y-auto max-h-[380px] custom-scrollbar pr-2">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead class="sticky top-0 bg-white dark:bg-zinc-900 z-10 text-[9px] font-black uppercase text-zinc-400 tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                    <tr>
                        <th class="pb-4">Ativo</th>
                        <th class="pb-4 text-center">Tipo</th>
                        <th class="pb-4 text-right">Data</th>
                        <th class="pb-4 text-right">Taxa Base</th>
                        <th class="pb-4 text-right">Prémio</th>
                        <th class="pb-4 text-right">Juro Bruto</th>
                        <th class="pb-4 text-right">IRS 28%</th>
                        <th class="pb-4 text-right">Juro Líquido</th>
                        <th class="pb-4 text-right">Saldo Após</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @foreach($recentIncomes as $income)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition">
                            <td class="py-4">
                                <p class="text-xs font-black dark:text-white uppercase tracking-tight">
                                    {{ $income->investment?->symbol ?? '—' }}
                                </p>
                                <p class="text-[10px] text-zinc-400 truncate max-w-[160px]">
                                    {{ $income->investment?->name ?? '—' }}
                                </p>
                            </td>

                            <td class="py-4 text-center">
                                <span class="text-[8px] font-black uppercase px-2 py-1 rounded-lg
                                    {{ $income->type === 'CA'
                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                                        : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400' }}">
                                    {{ $income->type === 'CA' ? 'Aforro' : 'Tesouro' }}
                                </span>
                            </td>

                            <td class="py-4 text-right text-xs font-bold text-zinc-500">
                                {{ $income->reference_date->format('d/m/Y') }}
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-zinc-200">
                                {{ number_format($income->base_rate, 3, ',', '') }}%
                            </td>

                            <td class="py-4 text-right text-xs font-black text-blue-500">
                                +{{ number_format($income->loyalty_bonus, 3, ',', '') }}%
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-zinc-200">
                                {{ number_format($income->gross_amount, 2, ',', ' ') }} €
                            </td>

                            <td class="py-4 text-right text-xs font-black text-red-500">
                                -{{ number_format($income->tax_amount, 2, ',', ' ') }} €
                            </td>

                            <td class="py-4 text-right">
                                <span class="text-sm font-black text-emerald-500 italic tracking-tighter">
                                    +{{ number_format($income->net_amount, 2, ',', ' ') }} €
                                </span>
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-white italic">
                                {{ number_format($income->capital_after, 2, ',', ' ') }} €
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endif

{{-- 9. RODAPÉ --}}
<footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 px-4">
    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.5em]">
        © {{ date('Y') }} Finance Pro · Terminal de Ativos Inteligente
    </p>
</footer>

</div> {{-- FIM DA DIV RAIZ --}}


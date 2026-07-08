<div
    x-data="{
        tab: @entangle('activeTab'),
        privacyMode: localStorage.getItem('privacyMode') === 'true'
    }"
    class="min-h-screen bg-zinc-50 dark:bg-zinc-950"
>

{{-- ═══════════════════════════════════════════════════════════════
     CABEÇALHO
══════════════════════════════════════════════════════════════ --}}
<div class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-30">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- TÍTULO --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="size-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20 flex-shrink-0">
                    <flux:icon name="building-library" class="size-5 text-white" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-black text-zinc-900 dark:text-white leading-none">Banco</h1>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 leading-none mt-0.5 hidden sm:block">
                        Visualize e acompanhe toda a sua situação financeira em tempo real.
                    </p>
                </div>
            </div>

            {{-- ALERTAS --}}
            @if(count($alerts) > 0)
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-full">
                    <flux:icon name="bell-alert" class="size-3.5 text-amber-600 dark:text-amber-400" />
                    <span class="text-[10px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider">{{ count($alerts) }} alertas</span>
                </div>
            @endif

            {{-- BOTÕES DE AÇÃO --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <button wire:click="openAccountModal" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg transition-colors">
                    <flux:icon name="plus" variant="micro" class="size-3.5" />
                    <span class="hidden md:inline">Conta</span>
                </button>
                <button wire:click="openTransferModal" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded-lg transition-colors">
                    <flux:icon name="arrows-right-left" variant="micro" class="size-3.5" />
                    <span class="hidden md:inline">Transferência</span>
                </button>
                <button wire:click="openReserveModal" class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-[11px] font-bold rounded-lg transition-colors">
                    <flux:icon name="banknotes" variant="micro" class="size-3.5" />
                    <span>Reserva</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TABS DE NAVEGAÇÃO
══════════════════════════════════════════════════════════════ --}}
<div class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex gap-0 overflow-x-auto scrollbar-hide -mb-px" aria-label="Tabs">
            @php
                $tabs = [
                    ['id' => 'overview',    'label' => 'Visão Geral',  'icon' => 'squares-2x2'],
                    ['id' => 'accounts',    'label' => 'Contas',       'icon' => 'building-library'],
                    ['id' => 'transfers',   'label' => 'Transferências','icon' => 'arrows-right-left'],
                    ['id' => 'reserves',    'label' => 'Reservas',     'icon' => 'banknotes'],
                    ['id' => 'transit',     'label' => 'Em Trânsito',  'icon' => 'clock'],
                    ['id' => 'credits',     'label' => 'Créditos',     'icon' => 'arrow-trending-up'],
                    ['id' => 'debts',       'label' => 'Dívidas',      'icon' => 'hand-raised'],
                    ['id' => 'patrimony',   'label' => 'Património',   'icon' => 'home'],
                    ['id' => 'liquidity',   'label' => 'Liquidez',     'icon' => 'chart-bar'],
                    ['id' => 'goals',       'label' => 'Objetivos',    'icon' => 'trophy'],
                    ['id' => 'stats',       'label' => 'Estatísticas', 'icon' => 'presentation-chart-line'],
                    ['id' => 'alerts',      'label' => 'Alertas',      'icon' => 'bell-alert'],
                ];
            @endphp

            @foreach($tabs as $t)
                <button
                    @click="tab = '{{ $t['id'] }}'"
                    :class="tab === '{{ $t['id'] }}' ? 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200'"
                    class="flex items-center gap-1.5 px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider whitespace-nowrap transition-colors border-b-2 border-transparent"
                >
                    <flux:icon name="{{ $t['icon'] }}" variant="micro" class="size-3.5 flex-shrink-0" />
                    {{ $t['label'] }}
                    @if($t['id'] === 'alerts' && count($alerts) > 0)
                        <span class="ml-1 size-4 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center">{{ count($alerts) }}</span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>
</div>

<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

{{-- ═══════════════════════════════════════════════════════════════
     TAB: VISÃO GERAL
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'overview'" x-cloak class="space-y-6">

    {{-- CARDS DE RESUMO --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

        {{-- DINHEIRO DISPONÍVEL --}}
        <div class="col-span-2 sm:col-span-1 lg:col-span-1 relative bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 overflow-hidden group hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-300">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center justify-between mb-3">
                <div class="size-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <flux:icon name="banknotes" class="size-4 text-emerald-600 dark:text-emerald-400" />
                </div>
                @if($summary['is_available_negative'])
                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Negativo</span>
                @else
                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">Ok</span>
                @endif
            </div>
            <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Disponível</p>
            <p class="text-2xl font-black {{ $summary['available_cash'] < 0 ? 'text-red-600' : 'text-zinc-900 dark:text-white' }} tabular-nums privacy-target">
                {{ number_format($summary['available_cash'], 2, ',', '.') }} €
            </p>
            <p class="text-[10px] text-zinc-400 mt-1">Líquido após reservas</p>
        </div>

        {{-- SALDO BANCÁRIO --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 group hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="size-9 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                <flux:icon name="building-library" class="size-4 text-blue-600 dark:text-blue-400" />
            </div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Saldo Bancário</p>
            <p class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums privacy-target">
                {{ number_format($summary['total_bank_balance'], 2, ',', '.') }} €
            </p>
            <p class="text-[10px] text-zinc-400 mt-1">{{ $accounts->count() }} contas activas</p>
        </div>

        {{-- INVESTIDO --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 group hover:shadow-lg hover:shadow-violet-500/10 transition-all duration-300 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="size-9 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center mb-3">
                <flux:icon name="chart-bar-square" class="size-4 text-violet-600 dark:text-violet-400" />
            </div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Investido</p>
            <p class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums privacy-target">
                {{ number_format($summary['total_investments'], 2, ',', '.') }} €
            </p>
            @php
                $investedPct = $summary['total_patrimony'] > 0
                    ? round(($summary['total_investments'] / $summary['total_patrimony']) * 100, 1)
                    : 0;
            @endphp
            <p class="text-[10px] text-zinc-400 mt-1">{{ $investedPct }}% do património</p>
        </div>

        {{-- RESERVADO --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 group hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-300 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="size-9 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-3">
                <flux:icon name="shield-check" class="size-4 text-amber-600 dark:text-amber-400" />
            </div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Reservado</p>
            <p class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums privacy-target">
                {{ number_format($summary['total_reserves'], 2, ',', '.') }} €
            </p>
            <p class="text-[10px] text-zinc-400 mt-1">{{ $reserves->count() }} reservas activas</p>
        </div>

        {{-- PATRIMÓNIO TOTAL --}}
        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 dark:from-zinc-800 dark:to-zinc-900 rounded-2xl border border-zinc-700 p-5 group hover:shadow-xl hover:shadow-zinc-900/30 transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 size-24 bg-white/5 rounded-bl-full"></div>
            <div class="size-9 rounded-xl bg-white/10 flex items-center justify-center mb-3">
                <flux:icon name="star" class="size-4 text-amber-400" />
            </div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Património Total</p>
            <p class="text-2xl font-black text-white tabular-nums privacy-target">
                {{ number_format($summary['total_patrimony'], 2, ',', '.') }} €
            </p>
            <p class="text-[10px] text-zinc-400 mt-1">
                Líquido: {{ number_format($summary['net_worth'], 2, ',', '.') }} €
            </p>
        </div>
    </div>

    {{-- FLUXO MENSAL + DISTRIBUIÇÃO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- FLUXO DE CAIXA (2/3) --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">Fluxo Financeiro</h3>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Receitas vs. Despesas — últimos 12 meses</p>
                </div>
                <div class="flex items-center gap-4 text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-zinc-500">Receitas</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-full bg-red-400"></span>
                        <span class="text-zinc-500">Despesas</span>
                    </div>
                </div>
            </div>

            {{-- GRÁFICO DE BARRAS SIMPLIFICADO (CSS) --}}
            @php
                $maxFlow = collect($monthlyFlow)->max(fn($m) => max($m['income'], $m['expense']));
                $maxFlow = $maxFlow > 0 ? $maxFlow : 1;
            @endphp
            <div class="flex items-end gap-1 h-40">
                @foreach($monthlyFlow as $month)
                    <div class="flex-1 flex items-end gap-0.5 group relative">
                        {{-- Receita --}}
                        <div
                            class="flex-1 bg-emerald-500/80 hover:bg-emerald-500 rounded-t transition-all"
                            style="height: {{ max(2, ($month['income'] / $maxFlow) * 100) }}%"
                            title="Receita: {{ number_format($month['income'], 2, ',', '.') }} €"
                        ></div>
                        {{-- Despesa --}}
                        <div
                            class="flex-1 bg-red-400/80 hover:bg-red-400 rounded-t transition-all"
                            style="height: {{ max(2, ($month['expense'] / $maxFlow) * 100) }}%"
                            title="Despesa: {{ number_format($month['expense'], 2, ',', '.') }} €"
                        ></div>

                        {{-- Tooltip --}}
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-zinc-900 text-white text-[9px] px-2 py-1 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-10 shadow-xl">
                            <div class="font-bold">{{ $month['label'] }}</div>
                            <div class="text-emerald-400">+{{ number_format($month['income'], 2, ',', '.') }} €</div>
                            <div class="text-red-400">-{{ number_format($month['expense'], 2, ',', '.') }} €</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-1 mt-2">
                @foreach($monthlyFlow as $month)
                    <div class="flex-1 text-center text-[8px] text-zinc-400 truncate">{{ $month['label'] }}</div>
                @endforeach
            </div>

            {{-- RESUMO DO MÊS ACTUAL --}}
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 grid grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-[10px] text-zinc-400 mb-0.5">Entradas</p>
                    <p class="text-base font-black text-emerald-600 privacy-target">+{{ number_format($summary['curr_month_income'], 2, ',', '.') }} €</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-zinc-400 mb-0.5">Saídas</p>
                    <p class="text-base font-black text-red-500 privacy-target">-{{ number_format($summary['curr_month_expense'], 2, ',', '.') }} €</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-zinc-400 mb-0.5">Saldo Mensal</p>
                    <p class="text-base font-black {{ $summary['curr_month_balance'] >= 0 ? 'text-emerald-600' : 'text-red-500' }} privacy-target">
                        {{ $summary['curr_month_balance'] >= 0 ? '+' : '' }}{{ number_format($summary['curr_month_balance'], 2, ',', '.') }} €
                    </p>
                </div>
            </div>
        </div>

        {{-- DISTRIBUIÇÃO DO PATRIMÓNIO (1/3) --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <h3 class="text-sm font-black text-zinc-900 dark:text-white mb-4">Distribuição</h3>

            @if(count($distribution) > 0)
                <div class="space-y-2.5">
                    @foreach($distribution as $item)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300">{{ $item['label'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-zinc-400">{{ $item['pct'] }}%</span>
                                    <span class="text-[11px] font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($item['value'], 0, ',', '.') }} €</span>
                                </div>
                            </div>
                            <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width: {{ $item['pct'] }}%; background-color: {{ $item['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] font-bold text-zinc-500">Total</span>
                        <span class="text-base font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($summary['total_patrimony'], 2, ',', '.') }} €</span>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-zinc-400">
                    <flux:icon name="chart-pie" class="size-10 mx-auto mb-2 opacity-30" />
                    <p class="text-[11px]">Sem dados de distribuição</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ALERTAS + LIQUIDEZ + EM TRÂNSITO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- LIQUIDEZ --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="chart-bar" class="size-4 text-blue-600" />
                <h3 class="text-sm font-black text-zinc-900 dark:text-white">Liquidez</h3>
                @if($liquidity['low_liquidity_warning'])
                    <span class="ml-auto text-[9px] font-black bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full uppercase">Atenção</span>
                @endif
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-[11px] text-zinc-500">Dinheiro Imediato</span>
                    <span class="text-sm font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($liquidity['immediate_cash'], 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[11px] text-zinc-500">Reservas</span>
                    <span class="text-sm font-black text-amber-600 privacy-target">-{{ number_format($liquidity['reserved'], 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[11px] text-zinc-500">Disponível Hoje</span>
                    <span class="text-sm font-black text-emerald-600 privacy-target">{{ number_format($liquidity['available_today'], 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[11px] text-zinc-500">Investido</span>
                    <span class="text-sm font-black text-violet-600 privacy-target">{{ number_format($liquidity['invested'], 2, ',', '.') }} €</span>
                </div>
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="text-center">
                        <span class="text-3xl font-black text-zinc-900 dark:text-white">{{ $liquidity['months_coverage'] }}</span>
                        <span class="text-sm text-zinc-400 ml-1">meses de cobertura</span>
                    </div>
                    <div class="mt-2 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 {{ $liquidity['months_coverage'] >= 6 ? 'bg-emerald-500' : ($liquidity['months_coverage'] >= 3 ? 'bg-amber-500' : 'bg-red-500') }}"
                             style="width: {{ min(100, ($liquidity['months_coverage'] / 12) * 100) }}%"></div>
                    </div>
                    <p class="text-[9px] text-zinc-400 text-center mt-1">Referência: mínimo 3 meses</p>
                </div>
            </div>
        </div>

        {{-- DINHEIRO EM TRÂNSITO --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="size-4 text-orange-600" />
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">Em Trânsito</h3>
                </div>
                <button wire:click="openTransitModal" class="text-[10px] font-bold text-emerald-600 hover:underline">+ Novo</button>
            </div>
            @if($transitItems->count() > 0)
                <div class="space-y-2">
                    @foreach($transitItems->take(4) as $item)
                        <div class="flex items-center gap-3 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="size-6 rounded-full flex items-center justify-center {{ $item->direction === 'in' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                <flux:icon name="{{ $item->direction === 'in' ? 'arrow-down' : 'arrow-up' }}" variant="micro" class="size-3 {{ $item->direction === 'in' ? 'text-emerald-600' : 'text-red-500' }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $item->name }}</p>
                                <p class="text-[9px] text-zinc-400">{{ $item->expected_date?->format('d/m/Y') }}</p>
                            </div>
                            <p class="text-sm font-black {{ $item->direction === 'in' ? 'text-emerald-600' : 'text-red-500' }} privacy-target">
                                {{ $item->direction === 'in' ? '+' : '-' }}{{ number_format($item->amount, 2, ',', '.') }} €
                            </p>
                        </div>
                    @endforeach
                    @if($transitItems->count() > 4)
                        <button @click="tab = 'transit'" class="w-full text-center text-[10px] font-bold text-emerald-600 hover:underline pt-1">
                            Ver mais {{ $transitItems->count() - 4 }} itens →
                        </button>
                    @endif
                </div>
                <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between">
                    <span class="text-[11px] text-zinc-400">A receber</span>
                    <span class="text-sm font-black text-emerald-600 privacy-target">+{{ number_format($summary['total_transit_in'], 2, ',', '.') }} €</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[11px] text-zinc-400">A pagar</span>
                    <span class="text-sm font-black text-red-500 privacy-target">-{{ number_format($summary['total_transit_out'], 2, ',', '.') }} €</span>
                </div>
            @else
                <div class="text-center py-6 text-zinc-400">
                    <flux:icon name="clock" class="size-8 mx-auto mb-2 opacity-30" />
                    <p class="text-[11px]">Sem valores em trânsito</p>
                    <button wire:click="openTransitModal" class="mt-2 text-[10px] font-bold text-emerald-600 hover:underline">Adicionar</button>
                </div>
            @endif
        </div>

        {{-- ALERTAS --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="bell-alert" class="size-4 text-red-500" />
                <h3 class="text-sm font-black text-zinc-900 dark:text-white">Alertas</h3>
            </div>
            @if(count($alerts) > 0)
                <div class="space-y-2">
                    @foreach($alerts as $alert)
                        <div class="flex items-start gap-2.5 p-2.5 rounded-lg {{ $alert['type'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200/60 dark:border-red-700/30' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-700/30' }}">
                            <flux:icon name="{{ $alert['icon'] }}" class="size-4 mt-0.5 flex-shrink-0 {{ $alert['type'] === 'danger' ? 'text-red-500' : 'text-amber-600' }}" />
                            <div class="min-w-0">
                                <p class="text-[11px] font-bold {{ $alert['type'] === 'danger' ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400' }}">{{ $alert['title'] }}</p>
                                <p class="text-[10px] text-zinc-500 mt-0.5">{{ $alert['message'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-zinc-400">
                    <flux:icon name="check-circle" class="size-8 mx-auto mb-2 text-emerald-500 opacity-60" />
                    <p class="text-[11px] font-bold text-emerald-600">Tudo em ordem!</p>
                    <p class="text-[10px] mt-0.5">Sem alertas activos.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- DINHEIRO REALMENTE DISPONÍVEL --}}
    <div class="bg-gradient-to-r from-emerald-900 to-teal-900 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 80%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="calculator" class="size-5 text-emerald-300" />
                <h3 class="text-base font-black">Cálculo de Dinheiro Disponível</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white/10 rounded-xl p-3">
                    <p class="text-[10px] text-emerald-200 font-bold uppercase tracking-wider">Saldo Bancário</p>
                    <p class="text-xl font-black mt-1 privacy-target">{{ number_format($summary['total_bank_balance'], 2, ',', '.') }} €</p>
                </div>
                <div class="bg-white/10 rounded-xl p-3">
                    <p class="text-[10px] text-amber-200 font-bold uppercase tracking-wider">Menos Reservas</p>
                    <p class="text-xl font-black mt-1 text-amber-300 privacy-target">-{{ number_format($summary['total_reserves'], 2, ',', '.') }} €</p>
                </div>
                <div class="bg-white/10 rounded-xl p-3">
                    <p class="text-[10px] text-red-200 font-bold uppercase tracking-wider">Despesas Pendentes</p>
                    <p class="text-xl font-black mt-1 text-red-300 privacy-target">-{{ number_format($summary['pending_expenses'], 2, ',', '.') }} €</p>
                </div>
                <div class="bg-emerald-600/60 rounded-xl p-3 border border-emerald-400/30">
                    <p class="text-[10px] text-emerald-200 font-bold uppercase tracking-wider">= Disponível</p>
                    <p class="text-2xl font-black mt-1 {{ $summary['available_cash'] < 0 ? 'text-red-300' : 'text-emerald-300' }} privacy-target">
                        {{ number_format($summary['available_cash'], 2, ',', '.') }} €
                    </p>
                    @if($summary['is_available_negative'])
                        <p class="text-[9px] text-red-300 mt-0.5 font-bold">⚠ Atenção: valor negativo!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- RESUMO DE CONTAS PESSOAIS E EMPRESARIAIS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- PESSOAL --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="size-7 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <flux:icon name="user" variant="micro" class="size-3.5 text-blue-600" />
                    </div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">Pessoal</h3>
                </div>
                <button wire:click="openAccountModal" class="text-[10px] font-bold text-emerald-600 hover:underline">+ Conta</button>
            </div>
            @if($personalAccounts->count() > 0)
                <div class="space-y-2">
                    @foreach($personalAccounts->take(5) as $acc)
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors group">
                            <div class="size-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $acc->color ?? '#6366f1' }}20">
                                <flux:icon name="{{ $acc->icon ?? 'building-library' }}" class="size-4" style="color: {{ $acc->color ?? '#6366f1' }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $acc->name }}</p>
                                <p class="text-[10px] text-zinc-400">{{ $acc->bank_name ?? $acc->type }}</p>
                            </div>
                            <p class="text-sm font-black {{ $acc->balance < 0 ? 'text-red-500' : 'text-zinc-900 dark:text-white' }} privacy-target">
                                {{ number_format($acc->balance, 2, ',', '.') }} €
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <span class="text-[11px] font-bold text-zinc-500">Total Pessoal</span>
                    <span class="text-base font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($personalAccounts->sum('balance'), 2, ',', '.') }} €</span>
                </div>
            @else
                <div class="text-center py-6 text-zinc-400">
                    <flux:icon name="building-library" class="size-8 mx-auto mb-2 opacity-30" />
                    <p class="text-[11px]">Sem contas pessoais</p>
                    <button wire:click="openAccountModal" class="mt-1 text-[10px] font-bold text-emerald-600 hover:underline">Adicionar conta</button>
                </div>
            @endif
        </div>

        {{-- EMPRESA --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="size-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <flux:icon name="building-office" variant="micro" class="size-3.5 text-emerald-600" />
                    </div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white">Empresa</h3>
                </div>
                <button wire:click="openAccountModal" class="text-[10px] font-bold text-emerald-600 hover:underline">+ Conta</button>
            </div>
            @if($businessAccounts->count() > 0)
                <div class="space-y-2">
                    @foreach($businessAccounts->take(5) as $acc)
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            <div class="size-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: {{ $acc->color ?? '#10b981' }}20">
                                <flux:icon name="{{ $acc->icon ?? 'building-office-2' }}" class="size-4" style="color: {{ $acc->color ?? '#10b981' }}" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $acc->name }}</p>
                                <p class="text-[10px] text-zinc-400">{{ $acc->bank_name ?? $acc->type }}</p>
                            </div>
                            <p class="text-sm font-black {{ $acc->balance < 0 ? 'text-red-500' : 'text-zinc-900 dark:text-white' }} privacy-target">
                                {{ number_format($acc->balance, 2, ',', '.') }} €
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <span class="text-[11px] font-bold text-zinc-500">Total Empresa</span>
                    <span class="text-base font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($businessAccounts->sum('balance'), 2, ',', '.') }} €</span>
                </div>
            @else
                <div class="text-center py-6 text-zinc-400">
                    <flux:icon name="building-office" class="size-8 mx-auto mb-2 opacity-30" />
                    <p class="text-[11px]">Sem contas empresariais</p>
                    <button wire:click="openAccountModal" class="mt-1 text-[10px] font-bold text-emerald-600 hover:underline">Adicionar conta</button>
                </div>
            @endif
        </div>
    </div>

</div>{{-- /overview --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: CONTAS BANCÁRIAS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'accounts'" x-cloak class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-zinc-900 dark:text-white">Contas Bancárias</h2>
            <p class="text-[11px] text-zinc-400 mt-0.5">Gerencie todas as suas contas pessoais e empresariais.</p>
        </div>
        <button wire:click="openAccountModal" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="plus" variant="micro" class="size-4" />
            Adicionar Conta
        </button>
    </div>

    {{-- PESSOAL --}}
    <div>
        <h3 class="text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-3 flex items-center gap-2">
            <flux:icon name="user" variant="micro" class="size-3" /> Contas Pessoais
            <span class="font-black text-zinc-900 dark:text-white text-base ml-2 normal-case tracking-normal privacy-target">
                {{ number_format($personalAccounts->sum('balance'), 2, ',', '.') }} €
            </span>
        </h3>
        @if($personalAccounts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($personalAccounts as $acc)
                    @include('livewire.partials.banco-account-card', ['acc' => $acc])
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 border-dashed">
                <flux:icon name="building-library" class="size-10 mx-auto mb-3 text-zinc-300 dark:text-zinc-700" />
                <p class="text-sm font-bold text-zinc-500">Sem contas pessoais</p>
                <button wire:click="openAccountModal" class="mt-2 text-sm font-bold text-emerald-600 hover:underline">Adicionar primeira conta</button>
            </div>
        @endif
    </div>

    {{-- EMPRESA --}}
    <div>
        <h3 class="text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-3 flex items-center gap-2">
            <flux:icon name="building-office" variant="micro" class="size-3" /> Contas Empresariais
            <span class="font-black text-zinc-900 dark:text-white text-base ml-2 normal-case tracking-normal privacy-target">
                {{ number_format($businessAccounts->sum('balance'), 2, ',', '.') }} €
            </span>
        </h3>
        @if($businessAccounts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($businessAccounts as $acc)
                    @include('livewire.partials.banco-account-card', ['acc' => $acc])
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 border-dashed">
                <flux:icon name="building-office-2" class="size-10 mx-auto mb-3 text-zinc-300 dark:text-zinc-700" />
                <p class="text-sm font-bold text-zinc-500">Sem contas empresariais</p>
                <button wire:click="openAccountModal" class="mt-2 text-sm font-bold text-emerald-600 hover:underline">Adicionar conta empresarial</button>
            </div>
        @endif
    </div>
</div>{{-- /accounts --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: TRANSFERÊNCIAS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'transfers'" x-cloak class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-zinc-900 dark:text-white">Transferências</h2>
            <p class="text-[11px] text-zinc-400 mt-0.5">Histórico de todas as transferências entre contas.</p>
        </div>
        <button wire:click="openTransferModal" class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="plus" variant="micro" class="size-4" />
            Nova Transferência
        </button>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        @if($transfers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Data</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Origem</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Destino</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Descrição</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-zinc-400 uppercase tracking-wider">Valor</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-zinc-400 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($transfers as $tr)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 text-[12px] text-zinc-500">{{ $tr->transferred_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="size-6 rounded-lg flex items-center justify-center" style="background: {{ $tr->fromAccount?->color ?? '#6366f1' }}20">
                                            <flux:icon name="building-library" variant="micro" class="size-3" style="color: {{ $tr->fromAccount?->color ?? '#6366f1' }}" />
                                        </div>
                                        <span class="text-[12px] font-bold text-zinc-800 dark:text-zinc-200">{{ $tr->fromAccount?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="size-6 rounded-lg flex items-center justify-center" style="background: {{ $tr->toAccount?->color ?? '#10b981' }}20">
                                            <flux:icon name="building-library" variant="micro" class="size-3" style="color: {{ $tr->toAccount?->color ?? '#10b981' }}" />
                                        </div>
                                        <span class="text-[12px] font-bold text-zinc-800 dark:text-zinc-200">{{ $tr->toAccount?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-[12px] text-zinc-500">{{ $tr->description ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($tr->amount, 2, ',', '.') }} €</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $tr->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                           ($tr->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' :
                                           'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                        {{ ucfirst($tr->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16 text-zinc-400">
                <flux:icon name="arrows-right-left" class="size-12 mx-auto mb-3 opacity-30" />
                <p class="text-sm font-bold">Sem transferências registadas</p>
                <button wire:click="openTransferModal" class="mt-2 text-sm font-bold text-blue-600 hover:underline">Registar primeira transferência</button>
            </div>
        @endif
    </div>
</div>{{-- /transfers --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: RESERVAS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'reserves'" x-cloak class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-zinc-900 dark:text-white">Reservas Financeiras</h2>
            <p class="text-[11px] text-zinc-400 mt-0.5">Dinheiro guardado para fins específicos. Não conta como disponível.</p>
        </div>
        <button wire:click="openReserveModal" class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="plus" variant="micro" class="size-4" />
            Nova Reserva
        </button>
    </div>

    @if($reserves->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($reserves as $reserve)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl flex items-center justify-center" style="background-color: {{ $reserve->color ?? '#10b981' }}20">
                                <flux:icon name="{{ $reserve->icon ?? 'banknotes' }}" class="size-5" style="color: {{ $reserve->color ?? '#10b981' }}" />
                            </div>
                            <div>
                                <p class="text-sm font-black text-zinc-900 dark:text-white">{{ $reserve->name }}</p>
                                @if($reserve->is_business)
                                    <span class="text-[9px] font-bold bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">Empresa</span>
                                @else
                                    <span class="text-[9px] font-bold bg-zinc-100 text-zinc-600 px-1.5 py-0.5 rounded">Pessoal</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="openReserveModal({{ $reserve->id }})" class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                <flux:icon name="pencil" variant="micro" class="size-3.5 text-zinc-500" />
                            </button>
                            <button wire:click="deleteReserve({{ $reserve->id }})" onclick="return confirm('Eliminar esta reserva?')" class="size-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                <flux:icon name="trash" variant="micro" class="size-3.5 text-red-500" />
                            </button>
                        </div>
                    </div>

                    <p class="text-2xl font-black text-zinc-900 dark:text-white mb-1 privacy-target">{{ number_format($reserve->amount, 2, ',', '.') }} €</p>

                    @if($reserve->target_amount)
                        <div class="mt-3">
                            <div class="flex justify-between text-[10px] text-zinc-400 mb-1.5">
                                <span>{{ number_format($reserve->progress, 1) }}%</span>
                                <span>Meta: {{ number_format($reserve->target_amount, 2, ',', '.') }} €</span>
                            </div>
                            <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700"
                                     style="width: {{ $reserve->progress }}%; background-color: {{ $reserve->color ?? '#10b981' }}"></div>
                            </div>
                        </div>
                    @endif

                    @if($reserve->target_date)
                        <p class="text-[10px] text-zinc-400 mt-2">
                            Meta: {{ \Carbon\Carbon::parse($reserve->target_date)->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- TOTAL --}}
        <div class="bg-zinc-900 dark:bg-zinc-800 rounded-2xl p-4 flex items-center justify-between">
            <span class="text-sm font-bold text-zinc-300">Total Reservado</span>
            <span class="text-xl font-black text-white privacy-target">{{ number_format($reserves->sum('amount'), 2, ',', '.') }} €</span>
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400">
            <flux:icon name="shield-check" class="size-12 mx-auto mb-3 opacity-30" />
            <p class="text-sm font-bold">Sem reservas criadas</p>
            <p class="text-[11px] mt-1">Crie reservas para IRS, férias, emergências e mais.</p>
            <button wire:click="openReserveModal" class="mt-3 text-sm font-bold text-violet-600 hover:underline">Criar primeira reserva</button>
        </div>
    @endif
</div>{{-- /reserves --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: EM TRÂNSITO
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'transit'" x-cloak class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-zinc-900 dark:text-white">Dinheiro em Trânsito</h2>
            <p class="text-[11px] text-zinc-400 mt-0.5">Valores que ainda não entraram ou saíram definitivamente.</p>
        </div>
        <button wire:click="openTransitModal" class="flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="plus" variant="micro" class="size-4" />
            Adicionar
        </button>
    </div>

    @if($transitItems->count() > 0)
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Direção</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Nome</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Origem / Destino</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Data Prevista</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-zinc-400 uppercase tracking-wider">Valor</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-zinc-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($transitItems as $item)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="size-7 rounded-lg flex items-center justify-center {{ $item->direction === 'in' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                        <flux:icon name="{{ $item->direction === 'in' ? 'arrow-down' : 'arrow-up' }}" variant="micro" class="size-4 {{ $item->direction === 'in' ? 'text-emerald-600' : 'text-red-500' }}" />
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-[12px] font-bold text-zinc-800 dark:text-zinc-200">{{ $item->name }}</p>
                                    <p class="text-[10px] text-zinc-400">{{ $item->description }}</p>
                                </td>
                                <td class="px-4 py-3 text-[12px] text-zinc-500">
                                    {{ $item->origin ?? '' }}{{ ($item->origin && $item->destination) ? ' → ' : '' }}{{ $item->destination ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-[12px] text-zinc-500">{{ $item->expected_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-black {{ $item->direction === 'in' ? 'text-emerald-600' : 'text-red-500' }} privacy-target">
                                    {{ $item->direction === 'in' ? '+' : '-' }}{{ number_format($item->amount, 2, ',', '.') }} €
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="confirmTransitItem({{ $item->id }})" title="Confirmar" class="size-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center hover:bg-emerald-100 transition-colors">
                                            <flux:icon name="check" variant="micro" class="size-4 text-emerald-600" />
                                        </button>
                                        <button wire:click="openTransitModal({{ $item->id }})" title="Editar" class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center hover:bg-zinc-200 transition-colors">
                                            <flux:icon name="pencil" variant="micro" class="size-4 text-zinc-500" />
                                        </button>
                                        <button wire:click="deleteTransitItem({{ $item->id }})" onclick="return confirm('Eliminar?')" title="Eliminar" class="size-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center hover:bg-red-100 transition-colors">
                                            <flux:icon name="trash" variant="micro" class="size-4 text-red-500" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400">
            <flux:icon name="clock" class="size-12 mx-auto mb-3 opacity-30" />
            <p class="text-sm font-bold">Sem valores em trânsito</p>
            <button wire:click="openTransitModal" class="mt-2 text-sm font-bold text-orange-600 hover:underline">Adicionar item</button>
        </div>
    @endif
</div>{{-- /transit --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: CRÉDITOS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'credits'" x-cloak class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-zinc-900 dark:text-white">Créditos a Receber</h2>
            <p class="text-[11px] text-zinc-400 mt-0.5">Dinheiro que lhe é devido por terceiros.</p>
        </div>
        <button wire:click="openCreditModal" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="plus" variant="micro" class="size-4" />
            Novo Crédito
        </button>
    </div>

    @if($credits->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($credits as $credit)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 group hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full
                            {{ match($credit->category) {
                                'client' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'employee' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                                'refund' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400',
                            } }}">
                            {{ match($credit->category) {
                                'client' => 'Cliente', 'employee' => 'Funcionário',
                                'friend' => 'Amigo', 'refund' => 'Reembolso', default => $credit->category
                            } }}
                        </span>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="markCreditReceived({{ $credit->id }})" title="Marcar como recebido" class="size-6 rounded bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center hover:bg-emerald-100 transition-colors">
                                <flux:icon name="check" variant="micro" class="size-3.5 text-emerald-600" />
                            </button>
                            <button wire:click="deleteCredit({{ $credit->id }})" onclick="return confirm('Eliminar?')" class="size-6 rounded bg-red-50 dark:bg-red-900/20 flex items-center justify-center hover:bg-red-100 transition-colors">
                                <flux:icon name="trash" variant="micro" class="size-3.5 text-red-500" />
                            </button>
                        </div>
                    </div>
                    <p class="text-base font-black text-zinc-900 dark:text-white">{{ $credit->name }}</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1 privacy-target">+{{ number_format($credit->amount, 2, ',', '.') }} €</p>
                    @if($credit->due_date)
                        <p class="text-[10px] text-zinc-400 mt-2 flex items-center gap-1">
                            <flux:icon name="calendar" variant="micro" class="size-3" />
                            Prazo: {{ $credit->due_date->format('d/m/Y') }}
                            @if($credit->due_date->isPast())
                                <span class="text-red-500 font-bold">• Em atraso</span>
                            @endif
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="bg-zinc-900 dark:bg-zinc-800 rounded-2xl p-4 flex items-center justify-between">
            <span class="text-sm font-bold text-zinc-300">Total a Receber</span>
            <span class="text-xl font-black text-emerald-400 privacy-target">+{{ number_format($credits->sum('amount'), 2, ',', '.') }} €</span>
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400">
            <flux:icon name="arrow-trending-up" class="size-12 mx-auto mb-3 opacity-30" />
            <p class="text-sm font-bold">Sem créditos pendentes</p>
            <button wire:click="openCreditModal" class="mt-2 text-sm font-bold text-emerald-600 hover:underline">Adicionar crédito</button>
        </div>
    @endif
</div>{{-- /credits --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: DÍVIDAS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'debts'" x-cloak class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-black text-zinc-900 dark:text-white">Dívidas</h2>
        <a href="{{ route('hub.debts') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="arrow-top-right-on-square" variant="micro" class="size-4" />
            Ir para Gestão de Dívidas
        </a>
    </div>
    @if($debts->count() > 0)
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Pessoa / Entidade</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Descrição</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Vencimento</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-zinc-400 uppercase tracking-wider">Valor</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-zinc-400 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($debts as $debt)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3 text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ $debt->person_name }}</td>
                                <td class="px-4 py-3 text-[12px] text-zinc-500">{{ $debt->description ?? '—' }}</td>
                                <td class="px-4 py-3 text-[12px] text-zinc-500">
                                    @if($debt->due_at)
                                        <span class="{{ \Carbon\Carbon::parse($debt->due_at)->isPast() ? 'text-red-500 font-bold' : '' }}">
                                            {{ \Carbon\Carbon::parse($debt->due_at)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-black text-red-500 privacy-target">-{{ number_format($debt->amount, 2, ',', '.') }} €</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Pendente</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-red-900/80 rounded-2xl p-4 flex items-center justify-between">
            <span class="text-sm font-bold text-red-200">Total de Dívidas</span>
            <span class="text-xl font-black text-red-300 privacy-target">-{{ number_format($debts->sum('amount'), 2, ',', '.') }} €</span>
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400">
            <flux:icon name="hand-raised" class="size-12 mx-auto mb-3 opacity-30" />
            <p class="text-sm font-bold">Sem dívidas activas</p>
            <a href="{{ route('hub.debts') }}" wire:navigate class="mt-2 text-sm font-bold text-blue-600 hover:underline block">Ir para gestão de dívidas</a>
        </div>
    @endif
</div>{{-- /debts --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: PATRIMÓNIO
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'patrimony'" x-cloak class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-zinc-900 dark:text-white">Património</h2>
            <p class="text-[11px] text-zinc-400 mt-0.5">Ativos não-bancários: imóveis, veículos, ouro, criptomoedas.</p>
        </div>
        <button wire:click="openPatrimonyModal" class="flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="plus" variant="micro" class="size-4" />
            Adicionar Ativo
        </button>
    </div>

    {{-- KPI CARDS DE PATRIMÓNIO --}}
    @php
        $patTypes = [
            'real_estate' => ['label' => 'Imóveis', 'icon' => 'home', 'color' => 'emerald'],
            'vehicle'     => ['label' => 'Veículos', 'icon' => 'truck', 'color' => 'blue'],
            'gold'        => ['label' => 'Ouro', 'icon' => 'star', 'color' => 'amber'],
            'crypto'      => ['label' => 'Cripto', 'icon' => 'cpu-chip', 'color' => 'violet'],
            'other_asset' => ['label' => 'Outros', 'icon' => 'cube', 'color' => 'zinc'],
            'liability'   => ['label' => 'Passivos', 'icon' => 'minus-circle', 'color' => 'red'],
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach($patTypes as $type => $info)
            @php
                $typeTotal = $patrimony->where('type', $type)->sum('value');
                $typeCount = $patrimony->where('type', $type)->count();
            @endphp
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 text-center">
                <div class="size-8 rounded-lg bg-{{ $info['color'] }}-100 dark:bg-{{ $info['color'] }}-900/30 flex items-center justify-center mx-auto mb-2">
                    <flux:icon name="{{ $info['icon'] }}" class="size-4 text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-400" />
                </div>
                <p class="text-[10px] font-bold text-zinc-400 mb-0.5">{{ $info['label'] }}</p>
                <p class="text-base font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($typeTotal, 0, ',', '.') }} €</p>
                <p class="text-[9px] text-zinc-400">{{ $typeCount }} {{ $typeCount === 1 ? 'item' : 'itens' }}</p>
            </div>
        @endforeach
    </div>

    {{-- LISTAGEM --}}
    @if($patrimony->count() > 0)
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Ativo</th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-zinc-400 uppercase tracking-wider">Valor Atual</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-zinc-400 uppercase tracking-wider">Custo</th>
                            <th class="px-4 py-3 text-right text-[10px] font-black text-zinc-400 uppercase tracking-wider">+/-</th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-zinc-400 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($patrimony as $pat)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $pat->name }}</p>
                                    <p class="text-[10px] text-zinc-400">{{ $pat->description }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400">
                                        {{ $patTypes[$pat->type]['label'] ?? $pat->type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($pat->value, 2, ',', '.') }} €</td>
                                <td class="px-4 py-3 text-right text-[12px] text-zinc-400 privacy-target">{{ $pat->purchase_price ? number_format($pat->purchase_price, 2, ',', '.') . ' €' : '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($pat->purchase_price)
                                        <span class="text-sm font-black {{ $pat->gain_loss >= 0 ? 'text-emerald-600' : 'text-red-500' }} privacy-target">
                                            {{ $pat->gain_loss >= 0 ? '+' : '' }}{{ number_format($pat->gain_loss, 2, ',', '.') }} €
                                        </span>
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="openPatrimonyModal({{ $pat->id }})" class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center hover:bg-zinc-200 transition-colors">
                                            <flux:icon name="pencil" variant="micro" class="size-3.5 text-zinc-500" />
                                        </button>
                                        <button wire:click="deletePatrimony({{ $pat->id }})" onclick="return confirm('Eliminar este ativo?')" class="size-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center hover:bg-red-100 transition-colors">
                                            <flux:icon name="trash" variant="micro" class="size-3.5 text-red-500" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400">
            <flux:icon name="home" class="size-12 mx-auto mb-3 opacity-30" />
            <p class="text-sm font-bold">Sem ativos de património registados</p>
            <button wire:click="openPatrimonyModal" class="mt-2 text-sm font-bold text-amber-600 hover:underline">Adicionar ativo</button>
        </div>
    @endif
</div>{{-- /patrimony --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: LIQUIDEZ
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'liquidity'" x-cloak class="space-y-6">
    <h2 class="text-base font-black text-zinc-900 dark:text-white">Indicadores de Liquidez</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="size-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                <flux:icon name="banknotes" class="size-5 text-emerald-600" />
            </div>
            <p class="text-[11px] text-zinc-400 uppercase tracking-wider font-bold mb-1">Dinheiro Imediato</p>
            <p class="text-2xl font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($liquidity['immediate_cash'], 2, ',', '.') }} €</p>
            <p class="text-[10px] text-zinc-400 mt-1">Contas correntes + Poupança + Caixa</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="size-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                <flux:icon name="check-circle" class="size-5 text-blue-600" />
            </div>
            <p class="text-[11px] text-zinc-400 uppercase tracking-wider font-bold mb-1">Disponível Hoje</p>
            <p class="text-2xl font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($liquidity['available_today'], 2, ',', '.') }} €</p>
            <p class="text-[10px] text-zinc-400 mt-1">Após deduzir reservas</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="size-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center mb-3">
                <flux:icon name="chart-bar-square" class="size-5 text-violet-600" />
            </div>
            <p class="text-[11px] text-zinc-400 uppercase tracking-wider font-bold mb-1">Investido</p>
            <p class="text-2xl font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($liquidity['invested'], 2, ',', '.') }} €</p>
            <p class="text-[10px] text-zinc-400 mt-1">Conversão estimada: dias a semanas</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="size-10 rounded-xl {{ $liquidity['low_liquidity_warning'] ? 'bg-red-100 dark:bg-red-900/30' : 'bg-emerald-100 dark:bg-emerald-900/30' }} flex items-center justify-center mb-3">
                <flux:icon name="calendar" class="size-5 {{ $liquidity['low_liquidity_warning'] ? 'text-red-600' : 'text-emerald-600' }}" />
            </div>
            <p class="text-[11px] text-zinc-400 uppercase tracking-wider font-bold mb-1">Cobertura</p>
            <p class="text-2xl font-black {{ $liquidity['low_liquidity_warning'] ? 'text-red-600' : 'text-emerald-600' }}">{{ $liquidity['months_coverage'] }} meses</p>
            <p class="text-[10px] text-zinc-400 mt-1">Despesa média: {{ number_format($liquidity['avg_monthly_expense'], 0, ',', '.') }} €/mês</p>
        </div>
    </div>

    {{-- ANÁLISE DE LIQUIDEZ --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
        <h3 class="text-sm font-black text-zinc-900 dark:text-white mb-4">Análise Detalhada</h3>

        <div class="space-y-4">
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Liquidez Total</span>
                    <span class="text-sm font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($liquidity['total_liquidity'], 2, ',', '.') }} €</span>
                </div>
                <div class="h-3 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                    @php
                        $liqTotal = max(1, $liquidity['total_liquidity']);
                        $cashPct = min(100, ($liquidity['immediate_cash'] / $liqTotal) * 100);
                        $investPct = min(100 - $cashPct, ($liquidity['invested'] / $liqTotal) * 100);
                    @endphp
                    <div class="h-full bg-emerald-500 transition-all" style="width: {{ $cashPct }}%"></div>
                    <div class="h-full bg-violet-500 transition-all" style="width: {{ $investPct }}%"></div>
                </div>
                <div class="flex gap-4 mt-2 text-[10px]">
                    <div class="flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        <span class="text-zinc-400">Imediato: {{ number_format($cashPct, 1) }}%</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-violet-500"></span>
                        <span class="text-zinc-400">Investido: {{ number_format($investPct, 1) }}%</span>
                    </div>
                </div>
            </div>

            {{-- INDICADOR DE SAÚDE --}}
            <div class="mt-4 p-4 rounded-xl {{ $liquidity['months_coverage'] >= 6 ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40' : ($liquidity['months_coverage'] >= 3 ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40') }}">
                <div class="flex items-start gap-3">
                    <flux:icon name="{{ $liquidity['months_coverage'] >= 6 ? 'shield-check' : ($liquidity['months_coverage'] >= 3 ? 'exclamation-triangle' : 'x-circle') }}"
                               class="size-5 mt-0.5 {{ $liquidity['months_coverage'] >= 6 ? 'text-emerald-600' : ($liquidity['months_coverage'] >= 3 ? 'text-amber-600' : 'text-red-600') }}" />
                    <div>
                        <p class="text-sm font-black {{ $liquidity['months_coverage'] >= 6 ? 'text-emerald-800 dark:text-emerald-300' : ($liquidity['months_coverage'] >= 3 ? 'text-amber-800 dark:text-amber-300' : 'text-red-800 dark:text-red-300') }}">
                            @if($liquidity['months_coverage'] >= 6)
                                Excelente liquidez! Tem cobertura para {{ $liquidity['months_coverage'] }} meses.
                            @elseif($liquidity['months_coverage'] >= 3)
                                Liquidez adequada. Tem cobertura para {{ $liquidity['months_coverage'] }} meses.
                            @else
                                Liquidez reduzida! Apenas {{ $liquidity['months_coverage'] }} meses de cobertura.
                            @endif
                        </p>
                        <p class="text-[11px] text-zinc-500 mt-1">Recomenda-se manter pelo menos 3-6 meses de despesas em liquidez imediata.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /liquidity --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: OBJETIVOS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'goals'" x-cloak class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-black text-zinc-900 dark:text-white">Objetivos Financeiros</h2>
        <a href="{{ route('hub.goals') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-bold rounded-xl transition-colors">
            <flux:icon name="arrow-top-right-on-square" variant="micro" class="size-4" />
            Gerir Objetivos
        </a>
    </div>

    @if($goals->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($goals as $goal)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-black text-zinc-900 dark:text-white truncate">{{ $goal->name }}</h3>
                        @if($goal->months_remaining !== null)
                            <span class="text-[10px] font-bold text-zinc-500 flex-shrink-0 ml-2">{{ $goal->months_remaining }} meses</span>
                        @endif
                    </div>
                    <div class="flex items-end justify-between mb-2">
                        <span class="text-xl font-black text-zinc-900 dark:text-white privacy-target">{{ number_format($goal->current_amount, 2, ',', '.') }} €</span>
                        <span class="text-[11px] text-zinc-400">/ {{ number_format($goal->target_amount, 2, ',', '.') }} €</span>
                    </div>
                    <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden mb-1.5">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-700" style="width: {{ $goal->progress }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-zinc-400">
                        <span>{{ number_format($goal->progress, 1) }}% concluído</span>
                        @if($goal->deadline)
                            <span>Prazo: {{ $goal->deadline->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400">
            <flux:icon name="trophy" class="size-12 mx-auto mb-3 opacity-30" />
            <p class="text-sm font-bold">Sem objetivos definidos</p>
            <a href="{{ route('hub.goals') }}" wire:navigate class="mt-2 text-sm font-bold text-emerald-600 hover:underline block">Criar objetivo</a>
        </div>
    @endif
</div>{{-- /goals --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: ESTATÍSTICAS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'stats'" x-cloak class="space-y-6">
    <h2 class="text-base font-black text-zinc-900 dark:text-white">Estatísticas Financeiras</h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['label' => 'Contas Activas',       'value' => $stats['total_accounts'],                     'icon' => 'building-library', 'color' => 'blue',    'suffix' => ''],
                ['label' => 'Transferências',        'value' => $stats['total_transfers'],                    'icon' => 'arrows-right-left', 'color' => 'indigo',  'suffix' => ''],
                ['label' => 'Reservas',              'value' => $stats['total_reserves'],                     'icon' => 'shield-check',      'color' => 'amber',   'suffix' => ''],
                ['label' => 'Maior Entrada',         'value' => number_format($stats['max_income'], 2, ',', '.'), 'icon' => 'arrow-trending-up', 'color' => 'emerald', 'suffix' => ' €'],
                ['label' => 'Maior Saída',           'value' => number_format($stats['max_expense'], 2, ',', '.'), 'icon' => 'arrow-trending-down', 'color' => 'red', 'suffix' => ' €'],
                ['label' => 'Total Movimentado',     'value' => number_format($stats['total_moved'], 2, ',', '.'), 'icon' => 'banknotes',        'color' => 'zinc',    'suffix' => ' €'],
                ['label' => 'Saldo Médio Mensal',    'value' => number_format($stats['avg_monthly_balance'], 2, ',', '.'), 'icon' => 'calculator', 'color' => 'violet', 'suffix' => ' €'],
                ['label' => 'Rentabilidade Invest.', 'value' => number_format($stats['investment_return'], 2, ',', '.'), 'icon' => 'chart-bar-square', 'color' => ($stats['investment_return'] >= 0 ? 'emerald' : 'red'), 'suffix' => ' %'],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5">
                <div class="size-9 rounded-xl bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30 flex items-center justify-center mb-3">
                    <flux:icon name="{{ $card['icon'] }}" class="size-4 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400" />
                </div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">{{ $card['label'] }}</p>
                <p class="text-xl font-black text-zinc-900 dark:text-white privacy-target">{{ $card['value'] }}{{ $card['suffix'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- COMPARAÇÃO PESSOAL VS EMPRESA --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
        <h3 class="text-sm font-black text-zinc-900 dark:text-white mb-4">Pessoal vs. Empresa</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div></div>
            <div class="text-[11px] font-black text-zinc-400 uppercase tracking-wider">Pessoal</div>
            <div class="text-[11px] font-black text-zinc-400 uppercase tracking-wider">Empresa</div>

            @php
                $compareItems = [
                    ['label' => 'Saldo Disponível', 'personal' => $personalAccounts->sum('balance'), 'business' => $businessAccounts->sum('balance'), 'format' => 'money'],
                    ['label' => 'Nº Contas',         'personal' => $personalAccounts->count(),         'business' => $businessAccounts->count(),         'format' => 'number'],
                    ['label' => 'Reservas',          'personal' => $reserves->where('is_business', false)->sum('amount'), 'business' => $reserves->where('is_business', true)->sum('amount'), 'format' => 'money'],
                ];
            @endphp

            @foreach($compareItems as $item)
                <div class="text-[12px] font-bold text-zinc-600 dark:text-zinc-400 text-left">{{ $item['label'] }}</div>
                <div class="text-sm font-black text-zinc-900 dark:text-white privacy-target">
                    {{ $item['format'] === 'money' ? number_format($item['personal'], 2, ',', '.') . ' €' : $item['personal'] }}
                </div>
                <div class="text-sm font-black text-zinc-900 dark:text-white privacy-target">
                    {{ $item['format'] === 'money' ? number_format($item['business'], 2, ',', '.') . ' €' : $item['business'] }}
                </div>
            @endforeach
        </div>
    </div>
</div>{{-- /stats --}}


{{-- ═══════════════════════════════════════════════════════════════
     TAB: ALERTAS
══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'alerts'" x-cloak class="space-y-4">
    <h2 class="text-base font-black text-zinc-900 dark:text-white">Alertas Inteligentes</h2>

    @if(count($alerts) > 0)
        <div class="space-y-3">
            @foreach($alerts as $alert)
                <div class="flex items-start gap-4 p-4 rounded-2xl {{ $alert['type'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40' }}">
                    <div class="size-10 rounded-xl {{ $alert['type'] === 'danger' ? 'bg-red-100 dark:bg-red-900/40' : 'bg-amber-100 dark:bg-amber-900/40' }} flex items-center justify-center flex-shrink-0">
                        <flux:icon name="{{ $alert['icon'] }}" class="size-5 {{ $alert['type'] === 'danger' ? 'text-red-600' : 'text-amber-600' }}" />
                    </div>
                    <div>
                        <p class="text-sm font-black {{ $alert['type'] === 'danger' ? 'text-red-800 dark:text-red-300' : 'text-amber-800 dark:text-amber-300' }}">
                            {{ $alert['title'] }}
                        </p>
                        <p class="text-[12px] text-zinc-500 mt-0.5">{{ $alert['message'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 text-zinc-400">
            <div class="size-16 mx-auto mb-4 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                <flux:icon name="shield-check" class="size-8 text-emerald-600" />
            </div>
            <p class="text-base font-black text-emerald-700 dark:text-emerald-400">Sem alertas activos!</p>
            <p class="text-[12px] text-zinc-400 mt-1">A sua situação financeira está em ordem.</p>
        </div>
    @endif

    {{-- INFO SOBRE ALERTAS AUTOMÁTICOS --}}
    <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5">
        <h3 class="text-[11px] font-black text-zinc-500 uppercase tracking-wider mb-3">Alertas Automáticos Activos</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-zinc-500">
            @foreach([
                'Conta abaixo do limite de alerta',
                'Saldo negativo em qualquer conta',
                'Créditos com prazo ultrapassado',
                'Liquidez inferior a 3 meses',
                'Dívidas com vencimento nos próximos 7 dias',
            ] as $alertInfo)
                <div class="flex items-center gap-2">
                    <flux:icon name="check-circle" variant="micro" class="size-3.5 text-emerald-500 flex-shrink-0" />
                    {{ $alertInfo }}
                </div>
            @endforeach
        </div>
    </div>
</div>{{-- /alerts --}}


</div>{{-- /main content --}}

{{-- ═══════════════════════════════════════════════════════════════
     MODAL UNIVERSAL
══════════════════════════════════════════════════════════════ --}}
@if($showModal)
    <div
        x-data
        x-show="true"
        x-transition.opacity
        class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4"
        wire:click.self="$set('showModal', false)"
    >
        <div class="absolute inset-0 bg-zinc-950/70 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>

        <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 w-full max-w-xl max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            {{-- HEADER DO MODAL --}}
            <div class="flex items-center justify-between p-5 border-b border-zinc-200 dark:border-zinc-800 sticky top-0 bg-white dark:bg-zinc-900 rounded-t-2xl z-10">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                    @if($modalType === 'account')
                        {{ $editingId ? 'Editar Conta' : 'Nova Conta Bancária' }}
                    @elseif($modalType === 'transfer')
                        Registar Transferência
                    @elseif($modalType === 'reserve')
                        {{ $editingId ? 'Editar Reserva' : 'Nova Reserva' }}
                    @elseif($modalType === 'transit')
                        {{ $editingId ? 'Editar Trânsito' : 'Novo Item em Trânsito' }}
                    @elseif($modalType === 'credit')
                        {{ $editingId ? 'Editar Crédito' : 'Novo Crédito a Receber' }}
                    @elseif($modalType === 'patrimony')
                        {{ $editingId ? 'Editar Ativo' : 'Novo Ativo de Património' }}
                    @endif
                </h3>
                <button wire:click="$set('showModal', false)" class="size-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                    <flux:icon name="x-mark" variant="micro" class="size-4 text-zinc-500" />
                </button>
            </div>

            <div class="p-5 space-y-4">

                {{-- FORMULÁRIO DE CONTA --}}
                @if($modalType === 'account')
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Nome da Conta *</label>
                            <input wire:model="acc_name" type="text" placeholder="Ex: Conta Principal"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('acc_name') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Tipo *</label>
                            <select wire:model="acc_type" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="corrente">Conta Corrente</option>
                                <option value="poupanca">Conta Poupança</option>
                                <option value="cash">Dinheiro Físico</option>
                                <option value="credito">Cartão de Crédito</option>
                                <option value="investimento">Conta Investimento</option>
                                <option value="tesouraria">Tesouraria</option>
                                <option value="paypal">PayPal / Digital</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Saldo Atual *</label>
                            <input wire:model="acc_balance" type="number" step="0.01" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Banco</label>
                            <input wire:model="acc_bank_name" type="text" placeholder="Ex: Millennium BCP"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Moeda</label>
                            <select wire:model="acc_currency" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="EUR">EUR €</option>
                                <option value="USD">USD $</option>
                                <option value="GBP">GBP £</option>
                                <option value="CHF">CHF Fr.</option>
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">IBAN</label>
                            <input wire:model="acc_iban" type="text" placeholder="PT50 XXXX XXXX XXXX XXXX XXXX X"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Cor</label>
                            <div class="flex items-center gap-2">
                                <input wire:model="acc_color" type="color" class="size-10 rounded-lg border border-zinc-200 dark:border-zinc-700 cursor-pointer">
                                <span class="text-[11px] text-zinc-400">{{ $acc_color }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Alerta abaixo de</label>
                            <input wire:model="acc_alert_below" type="number" step="0.01" placeholder="Ex: 100.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div class="col-span-2 flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="acc_is_business" type="checkbox" class="size-4 rounded accent-emerald-600">
                                <span class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Conta Empresarial</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="acc_include" type="checkbox" class="size-4 rounded accent-emerald-600">
                                <span class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Incluir nos totais</span>
                            </label>
                        </div>
                    </div>

                {{-- FORMULÁRIO DE TRANSFERÊNCIA --}}
                @elseif($modalType === 'transfer')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Conta Origem *</label>
                            <select wire:model="tr_from_id" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">— Selecionar —</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ number_format($acc->balance, 2, ',', '.') }} €)</option>
                                @endforeach
                            </select>
                            @error('tr_from_id') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Conta Destino *</label>
                            <select wire:model="tr_to_id" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">— Selecionar —</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                            @error('tr_to_id') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Valor *</label>
                            <input wire:model="tr_amount" type="number" step="0.01" min="0.01" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Data *</label>
                            <input wire:model="tr_date" type="date"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Descrição</label>
                            <input wire:model="tr_description" type="text" placeholder="Ex: Transferência para poupança"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Estado</label>
                            <select wire:model="tr_status" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="completed">Concluída</option>
                                <option value="pending">Pendente</option>
                                <option value="failed">Falhada</option>
                            </select>
                        </div>
                    </div>

                {{-- FORMULÁRIO DE RESERVA --}}
                @elseif($modalType === 'reserve')
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Nome da Reserva *</label>
                            <input wire:model="res_name" type="text" placeholder="Ex: IRS, Férias, Emergência"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Valor Reservado *</label>
                            <input wire:model="res_amount" type="number" step="0.01" min="0" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Meta (opcional)</label>
                            <input wire:model="res_target" type="number" step="0.01" min="0" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Data Alvo</label>
                            <input wire:model="res_target_date" type="date"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Cor</label>
                            <input wire:model="res_color" type="color" class="size-10 rounded-lg border border-zinc-200 dark:border-zinc-700 cursor-pointer">
                        </div>

                        <div class="col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="res_is_business" type="checkbox" class="size-4 rounded accent-violet-600">
                                <span class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Reserva Empresarial</span>
                            </label>
                        </div>
                    </div>

                {{-- FORMULÁRIO DE TRÂNSITO --}}
                @elseif($modalType === 'transit')
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Nome / Descrição *</label>
                            <input wire:model="trs_name" type="text" placeholder="Ex: Pagamento Stripe Pendente"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Valor *</label>
                            <input wire:model="trs_amount" type="number" step="0.01" min="0.01" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Direção *</label>
                            <select wire:model="trs_direction" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="in">A Receber (Entrada)</option>
                                <option value="out">A Pagar (Saída)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Tipo</label>
                            <select wire:model="trs_type" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="transfer_sent">Transferência</option>
                                <option value="stripe_payment">Pagamento Stripe</option>
                                <option value="refund">Reembolso</option>
                                <option value="pending_payment">Pagamento Pendente</option>
                                <option value="pending_receipt">Recebimento Pendente</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Data Prevista</label>
                            <input wire:model="trs_expected_date" type="date"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Origem</label>
                            <input wire:model="trs_origin" type="text" placeholder="Ex: Stripe, Cliente A"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                {{-- FORMULÁRIO DE CRÉDITO --}}
                @elseif($modalType === 'credit')
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Nome / Quem deve *</label>
                            <input wire:model="crd_name" type="text" placeholder="Ex: João Silva, Cliente XYZ"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Valor *</label>
                            <input wire:model="crd_amount" type="number" step="0.01" min="0.01" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Categoria</label>
                            <select wire:model="crd_category" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="client">Cliente</option>
                                <option value="employee">Funcionário</option>
                                <option value="friend">Amigo/Família</option>
                                <option value="refund">Reembolso</option>
                                <option value="other">Outro</option>
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Prazo</label>
                            <input wire:model="crd_due_date" type="date"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                {{-- FORMULÁRIO DE PATRIMÓNIO --}}
                @elseif($modalType === 'patrimony')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Tipo de Ativo *</label>
                            <select wire:model="pat_type" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="real_estate">Imóvel</option>
                                <option value="vehicle">Veículo</option>
                                <option value="gold">Ouro / Metais</option>
                                <option value="crypto">Criptomoeda</option>
                                <option value="other_asset">Outro Ativo</option>
                                <option value="liability">Passivo / Dívida</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Nome / Descrição *</label>
                            <input wire:model="pat_name" type="text" placeholder="Ex: Apartamento Lisboa, BMW 320"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Valor Atual *</label>
                            <input wire:model="pat_value" type="number" step="0.01" min="0" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Preço de Compra</label>
                            <input wire:model="pat_purchase_price" type="number" step="0.01" min="0" placeholder="0.00"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Data de Compra</label>
                            <input wire:model="pat_purchase_date" type="date"
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider mb-1.5">Descrição</label>
                            <textarea wire:model="pat_description" rows="2" placeholder="Detalhes adicionais..."
                                      class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 text-sm text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="pat_is_business" type="checkbox" class="size-4 rounded accent-amber-600">
                                <span class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Ativo Empresarial</span>
                            </label>
                        </div>
                    </div>
                @endif

                {{-- ERROS GERAIS --}}
                @if($errors->any())
                    <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-700/40">
                        @foreach($errors->all() as $error)
                            <p class="text-[11px] text-red-600 dark:text-red-400">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- BOTÕES --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled" class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Guardar</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin size-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            A guardar...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

</div>{{-- /x-data --}}

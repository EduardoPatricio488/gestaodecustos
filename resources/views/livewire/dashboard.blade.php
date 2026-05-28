<div class="space-y-8 pb-10">
    @php
        $firstName = explode(' ', auth()->user()->name)[0] ?? auth()->user()->name;

        // Lógica de partilha
        $others = $currentWs->users->where('id', '!=', auth()->id())->pluck('name')->map(fn($name) => explode(' ', $name)[0]);
        $sharedText = $others->count() > 0
            ? "Partilhada com " . $others->implode(', ')
            : "Conta Individual";

        // Tickers de Mercado
        $tickers = [
            'BTC' => ['price' => $marketPrices['bitcoin']['eur'] ?? 0, 'change' => $marketPrices['bitcoin']['eur_24h_change'] ?? 0, 'icon' => 'currency-dollar'],
            'ETH' => ['price' => $marketPrices['ethereum']['eur'] ?? 0, 'change' => $marketPrices['ethereum']['eur_24h_change'] ?? 0, 'icon' => 'sparkles'],
            'S&P 500' => ['price' => 5222.68, 'change' => 0.45, 'icon' => 'chart-bar'],
            'NVIDIA' => ['price' => 945.30, 'change' => 1.25, 'icon' => 'cpu-chip'],
        ];
    @endphp

    {{-- 1. TICKER DE MERCADO (ESTILO TERMINAL) --}}
    <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar">
        @foreach($tickers as $symbol => $data)
            <div class="flex items-center gap-3 px-4 py-2 bg-white dark:bg-zinc-900/50 backdrop-blur-md rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm min-w-fit hover:border-brand-500/30 transition-colors">
                <div class="size-2 rounded-full {{ $data['change'] >= 0 ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></div>
                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ $symbol }}</span>
                <span class="text-xs font-bold dark:text-white">{{ number_format($data['price'], 2, ',', ' ') }}€</span>
                <span class="text-[9px] font-black {{ $data['change'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                    {{ $data['change'] >= 0 ? '▲' : '▼' }}{{ abs(round($data['change'], 1)) }}%
                </span>
            </div>
        @endforeach
    </div>

    {{-- 2. BARRA DE NÍVEL E XP (DESIGN INTEGRADO) --}}
    <div class="group relative flex items-center gap-6 bg-gradient-to-r from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-5 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden">
        {{-- Efeito Decorativo de Fundo --}}
        <div class="absolute -right-10 -top-10 size-40 bg-brand-500/5 blur-3xl rounded-full group-hover:bg-brand-500/10 transition-all duration-700"></div>

        {{-- Badge de Nível --}}
        <div class="relative flex-shrink-0">
            <div class="flex flex-col items-center justify-center w-20 h-20 rounded-[1.8rem] bg-brand-600 text-white shadow-2xl shadow-brand-500/40 relative z-10">
                <span class="text-[9px] font-black uppercase opacity-60 tracking-tighter">Nível</span>
                <span class="text-3xl font-black leading-none">{{ auth()->user()->level }}</span>
            </div>
            {{-- Indicador de Progresso Circular em volta do nível (CSS Subtil) --}}
            <div class="absolute inset-0 rounded-[1.8rem] border-2 border-brand-500/20 scale-110"></div>
        </div>

        <div class="flex-1 space-y-3">
            <div class="flex justify-between items-end">
                <div>
                    <h4 class="text-xs font-black uppercase text-zinc-500 tracking-[0.15em]">Experiência Financeira</h4>
                    <p class="text-[10px] text-zinc-400 font-bold mt-0.5">Faltam {{ 1000 - (auth()->user()->xp % 1000) }} XP para o próximo escalão</p>
                </div>
                <div class="text-right">
                    <span class="text-xs font-black text-brand-600 dark:text-brand-400 uppercase italic">{{ (auth()->user()->xp % 1000) / 10 }}%</span>
                </div>
            </div>

            {{-- Barra de Progresso Estilizada --}}
            <div class="relative h-3 w-full bg-zinc-200/50 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-100 dark:border-zinc-700 shadow-inner">
                <div class="h-full bg-brand-500 rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(59,130,246,0.6)]" style="width: {{ (auth()->user()->xp % 1000) / 10 }}%"></div>
            </div>
        </div>

        {{-- Medalhas/Badges --}}
        <div class="hidden lg:flex items-center gap-3 pl-6 border-l border-zinc-200 dark:border-zinc-800">
            @forelse(auth()->user()->badges->take(3) as $badge)
                <div title="{{ $badge->name }}" class="size-10 rounded-2xl flex items-center justify-center text-xl shadow-lg hover:scale-110 transition-transform cursor-help" style="background: {{ $badge->color }}15; border: 1px solid {{ $badge->color }}30;">
                    {{ $badge->icon }}
                </div>
            @empty
                <span class="text-[10px] text-zinc-400 uppercase font-black italic w-24 text-center leading-tight">Sem medalhas este mês</span>
            @endforelse
        </div>
    </div>
<div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="p-3 bg-brand-500/10 rounded-2xl text-brand-600">
            <flux:icon name="bell" variant="outline" class="size-6" />
        </div>
        <div>
            <h3 class="font-black dark:text-white uppercase text-sm">Alertas Push</h3>
            <p class="text-[10px] text-zinc-500 uppercase font-bold">Recebe avisos de gastos e orçamentos no telemóvel</p>
        </div>
    </div>

    <flux:button onclick="enableNotifications()" variant="primary" class="rounded-xl font-black uppercase text-[10px] px-6">
        Ativar balões
    </flux:button>
</div>
    {{-- 3. HEADER PRINCIPAL --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pt-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter uppercase italic">Olá, {{ $firstName }}</h1>
                {{-- INDICADOR DE MODO SUPORTE --}}
                @if(session()->has('impersonator_id'))
                    <a href="{{ route('admin.stop-impersonating') }}" class="flex items-center gap-2 px-3 py-1 bg-amber-500 text-white rounded-full animate-pulse text-[9px] font-black uppercase tracking-widest shadow-lg shadow-amber-500/30">
                        Suporte Ativo · Sair
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-2 mt-2">
                <span class="flex size-2 rounded-full bg-emerald-500"></span>
                <p class="text-sm font-bold text-zinc-400 italic">{{ $sharedText }} · {{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2 rounded-[1.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:button href="{{ route('ai') }}" variant="ghost" icon="sparkles" class="text-purple-600 rounded-xl hover:bg-purple-50" wire:navigate />

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

            <flux:modal.trigger name="export-pdf-modal">
                <flux:button variant="ghost" icon="document-arrow-down" class="text-zinc-500 rounded-xl" />
            </flux:modal.trigger>

            <flux:button href="{{ route('expenses') }}" variant="primary" class="rounded-xl px-6 font-black uppercase tracking-tighter" wire:navigate>
                Despesas
            </flux:button>
            <flux:button href="{{ route('hub.incomes') }}" variant="filled" class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-6 font-black uppercase tracking-tighter" wire:navigate>
                Receitas
            </flux:button>
        </div>
    </div>

    {{-- 4. SELETOR DE ESPAÇO (WORKSPACE SWITCHER) --}}
    @if($userWorkspaces->count() > 1)
        <div class="flex items-center gap-4 bg-zinc-100/50 dark:bg-zinc-900/50 p-1.5 rounded-2xl w-fit border border-zinc-200/50 dark:border-zinc-800/50">
            <div class="px-3 py-1 text-[9px] font-black uppercase text-zinc-500 tracking-widest border-r border-zinc-200 dark:border-zinc-800">Espaços</div>
            <div class="flex gap-1 overflow-x-auto no-scrollbar">
                @foreach($userWorkspaces as $ws)
                    <button
                        wire:click="switchWorkspace({{ $ws->id }})"
                        class="flex items-center gap-2 px-4 py-1.5 rounded-xl transition-all {{ $ws->id == $currentWs->id ? 'bg-white dark:bg-zinc-800 shadow-sm text-brand-600 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
                    >
                        <div class="size-1.5 rounded-full {{ $ws->id == $currentWs->id ? 'bg-brand-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]' : 'bg-zinc-300 dark:bg-zinc-700' }}"></div>
                        <span class="text-xs font-black uppercase tracking-tighter">{{ $ws->name }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 5. GRID DE PERFORMANCE (STATS CARDS) --}}
    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-5">

        {{-- Card: Saldo Real --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <div class="absolute top-0 left-0 w-1 h-full {{ $netBalance >= 0 ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Saldo Líquido</p>
            <h3 class="text-2xl font-black {{ $netBalance >= 0 ? 'text-emerald-600' : 'text-red-500' }} tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $currentWs->money($netBalance) }}
                </span>
            </h3>
            <flux:icon name="banknotes" class="absolute -right-2 -bottom-2 size-12 text-zinc-100 dark:text-zinc-800/50 group-hover:scale-110 transition-transform" />
        </div>

        {{-- Card: Gasto Atual --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Gasto em {{ now()->translatedFormat('M') }}</p>
            <h3 class="text-2xl font-black dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $currentWs->money($totalMonth) }}
                </span>
            </h3>
            <flux:icon name="arrow-trending-down" class="absolute -right-2 -bottom-2 size-12 text-zinc-100 dark:text-zinc-800/50 group-hover:rotate-12 transition-transform" />
        </div>

        {{-- Card: Património Investido (Destaque Premium) --}}
        <div class="relative overflow-hidden bg-zinc-950 dark:bg-brand-600 p-5 rounded-[2rem] shadow-xl shadow-brand-500/10 group">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-brand-400 dark:text-brand-100 uppercase tracking-widest mb-1">Portefólio Ativo</p>
                <h3 class="text-2xl font-black text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ $currentWs->money($portfolioValue) }}
                    </span>
                </h3>
            </div>
            <flux:icon name="chart-bar-square" class="absolute -right-3 -bottom-3 size-16 text-white/10 group-hover:scale-125 transition-transform" />
            <div class="absolute top-0 right-0 p-3">
                <div class="size-2 rounded-full bg-brand-400 animate-ping"></div>
            </div>
        </div>

        {{-- Card: Score Saúde --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Saúde Financeira</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black text-brand-600 tracking-tighter">{{ $overallScore }}%</h3>
                <span class="text-[9px] font-bold text-emerald-500">▲ 2%</span>
            </div>
            <div class="mt-2 h-1 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500" style="width: {{ $overallScore }}%"></div>
            </div>
        </div>

        {{-- Card: Poupança Total --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-5 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Poupança Total</p>
            <h3 class="text-2xl font-black text-emerald-500 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $currentWs->money($totalSaved) }}
                </span>
            </h3>
            <flux:icon name="shield-check" class="absolute -right-2 -bottom-2 size-12 text-zinc-100 dark:text-zinc-800/50 group-hover:scale-110 transition-transform" />
        </div>
    </div>

    {{-- 6. SECÇÃO DE INTELIGÊNCIA E PREVISÃO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- CARD: PREVISÃO INTELIGENTE (ESTILO BLACK GLASS) --}}
        <div class="lg:col-span-2 relative overflow-hidden bg-zinc-950 text-white rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            {{-- Efeito de Luz IA ao fundo --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/10 blur-[100px] rounded-full -mr-20 -mt-20"></div>

            <div class="relative z-10 p-8 flex flex-col md:flex-row justify-between gap-10">
                <div class="space-y-6 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-brand-500/20 rounded-lg">
                            <flux:icon name="sparkles" class="size-5 text-brand-400" />
                        </div>
                        <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Projeção de Fim de Mês</h2>
                    </div>

                    <div>
                        <p class="text-xs text-zinc-400 font-medium mb-2 uppercase tracking-widest">Saldo Estimado</p>
                        <h3 class="text-5xl font-black tracking-tighter {{ $projectedBalance >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            <span :class="privacyMode ? 'blur-xl select-none' : ''" class="transition-all duration-700 inline-block">
                                {{ $currentWs->money($projectedBalance) }}
                            </span>
                        </h3>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-white/5 rounded-2xl border border-white/5 backdrop-blur-sm">
                        <div class="relative flex-shrink-0">
                            <div class="size-3 rounded-full {{ $projectionStatus == 'critical' ? 'bg-red-500' : ($projectionStatus == 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}"></div>
                            <div class="absolute inset-0 size-3 rounded-full {{ $projectionStatus == 'critical' ? 'bg-red-500' : ($projectionStatus == 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }} animate-ping"></div>
                        </div>
                        <p class="text-xs font-bold text-zinc-300">
                            @if($projectionStatus == 'critical') Risco de saldo negativo detetado. Considera adiar compras.
                            @elseif($projectionStatus == 'warning') Poupança ligeiramente abaixo da meta mensal.
                            @else Gestão exemplar. O teu ritmo médio é de <b class="text-white">{{ $currentWs->money($totalMonth / max(now()->day, 1)) }}/dia</b>. @endif
                        </p>
                    </div>
                </div>

                {{-- Mini Métricas de Apoio --}}
                <div class="flex flex-col justify-center gap-6 border-l border-white/10 pl-10 min-w-[200px]">
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Gasto Projetado</p>
                        <p class="text-2xl font-black text-zinc-200">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($projectedExpenses) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-1">Receita Prevista</p>
                        <p class="text-2xl font-black text-emerald-500">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                {{ $currentWs->money($totalIncomeMonth) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: SCORE DE SAÚDE (ESTILO CIRCULAR/VISUAL) --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center p-8 text-center group">
            <div class="relative size-32 mb-4 flex items-center justify-center">
                {{-- SVG Circular Progress Subtil --}}
                <svg class="absolute inset-0 size-full -rotate-90">
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-100 dark:text-zinc-800" />
                    <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent"
                        class="{{ $overallScore > 70 ? 'text-emerald-500' : ($overallScore > 40 ? 'text-amber-500' : 'text-red-500') }} transition-all duration-1000"
                        stroke-dasharray="364.4"
                        stroke-dashoffset="{{ 364.4 - (364.4 * $overallScore) / 100 }}"
                        stroke-linecap="round" />
                </svg>
                <span class="text-4xl font-black dark:text-white tracking-tighter">{{ $overallScore }}%</span>
            </div>

            <h4 class="text-xs font-black uppercase text-zinc-500 tracking-widest">Saúde Financeira</h4>
            <p class="text-[10px] text-zinc-400 mt-2 font-medium">Baseado no teu rácio de poupança e cumprimento de orçamentos.</p>

            <div class="mt-6 w-full px-4">
                <flux:button variant="ghost" size="sm" class="w-full rounded-xl text-[10px] font-black uppercase tracking-widest">Ver análise detalhada</flux:button>
            </div>
        </div>
    </div>

    {{-- 7. GRÁFICOS E ORÇAMENTOS (VISUAL ANALYTICS) --}}
    <div class="grid gap-6 lg:grid-cols-5">

        {{-- GRÁFICO: FLUXO DE CAIXA (6 MESES) --}}
        <div class="glass-card p-6 lg:col-span-3 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Tendência de Fluxo</h2>
                    <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Análise Semestral de Caixa</p>
                </div>
                {{-- Legenda Discreta --}}
                <div class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-emerald-500"></div>
                        <span class="text-[9px] font-black uppercase text-zinc-500">Ganhos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-brand-500"></div>
                        <span class="text-[9px] font-black uppercase text-zinc-500">Gastos</span>
                    </div>
                </div>
            </div>

            {{-- Container das Barras --}}
            <div class="flex h-64 items-end gap-3 sm:gap-6 px-2 relative z-10">
                @foreach ($last6 as $m)
                    @php
                        $hE = ($m['earned'] / ($chartMax ?: 1)) * 100;
                        $hS = ($m['spent'] / ($chartMax ?: 1)) * 100;
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-3 h-full justify-end group/bar">
                        <div class="flex items-end gap-1.5 w-full h-full pb-2">
                            {{-- Barra Ganhos --}}
                            <div class="flex-1 bg-emerald-500/20 hover:bg-emerald-500 rounded-t-lg transition-all duration-500 cursor-help relative"
                                 style="height: {{ max(4, $hE) }}%"
                                 title="Ganhos: {{ $currentWs->money($m['earned']) }}">
                            </div>
                            {{-- Barra Gastos --}}
                            <div class="flex-1 bg-brand-500/20 hover:bg-brand-500 rounded-t-lg transition-all duration-500 cursor-help relative"
                                 style="height: {{ max(4, $hS) }}%"
                                 title="Gastos: {{ $currentWs->money($m['spent']) }}">
                            </div>
                        </div>
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest group-hover/bar:text-brand-500 transition-colors">{{ $m['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Linhas de Grelha de Fundo Subtis --}}
            <div class="absolute inset-0 flex flex-col justify-between p-6 pointer-events-none opacity-20 dark:opacity-10">
                @foreach(range(1, 4) as $i) <div class="w-full border-t border-dashed border-zinc-300 dark:border-zinc-700"></div> @endforeach
                <div class="w-full"></div>
            </div>
        </div>

        {{-- WIDGET: ESTADO DOS ORÇAMENTOS (PROGRESS BARS) --}}
        <div class="glass-card p-6 lg:col-span-2 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col group">
            <div class="mb-8">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Limites por Categoria</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Performance de Gastos</p>
            </div>

            <div class="space-y-6 overflow-y-auto flex-1 pr-2 custom-scrollbar">
                @forelse ($byCategory as $cat)
                    <div class="group/item">
                        <div class="flex justify-between items-end mb-2">
                            <div class="flex flex-col">
                                <span class="text-xs font-black uppercase tracking-tight text-zinc-800 dark:text-zinc-200 group-hover/item:text-brand-500 transition-colors">{{ $cat->name }}</span>
                                <span class="text-[9px] font-bold {{ $cat->over ? 'text-red-500' : 'text-zinc-400' }} uppercase italic">
                                    {{ $cat->over ? 'Orçamento Excedido' : 'Dentro da Meta' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black {{ $cat->over ? 'text-red-500' : 'text-zinc-600 dark:text-zinc-300' }}">
                                    {{ $currentWs->money($cat->total) }}
                                </span>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase">/ {{ $currentWs->money($cat->budget) }}</span>
                            </div>
                        </div>

                        {{-- Barra Neon Subtil --}}
                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800/50 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800">
                            <div class="h-full transition-all duration-1000 ease-out {{ $cat->over ? 'bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.4)]' : 'bg-brand-500 shadow-[0_0_10px_rgba(59,130,246,0.4)]' }}"
                                 style="width: {{ min($cat->percentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <flux:icon name="document-magnifying-glass" class="size-10 text-zinc-200 dark:text-zinc-800 mb-4" />
                        <p class="text-zinc-400 text-[10px] font-black uppercase tracking-widest italic">Sem orçamentos ativos.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <flux:button href="{{ route('categories') }}" variant="ghost" size="sm" class="w-full rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500" wire:navigate>
                    Configurar Orçamentos
                </flux:button>
            </div>
        </div>
    </div>

    {{-- 8. MOVIMENTOS RECENTES (ESTILO TIMELINE) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Fluxo de Caixa</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Movimentos Recentes</p>
            </div>
            <flux:button variant="ghost" size="sm" class="rounded-xl text-[10px] font-black uppercase tracking-widest" href="{{ route('expenses') }}" wire:navigate>Ver Histórico Completo</flux:button>
        </div>

        <div class="divide-y divide-zinc-50 dark:divide-zinc-800/50">
            @forelse ($recent as $e)
                <div class="flex items-center justify-between p-6 hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                    <div class="flex items-center gap-5">
                        {{-- Ícone da Categoria ou Avatar do User --}}
                        <div class="relative">
                            <div class="size-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 group-hover/row:scale-110 group-hover/row:bg-brand-500 group-hover/row:text-white transition-all shadow-sm">
                                <flux:icon name="banknotes" variant="outline" class="size-5" />
                            </div>
                            <div class="absolute -bottom-1 -right-1 size-5 rounded-full border-2 border-white dark:border-zinc-900 overflow-hidden shadow-sm" title="Registado por {{ $e->user->name }}">
                                <div class="size-full bg-brand-600 flex items-center justify-center text-[8px] font-black text-white uppercase">
                                    {{ substr($e->user->name, 0, 1) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-tight">{{ $e->description ?: $e->category?->name }}</span>
                                <span class="text-[8px] font-black px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 rounded-md uppercase tracking-widest">{{ $e->category?->name ?? 'Geral' }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest italic">{{ $e->spent_at->translatedFormat('d M Y') }}</p>
                                <span class="text-zinc-300 dark:text-zinc-700">·</span>
                                <p class="text-[10px] text-zinc-500 font-black uppercase">{{ $e->user->id === auth()->id() ? 'Tu' : explode(' ', $e->user->name)[0] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- VALOR COM PRIVACIDADE --}}
                    <div class="text-right">
                        <span class="text-lg font-black text-red-500 tracking-tighter">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                -{{ $currentWs->money($e->amount) }}
                            </span>
                        </span>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <flux:icon name="magnifying-glass" class="size-12 text-zinc-200 dark:text-zinc-800 mb-4" />
                    <p class="text-zinc-400 text-xs font-black uppercase tracking-widest italic">Nenhum movimento registado neste espaço.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 9. MODAL: COLABORAÇÃO (VISUAL SaaS) --}}
    <flux:modal name="collab-modal" position="center" class="md:w-[500px] !p-0 overflow-hidden">
        <div class="p-10 space-y-8 bg-white dark:bg-zinc-950">
            <div class="text-center space-y-2">
                <flux:heading size="xl" class="font-black uppercase italic tracking-tighter">Convidar Membros</flux:heading>
                <p class="text-sm text-zinc-500 font-medium">Partilha o acesso ao teu espaço de gestão financeira.</p>
            </div>

            <div class="p-8 bg-zinc-900 rounded-[2rem] border border-zinc-800 text-center space-y-4 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-brand-500/5 blur-3xl rounded-full"></div>
                <h4 class="relative z-10 text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">O Teu Código Único</h4>
                <div class="relative z-10 flex items-center justify-center gap-6">
                    <span class="text-4xl font-mono font-black text-brand-500 uppercase tracking-[0.3em]">{{ $currentWs->invite_code ?: '--------' }}</span>
                    <flux:button wire:click="generateInviteCode" icon="arrow-path" variant="ghost" class="text-zinc-500 hover:text-brand-500" />
                </div>
            </div>

            <div class="space-y-4 pt-4">
                <div class="flex items-center gap-2 px-2">
                    <flux:icon name="plus-circle" class="size-4 text-zinc-400" />
                    <span class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">Entrar num grupo existente</span>
                </div>
                <div class="flex gap-2 p-2 bg-zinc-100 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                    <flux:input wire:model="inviteCodeInput" placeholder="INSERIR CÓDIGO" class="flex-1 !bg-transparent border-none uppercase font-mono font-black tracking-widest" />
                    <flux:button wire:click="joinWorkspace" variant="primary" class="rounded-xl px-6 font-black uppercase text-[10px]">Entrar</flux:button>
                </div>
            </div>

            <flux:button x-on:click="$dispatch('modal-close')" variant="ghost" class="w-full font-bold text-zinc-400">Fechar Janela</flux:button>
        </div>
    </flux:modal>

    {{-- 10. MODAL: EXPORTAÇÃO PDF --}}
    <flux:modal name="export-pdf-modal" position="center" class="md:w-[480px]">
        <div class="space-y-8 p-4">
            <div class="flex flex-col items-center text-center gap-3">
                <div class="size-16 bg-brand-500/10 rounded-3xl flex items-center justify-center">
                    <flux:icon name="document-arrow-down" class="size-8 text-brand-600" />
                </div>
                <div>
                    <flux:heading size="lg" class="font-black uppercase tracking-tight">Gerar Relatório Financeiro</flux:heading>
                    <p class="text-xs text-zinc-500 font-medium">Exporta os teus dados em formato PDF profissional.</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="exportStart" type="date" label="Data de Início" class="rounded-xl" />
                    <flux:input wire:model="exportEnd" type="date" label="Data de Fim" class="rounded-xl" />
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Conteúdo do PDF</p>
                    <div class="flex flex-col gap-3">
                        <flux:checkbox wire:model.live="exportExpenses" label="Listagem de Gastos Detalhada" class="font-bold text-sm" />
                        <flux:checkbox wire:model.live="exportIncomes" label="Resumo de Receitas e Ganhos" class="font-bold text-sm" />
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <flux:modal.close><flux:button variant="ghost" class="flex-1 font-bold">Cancelar</flux:button></flux:modal.close>
                <flux:button wire:click="downloadCustomPdf" variant="primary" icon="check" class="flex-1 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">Gerar PDF</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DISCRETO --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Gestão Inteligente
        </p>
    </footer>

    {{-- ESTILO DA SCROLLBAR --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
    </style>
</div>

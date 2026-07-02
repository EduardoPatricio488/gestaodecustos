<div class="space-y-8 pb-24" x-data="netWorthHub()" x-init="init()">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 1. HEADER                                                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-1">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                    <flux:icon name="briefcase" class="w-8 h-8 text-brand-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Património Líquido</h1>
                    <span class="text-[9px] font-black uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-3 py-1 rounded-full">Wealth Report</span>
                </div>
                <p class="text-xs text-zinc-400 mt-1">Análise estrutural · ativos · passivos · solvabilidade · {{ now()->format('d M Y') }}</p>
            </div>
        </div>
        <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="rounded-2xl font-bold self-start md:self-auto">Voltar</flux:button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 2. HERO EXECUTIVO                                              --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-zinc-950 text-white p-8 lg:p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800">
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-500/10 blur-[120px] rounded-full -mr-32 -mt-32 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/5 blur-[100px] rounded-full -ml-20 -mb-20 pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            {{-- Valor principal --}}
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.4em] text-brand-400 mb-2">Valor Líquido Total</p>
                    <div class="text-5xl sm:text-7xl font-black tracking-tighter italic leading-none tabular-nums">
                        @php $nw = $netWorth; @endphp
                        <span class="{{ $nw >= 0 ? 'text-white' : 'text-red-400' }}">
                            {{ number_format(abs($nw), 2, ',', ' ') }} <span class="text-2xl sm:text-3xl">€</span>
                        </span>
                    </div>
                    @if($netWorth < 0)
                        <p class="text-red-400 text-xs font-bold mt-2 uppercase tracking-widest">⚠ Passivo supera ativos</p>
                    @endif
                </div>

                {{-- Score de saúde --}}
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 flex-shrink-0">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 56 56">
                            <circle cx="28" cy="28" r="22" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="5"/>
                            <circle cx="28" cy="28" r="22" fill="none"
                                stroke="{{ $healthScore >= 70 ? '#10b981' : ($healthScore >= 40 ? '#f59e0b' : '#ef4444') }}"
                                stroke-width="5"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round($healthScore * 1.382) }} 138.2"
                                style="transition: stroke-dasharray 1.5s cubic-bezier(.4,0,.2,1)"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-black">{{ round($healthScore) }}</span>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase font-black tracking-widest text-zinc-400">Score de Saúde</p>
                        <p class="text-sm font-bold {{ $healthScore >= 70 ? 'text-emerald-400' : ($healthScore >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                            {{ $healthScore >= 70 ? 'Sólida' : ($healthScore >= 40 ? 'Moderada' : 'Crítica') }}
                        </p>
                        <p class="text-[10px] text-zinc-500 mt-0.5">Taxa de poupança: {{ round($avgSavingsRate) }}%</p>
                    </div>
                </div>

                {{-- Ativos vs Passivos --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Ativos Totais</p>
                        <p class="text-xl font-black text-emerald-400 tracking-tighter tabular-nums">{{ number_format($totalAssets, 0, ',', ' ') }} €</p>
                    </div>
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Passivos</p>
                        <p class="text-xl font-black text-red-400 tracking-tighter tabular-nums">{{ number_format($liabilities, 0, ',', ' ') }} €</p>
                    </div>
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Liquidez</p>
                        <p class="text-xl font-black text-sky-400 tracking-tighter tabular-nums">{{ number_format($totalBankBalance, 0, ',', ' ') }} €</p>
                    </div>
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Solvabilidade</p>
                        <p class="text-xl font-black {{ $solvencyRatio >= 2 ? 'text-emerald-400' : 'text-amber-400' }} tracking-tighter">
                            {{ $solvencyRatio >= 99 ? '∞' : number_format($solvencyRatio, 1, ',', '') }}x
                        </p>
                    </div>
                </div>
            </div>

            {{-- Distribuição visual --}}
            <div class="lg:col-span-4 bg-white/5 p-6 rounded-3xl border border-white/8 space-y-5">
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Composição do Ativo</p>

                @php
                    $slices = [
                        ['label' => 'Liquidez (Bancos)',  'value' => $totalBankBalance,  'color' => '#38bdf8', 'pct' => $liquidityRatio],
                        ['label' => 'Investimentos',      'value' => $investmentsValue,  'color' => '#818cf8', 'pct' => $investmentExposure],
                        ['label' => 'Metas de Poupança', 'value' => $goalsSaved,         'color' => '#34d399', 'pct' => $savingsHealth],
                    ];
                    $maxVal = max(array_column($slices, 'value'), 0.01);
                @endphp

                {{-- Donut SVG --}}
                @php
                    $total = $totalAssets ?: 1;
                    $donutR = 40; $donutC = 50;
                    $circ = 2 * M_PI * $donutR;
                    $offset = 0;
                    $donutColors = ['#38bdf8','#818cf8','#34d399'];
                    $donutVals  = [$totalBankBalance, $investmentsValue, $goalsSaved];
                @endphp
                <div class="flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-36 h-36">
                        @foreach($donutVals as $idx => $val)
                            @php
                                $pct   = $val / $total;
                                $dash  = $pct * $circ;
                                $gap   = $circ - $dash;
                            @endphp
                            <circle cx="{{ $donutC }}" cy="{{ $donutC }}" r="{{ $donutR }}"
                                fill="none"
                                stroke="{{ $donutColors[$idx] }}"
                                stroke-width="12"
                                stroke-dasharray="{{ round($dash, 2) }} {{ round($gap, 2) }}"
                                stroke-dashoffset="{{ round(-$offset, 2) }}"
                                transform="rotate(-90 {{ $donutC }} {{ $donutC }})"
                                opacity="0.9"/>
                            @php $offset += $dash; @endphp
                        @endforeach
                        <text x="50" y="46" text-anchor="middle" fill="white" font-size="8" font-weight="900" font-family="monospace">NET</text>
                        <text x="50" y="57" text-anchor="middle" fill="white" font-size="7" font-family="monospace">WORTH</text>
                    </svg>
                </div>

                {{-- Barras de composição --}}
                <div class="space-y-3.5">
                    @foreach($slices as $slice)
                        <div class="space-y-1">
                            <div class="flex justify-between text-[10px] font-bold text-zinc-300">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $slice['color'] }}"></span>
                                    {{ $slice['label'] }}
                                </span>
                                <span style="color:{{ $slice['color'] }}">{{ round($slice['pct']) }}%</span>
                            </div>
                            <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000"
                                    style="width:{{ min(100, $slice['pct']) }}%; background:{{ $slice['color'] }}; box-shadow:0 0 8px {{ $slice['color'] }}80"></div>
                            </div>
                            <p class="text-[10px] text-zinc-500 tabular-nums">{{ number_format($slice['value'], 0, ',', ' ') }} €</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Rácios rápidos --}}
            <div class="lg:col-span-3 space-y-3">
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Rácios Chave</p>
                @php
                    $ratios = [
                        ['label' => 'Dívida / Ativo',       'val' => round($debtToAssetRatio, 1).'%',  'good' => $debtToAssetRatio < 30,   'tip' => 'Ideal < 30%'],
                        ['label' => 'Taxa de Poupança',     'val' => round($avgSavingsRate, 1).'%',     'good' => $avgSavingsRate > 20,      'tip' => 'Ideal > 20%'],
                        ['label' => 'Solvabilidade',        'val' => ($solvencyRatio >= 99 ? '∞' : number_format($solvencyRatio,1,',','')).'x', 'good' => $solvencyRatio >= 2, 'tip' => 'Ideal > 2x'],
                        ['label' => 'Exposição Invest.',    'val' => round($investmentExposure, 1).'%', 'good' => $investmentExposure > 30,   'tip' => 'Ideal > 30%'],
                        ['label' => 'Progresso Metas',      'val' => round($goalsProgress, 1).'%',      'good' => $goalsProgress > 50,        'tip' => ''],
                    ];
                @endphp
                @foreach($ratios as $r)
                    <div class="flex items-center justify-between px-4 py-3 bg-white/5 rounded-2xl border border-white/8 hover:bg-white/8 transition-colors">
                        <div>
                            <p class="text-[9px] uppercase font-black tracking-widest text-zinc-500">{{ $r['label'] }}</p>
                            @if($r['tip'])
                                <p class="text-[9px] text-zinc-600 mt-0.5">{{ $r['tip'] }}</p>
                            @endif
                        </div>
                        <span class="text-sm font-black {{ $r['good'] ? 'text-emerald-400' : 'text-amber-400' }} tabular-nums">{{ $r['val'] }}</span>
                    </div>
                @endforeach

                {{-- Subscrições anuais --}}
                <div class="px-4 py-3 bg-red-500/10 rounded-2xl border border-red-500/20">
                    <p class="text-[9px] uppercase font-black tracking-widest text-red-400">Custo Anual Subs.</p>
                    <p class="text-sm font-black text-red-300 tabular-nums mt-0.5">{{ number_format($totalAnnualSubscriptions, 0, ',', ' ') }} €/ano</p>
                    <p class="text-[10px] text-red-400/60 mt-0.5">{{ $activeSubscriptions->count() }} subscrições ativas</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 3. GRÁFICO DE FLUXO (últimos 12 meses)                        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Fluxo de Caixa · 12 Meses</h2>
                <p class="text-xs text-zinc-400 mt-0.5">Rendimentos vs despesas mensais</p>
            </div>
            <div class="flex gap-4 text-[10px] font-black uppercase tracking-widest">
                <span class="flex items-center gap-1.5 text-emerald-500"><span class="w-2.5 h-2.5 rounded bg-emerald-500"></span>Rendimento</span>
                <span class="flex items-center gap-1.5 text-red-400"><span class="w-2.5 h-2.5 rounded bg-red-400"></span>Despesa</span>
                <span class="flex items-center gap-1.5 text-brand-500"><span class="w-2.5 h-2.5 rounded bg-brand-500"></span>Saldo</span>
            </div>
        </div>

        @php
            $months = $last12Months->values();
            $maxBar = max($months->max('income'), $months->max('expense'), 1);
            $barH   = 120;
        @endphp

        <div class="overflow-x-auto">
            <div class="flex items-end gap-1 min-w-[640px]" style="height:{{ $barH + 40 }}px">
                @foreach($months as $m)
                    @php
                        $incH  = round(($m['income'] / $maxBar) * $barH);
                        $expH  = round(($m['expense'] / $maxBar) * $barH);
                        $net   = $m['net'];
                        $netPct = $maxBar > 0 ? ($net / $maxBar) * 100 : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-0.5 group">
                        {{-- Net dot --}}
                        <div class="w-full flex justify-center mb-1">
                            <div class="w-1.5 h-1.5 rounded-full {{ $net >= 0 ? 'bg-brand-500' : 'bg-red-400' }} opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        {{-- Bars --}}
                        <div class="w-full flex items-end justify-center gap-px" style="height:{{ $barH }}px">
                            <div class="w-5/12 rounded-t transition-all duration-700 bg-emerald-400/80 hover:bg-emerald-400"
                                style="height:{{ $incH }}px; min-height:2px"></div>
                            <div class="w-5/12 rounded-t transition-all duration-700 bg-red-400/80 hover:bg-red-400"
                                style="height:{{ $expH }}px; min-height:2px"></div>
                        </div>
                        {{-- Label --}}
                        <p class="text-[9px] font-bold text-zinc-400 uppercase mt-1 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                            {{ $m['label'] }}
                        </p>
                        {{-- Tooltip on hover --}}
                        <div class="hidden group-hover:block absolute bg-zinc-900 dark:bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-[9px] font-bold whitespace-nowrap shadow-xl z-10 -translate-y-full -translate-x-1/2 left-1/2 text-white pointer-events-none">
                            +{{ number_format($m['income'], 0, ',', ' ') }} / -{{ number_format($m['expense'], 0, ',', ' ') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-zinc-100 dark:border-zinc-800">
            <div class="text-center">
                <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Média Receita (3m)</p>
                <p class="text-lg font-black text-emerald-500 tabular-nums mt-1">{{ number_format($avg3Income, 0, ',', ' ') }} €</p>
            </div>
            <div class="text-center">
                <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Média Despesa (3m)</p>
                <p class="text-lg font-black text-red-400 tabular-nums mt-1">{{ number_format($avg3Expense, 0, ',', ' ') }} €</p>
            </div>
            <div class="text-center">
                <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Taxa Poupança (3m)</p>
                <p class="text-lg font-black {{ $avgSavingsRate > 20 ? 'text-brand-500' : 'text-amber-500' }} tabular-nums mt-1">{{ round($avgSavingsRate) }}%</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 4. GRID: INVESTIMENTOS + CONTAS + METAS                       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- 4a. INVESTIMENTOS --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Carteira de Investimentos</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Valor de mercado atual</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($investmentsValue, 0, ',', ' ') }} €</p>
                    <p class="text-[10px] font-bold {{ $unrealizedGain >= 0 ? 'text-emerald-500' : 'text-red-400' }} tabular-nums">
                        {{ $unrealizedGain >= 0 ? '+' : '' }}{{ number_format($unrealizedGain, 0, ',', ' ') }} € ({{ $unrealizedGain >= 0 ? '+' : '' }}{{ round($unrealizedGainPct, 1) }}%)
                    </p>
                </div>
            </div>

            {{-- Por tipo --}}
            @if($investmentsByType->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
                    @foreach($investmentsByType as $type => $data)
                        @php $typePct = $investmentsValue > 0 ? ($data['value'] / $investmentsValue * 100) : 0; @endphp
                        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-2xl p-3.5">
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest">{{ $type }}</p>
                            <p class="text-base font-black text-zinc-900 dark:text-white tabular-nums mt-0.5">{{ number_format($data['value'], 0, ',', ' ') }} €</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex-1 h-0.5 bg-zinc-200 dark:bg-zinc-700 rounded-full">
                                    <div class="h-full bg-indigo-500 rounded-full" style="width:{{ round($typePct) }}%"></div>
                                </div>
                                <span class="text-[9px] font-bold text-zinc-400">{{ round($typePct) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Top 5 --}}
            @if($topInvestments->count())
                <div class="space-y-2">
                    <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest mb-3">Top Posições</p>
                    @foreach($topInvestments as $inv)
                        @php
                            $cost   = $inv['quantity'] * $inv['average_price'];
                            $val    = $inv['current_value'];
                            $gain   = $val - $cost;
                            $gainPct = $cost > 0 ? ($gain / $cost * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <span class="text-[8px] font-black text-indigo-600 dark:text-indigo-400 uppercase">{{ substr($inv['symbol'] ?? $inv['name'], 0, 3) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-zinc-900 dark:text-white">{{ Str::limit($inv['name'], 22) }}</p>
                                    <p class="text-[9px] text-zinc-400">{{ $inv['type'] ?? '—' }} · {{ $inv['quantity'] }} un.</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($val, 0, ',', ' ') }} €</p>
                                <p class="text-[10px] font-bold {{ $gain >= 0 ? 'text-emerald-500' : 'text-red-400' }} tabular-nums">
                                    {{ $gain >= 0 ? '+' : '' }}{{ round($gainPct, 1) }}%
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-400 text-center py-6">Sem investimentos registados.</p>
            @endif
        </div>

        {{-- 4b. CONTAS + METAS --}}
        <div class="space-y-5">

            {{-- Contas Bancárias --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm mb-4">Contas Bancárias</h2>
                @forelse($bankAccounts as $acc)
                    <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $acc->color ?? '#6366f1' }}"></div>
                            <div>
                                <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $acc->name }}</p>
                                <p class="text-[9px] text-zinc-400 uppercase">{{ $acc->type }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-black {{ $acc->balance >= 0 ? 'text-zinc-900 dark:text-white' : 'text-red-400' }} tabular-nums">
                            {{ number_format($acc->balance, 0, ',', ' ') }} €
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 text-center py-3">Sem contas.</p>
                @endforelse
                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between">
                    <span class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Total Liquidez</span>
                    <span class="text-sm font-black text-sky-500 tabular-nums">{{ number_format($totalBankBalance, 0, ',', ' ') }} €</span>
                </div>
            </div>

            {{-- Metas de Poupança --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Metas de Poupança</h2>
                    <span class="text-[9px] font-black text-emerald-500">{{ round($goalsProgress) }}% global</span>
                </div>
                @forelse($goals->take(4) as $goal)
                    @php $gp = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount * 100) : 0; @endphp
                    <div class="mb-3 last:mb-0">
                        <div class="flex justify-between text-[10px] font-bold mb-1">
                            <span class="text-zinc-700 dark:text-zinc-300 truncate max-w-[60%]">{{ $goal->name }}</span>
                            <span class="text-zinc-500 tabular-nums">{{ number_format($goal->current_amount, 0, ',', ' ') }} / {{ number_format($goal->target_amount, 0, ',', ' ') }} €</span>
                        </div>
                        <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-400 transition-all duration-700" style="width:{{ min(100,$gp) }}%"></div>
                        </div>
                        @if($goal->deadline)
                            <p class="text-[9px] text-zinc-400 mt-0.5">Prazo: {{ \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-zinc-400 text-center py-3">Sem metas definidas.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 5. DÍVIDAS + SUBSCRIÇÕES                                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Dívidas --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Passivos / Dívidas</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Por liquidar</p>
                </div>
                <span class="text-base font-black text-red-400 tabular-nums">{{ number_format($liabilities, 0, ',', ' ') }} €</span>
            </div>

            @if($upcomingDebts->count())
                <div class="mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-2xl px-4 py-3">
                    <p class="text-[9px] uppercase font-black text-amber-600 dark:text-amber-400 tracking-widest">⚠ {{ $upcomingDebts->count() }} dívida(s) nos próximos 30 dias</p>
                    <p class="text-sm font-black text-amber-700 dark:text-amber-300 tabular-nums mt-0.5">{{ number_format($upcomingDebts->sum('amount'), 0, ',', ' ') }} €</p>
                </div>
            @endif

            @forelse($debts->take(6) as $debt)
                <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                    <div>
                        <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $debt->person_name }}</p>
                        <div class="flex gap-2 mt-0.5">
                            <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">{{ $debt->type }}</span>
                            @if($debt->due_at)
                                <span class="text-[9px] text-zinc-400">{{ \Carbon\Carbon::parse($debt->due_at)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm font-black text-red-400 tabular-nums">{{ number_format($debt->amount, 0, ',', ' ') }} €</p>
                </div>
            @empty
                <div class="text-center py-8">
                    <flux:icon name="check-circle" class="w-8 h-8 text-emerald-400 mx-auto mb-2" />
                    <p class="text-sm font-bold text-emerald-500">Sem dívidas registadas</p>
                </div>
            @endforelse
        </div>

        {{-- Subscrições --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Subscrições Ativas</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Compromissos recorrentes</p>
                </div>
                <div class="text-right">
                    <p class="text-base font-black text-red-400 tabular-nums">{{ number_format($monthlySubscriptionCost, 0, ',', ' ') }} €/mês</p>
                    <p class="text-[9px] text-zinc-400 tabular-nums">{{ number_format($totalAnnualSubscriptions, 0, ',', ' ') }} €/ano</p>
                </div>
            </div>

            @forelse($activeSubscriptions->sortByDesc('amount')->take(8) as $sub)
                <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <span class="text-[9px] font-black text-zinc-500">{{ strtoupper(substr($sub->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $sub->name }}</p>
                            <p class="text-[9px] text-zinc-400 uppercase">{{ $sub->cycle }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-zinc-700 dark:text-zinc-300 tabular-nums">{{ number_format($sub->amount, 2, ',', ' ') }} €</p>
                        @if($sub->renewal_date)
                            <p class="text-[9px] text-zinc-400">renova {{ \Carbon\Carbon::parse($sub->renewal_date)->format('d/m') }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-400 text-center py-8">Sem subscrições ativas.</p>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 6. AI INSIGHT                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-6 lg:p-8 relative overflow-hidden"
         x-data="{
            loading: false,
            insight: '',
            generated: false,
            async generate() {
                if (this.generated) return;
                this.loading = true;
                const prompt = `Sou um consultor financeiro a analisar o seguinte resumo de patrimônio líquido:\n\n- Valor Líquido: {{ number_format($netWorth, 2, ',', '.') }}€\n- Ativos Totais: {{ number_format($totalAssets, 2, ',', '.') }}€\n- Passivos: {{ number_format($liabilities, 2, ',', '.') }}€\n- Liquidez (bancos): {{ number_format($totalBankBalance, 2, ',', '.') }}€\n- Investimentos: {{ number_format($investmentsValue, 2, ',', '.') }}€\n- Metas de poupança: {{ number_format($goalsSaved, 2, ',', '.') }}€\n- Taxa de poupança (3m): {{ round($avgSavingsRate, 1) }}%\n- Rácio dívida/ativo: {{ round($debtToAssetRatio, 1) }}%\n- Solvabilidade: {{ $solvencyRatio >= 99 ? 'sem dívidas' : number_format($solvencyRatio, 1, ',', '.') }}x\n- Score de saúde financeira: {{ round($healthScore) }}/100\n- Exposição a investimentos: {{ round($investmentExposure, 1) }}%\n- Custo anual de subscrições: {{ number_format($totalAnnualSubscriptions, 0, ',', '.') }}€\n\nFaz uma análise concisa e prática em português europeu (máximo 4 parágrafos curtos). Identifica os pontos fortes, os riscos principais e dá 2-3 ações concretas prioritárias. Sê direto, sem rodeios.`;
                try {
                    const res = await fetch('https://api.anthropic.com/v1/messages', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            model: 'claude-sonnet-4-6',
                            max_tokens: 1000,
                            messages: [{ role: 'user', content: prompt }]
                        })
                    });
                    const data = await res.json();
                    this.insight = data.content?.[0]?.text || 'Não foi possível gerar análise.';
                    this.generated = true;
                } catch(e) {
                    this.insight = 'Erro ao gerar análise. Verifica a tua chave de API.';
                } finally {
                    this.loading = false;
                }
            }
         }"
         x-init="$nextTick(() => generate())">

        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/5 blur-[80px] rounded-full pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-xl bg-brand-500/20 flex items-center justify-center">
                    <flux:icon name="sparkles" class="w-4 h-4 text-brand-400" />
                </div>
                <div>
                    <h2 class="text-sm font-black text-white uppercase tracking-tight">Análise AI · Consultor Financeiro</h2>
                    <p class="text-[9px] text-zinc-500">Gerado com base nos teus dados reais</p>
                </div>
            </div>

            <div x-show="loading" class="flex items-center gap-3 py-4">
                <div class="flex gap-1">
                    <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
                <span class="text-xs text-zinc-500">A analisar o teu patrimônio…</span>
            </div>

            <div x-show="!loading && insight" x-html="insight.replace(/\n\n/g,'<br><br>').replace(/\*\*(.*?)\*\*/g,'<strong class=\'text-white\'>$1</strong>')"
                class="text-sm text-zinc-300 leading-relaxed space-y-3 [&_strong]:font-black">
            </div>

            <div x-show="!loading && !insight" class="text-xs text-zinc-500 py-4 italic">
                A iniciar análise…
            </div>

            <button x-show="generated" @click="generated=false; insight=''; generate()"
                class="mt-5 text-[9px] uppercase font-black tracking-widest text-zinc-500 hover:text-brand-400 transition-colors flex items-center gap-1.5">
                <flux:icon name="arrow-path" class="w-3 h-3" /> Regenerar análise
            </button>
        </div>
    </div>

</div>

@push('scripts')
<script>
function netWorthHub() {
    return {
        init() {}
    }
}
</script>
@endpush

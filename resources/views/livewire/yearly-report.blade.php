<div class="space-y-8">
    <!-- Cabeçalho com Seletor de Ano -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tight">Relatório Anual</h1>
            <p class="text-sm text-zinc-500 font-medium">Análise de performance financeira de {{ $year }}</p>
        </div>

        <div class="flex gap-2 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-xl">
            @foreach([now()->year - 1, now()->year] as $y)
                <button wire:click="setYear({{ $y }})"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition {{ $year == $y ? 'bg-white dark:bg-zinc-700 shadow-sm dark:text-white' : 'text-zinc-500' }}">
                    {{ $y }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- RESUMO DO ANO -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stat-card bg-emerald-600 text-white p-6 rounded-3xl shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">Rendimento Total</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($yearlyEarned, 2, ',', ' ') }} €</p>
            <p class="mt-1 text-[10px] opacity-70">Média mensal: {{ number_format($yearlyEarned / 12, 0) }}€</p>
        </div>

        <div class="stat-card bg-zinc-900 text-white p-6 rounded-3xl shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Gasto Total</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($yearlySpent, 2, ',', ' ') }} €</p>
            <p class="mt-1 text-[10px] opacity-50">Média mensal: {{ number_format($yearlySpent / 12, 0) }}€</p>
        </div>

        <div class="stat-card p-6 rounded-3xl shadow-sm border-2 {{ $yearlySavings >= 0 ? 'border-emerald-500/20 bg-emerald-50/10' : 'border-red-500/20 bg-red-50/10' }}">
            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Poupança Real</p>
            <p class="mt-2 text-3xl font-black {{ $yearlySavings >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                {{ number_format($yearlySavings, 2, ',', ' ') }} €
            </p>
            @php $rate = $yearlyEarned > 0 ? ($yearlySavings / $yearlyEarned) * 100 : 0; @endphp
            <p class="mt-1 text-[10px] font-bold {{ $rate >= 20 ? 'text-emerald-500' : 'text-zinc-400' }}">
                TAXA DE POUPANÇA: {{ round($rate, 1) }}%
            </p>
        </div>
    </div>

    <!-- GRÁFICO DE BARRAS MENSAL -->
    <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl">
        <h2 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-10">Fluxo de Caixa Mensal</h2>
        <div class="flex h-64 items-end gap-2 md:gap-4 px-2">
            @foreach ($monthlyStats as $m)
                @php
                    $hEarned = ($m['earned'] / $chartMax) * 100;
                    $hSpent = ($m['spent'] / $chartMax) * 100;
                @endphp
                <div class="flex flex-1 flex-col items-center gap-2 h-full justify-end group relative">
                    <!-- Tooltip ao passar o rato -->
                    <div class="absolute bottom-full mb-2 hidden group-hover:block bg-zinc-800 text-white text-[10px] p-2 rounded shadow-xl z-50 whitespace-nowrap">
                        Ganhou: {{ number_format($m['earned'], 0) }}€<br>
                        Gastou: {{ number_format($m['spent'], 0) }}€
                    </div>

                    <div class="flex items-end gap-0.5 w-full h-full">
                        <div class="flex-1 bg-emerald-500/30 group-hover:bg-emerald-500 transition-all rounded-t-sm" style="height: {{ max(1, $hEarned) }}%"></div>
                        <div class="flex-1 bg-zinc-300 dark:bg-zinc-700 group-hover:bg-brand-500 transition-all rounded-t-sm" style="height: {{ max(1, $hSpent) }}%"></div>
                    </div>
                    <span class="text-[9px] font-bold text-zinc-400 uppercase">{{ substr($m['month_name'], 0, 3) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- RANKING DE CATEGORIAS DO ANO -->
        <div class="space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400">Maiores Gastos por Categoria</h3>
            <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl overflow-hidden">
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($categoryRanking->take(6) as $item)
                        <div class="p-4 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full" style="background-color: {{ $item->category->color ?? '#ccc' }}"></div>
                                <span class="text-sm font-bold dark:text-white">{{ $item->category->name }}</span>
                            </div>
                            <span class="font-black text-sm dark:text-zinc-100">{{ number_format($item->total, 2) }} €</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- CURIOSIDADES / RECORDS -->
        <div class="space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400">Destaques do Ano</h3>
            <div class="grid grid-cols-1 gap-4">
                <div class="p-6 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800 rounded-2xl">
                    <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase">Mês de Ouro</p>
                    <p class="text-xl font-black text-emerald-800 dark:text-emerald-200 mt-1">{{ $bestMonth['month_name'] }}</p>
                    <p class="text-sm text-emerald-600 dark:text-emerald-500">Conseguiste poupar {{ number_format($bestMonth['balance'], 2) }} €.</p>
                </div>

                <div class="p-6 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800 rounded-2xl">
                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase">Mês mais Crítico</p>
                    <p class="text-xl font-black text-red-800 dark:text-red-200 mt-1">{{ $worstMonth['month_name'] }}</p>
                    <p class="text-sm text-red-600 dark:text-red-500">O saldo foi de {{ number_format($worstMonth['balance'], 2) }} €.</p>
                </div>
            </div>
        </div>
    </div>
</div>

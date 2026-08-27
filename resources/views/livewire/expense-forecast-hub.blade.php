<div class="space-y-10 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="absolute inset-0 bg-blue-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="chart-bar" class="w-10 h-10 text-blue-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Previsão de Despesas</h1>
                    <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Forecast IA</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Estimativa do próximo mês por categoria com intervalo de confiança</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Histórico</label>
            <select wire:model.live="lookbackMonths" class="h-10 rounded-xl border-0 bg-zinc-50 px-3 text-xs font-black uppercase tracking-widest dark:bg-zinc-950 dark:text-white">
                <option value="3">3 meses</option>
                <option value="6">6 meses</option>
                <option value="9">9 meses</option>
                <option value="12">12 meses</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-950 text-white p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400 mb-2">Previsão Próximo Mês</p>
            <h3 class="text-5xl font-black tracking-tighter italic">{{ number_format($totalPredicted, 2, ',', '.') }}€</h3>
            <p class="text-[10px] text-zinc-500 mt-2 uppercase font-bold">base: {{ $months }} meses</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-2">Último Mês Fechado</p>
            <h3 class="text-4xl font-black dark:text-white tracking-tighter italic">{{ number_format($totalLast, 2, ',', '.') }}€</h3>
            <p class="text-[10px] text-zinc-400 mt-2 uppercase font-bold">referência mensal</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-2">Variação Prevista</p>
            <h3 class="text-4xl font-black tracking-tighter italic {{ $deltaTotalPct >= 0 ? 'text-red-500' : 'text-emerald-500' }}">
                {{ $deltaTotalPct >= 0 ? '+' : '' }}{{ number_format($deltaTotalPct, 1, ',', '.') }}%
            </h3>
            <p class="text-[10px] text-zinc-400 mt-2 uppercase font-bold">vs último mês</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500 mb-4">Categorias com maior subida prevista</h3>
            <div class="space-y-2">
                @forelse($topRisers as $row)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                        <span class="text-sm font-bold dark:text-white">{{ $row['category']->name }}</span>
                        <span class="text-sm font-black text-red-500">+{{ number_format($row['deltaVsLast'], 2, ',', '.') }}€</span>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400">Sem dados suficientes.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500 mb-4">Categorias com maior queda prevista</h3>
            <div class="space-y-2">
                @forelse($topDrops as $row)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                        <span class="text-sm font-bold dark:text-white">{{ $row['category']->name }}</span>
                        <span class="text-sm font-black text-emerald-500">{{ number_format($row['deltaVsLast'], 2, ',', '.') }}€</span>
                    </div>
                @empty
                    <p class="text-xs text-zinc-400">Sem dados suficientes.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Previsão por Categoria</h3>
            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">janela: {{ $months }} meses</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                        <th class="p-4">Categoria</th>
                        <th class="p-4 text-right">Último Mês</th>
                        <th class="p-4 text-right">Média Histórica</th>
                        <th class="p-4 text-right">Previsão</th>
                        <th class="p-4 text-right">Intervalo</th>
                        <th class="p-4 text-right">Confiança</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($forecastRows as $row)
                        <tr class="hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition-colors">
                            <td class="p-4">
                                <p class="text-sm font-black dark:text-white">{{ $row['category']->name }}</p>
                                <p class="text-[10px] font-bold uppercase text-zinc-400 mt-1">
                                    tendência {{ $row['trend'] >= 0 ? 'ascendente' : 'descendente' }}
                                </p>
                            </td>
                            <td class="p-4 text-right text-sm font-black text-zinc-500 tabular-nums">{{ number_format($row['last'], 2, ',', '.') }}€</td>
                            <td class="p-4 text-right text-sm font-black text-zinc-500 tabular-nums">{{ number_format($row['average'], 2, ',', '.') }}€</td>
                            <td class="p-4 text-right text-sm font-black {{ $row['deltaVsLast'] >= 0 ? 'text-red-500' : 'text-emerald-500' }} tabular-nums">{{ number_format($row['predicted'], 2, ',', '.') }}€</td>
                            <td class="p-4 text-right text-xs font-bold text-zinc-400 tabular-nums">{{ number_format($row['minBand'], 2, ',', '.') }}€ - {{ number_format($row['maxBand'], 2, ',', '.') }}€</td>
                            <td class="p-4 text-right">
                                <span class="inline-flex px-2 py-1 rounded-lg bg-blue-500/10 text-blue-600 text-[10px] font-black">
                                    {{ number_format($row['confidence'], 0, ',', '.') }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <flux:icon name="chart-bar" class="size-10 text-zinc-300" />
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Sem dados suficientes para previsão</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

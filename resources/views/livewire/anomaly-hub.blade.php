<div class="space-y-10 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="absolute inset-0 bg-amber-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="exclamation-triangle" class="w-10 h-10 text-amber-500" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Anomalias de Gastos</h1>
                    <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Radar IA</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Deteção automática de despesas fora do teu padrão histórico</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Sensibilidade</label>
            <input type="range" min="2" max="4.5" step="0.1" wire:model.live="sensitivity" class="w-32" />
            <span class="text-xs font-black text-amber-600 w-10 text-right">{{ number_format($sensitivity, 1, ',', '.') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-950 text-white p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-400 mb-2">Anomalias Detetadas</p>
            <h3 class="text-5xl font-black tracking-tighter italic">{{ $anomalies->count() }}</h3>
            <p class="text-[10px] text-zinc-500 mt-2 uppercase font-bold">mês atual</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-2">Total Mês Atual</p>
            <h3 class="text-4xl font-black dark:text-white tracking-tighter italic">{{ number_format($currentTotal, 2, ',', '.') }}€</h3>
            <p class="text-[10px] text-zinc-400 mt-2 uppercase font-bold">com base em todas as despesas</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-2">Vs Média 3 Meses</p>
            <h3 class="text-4xl font-black tracking-tighter italic {{ $monthlySpikePct > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                {{ $monthlySpikePct > 0 ? '+' : '' }}{{ number_format($monthlySpikePct, 1, ',', '.') }}%
            </h3>
            <p class="text-[10px] text-zinc-400 mt-2 uppercase font-bold">referência: {{ number_format($baselineTotal, 2, ',', '.') }}€</p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Lista de Alertas Inteligentes</h3>
            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Lookback {{ $lookbackMonths }} meses</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                        <th class="p-4">Data</th>
                        <th class="p-4">Descrição</th>
                        <th class="p-4">Categoria</th>
                        <th class="p-4 text-right">Valor</th>
                        <th class="p-4 text-right">Média Base</th>
                        <th class="p-4 text-right">Desvio</th>
                        <th class="p-4 text-right">Score IA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($anomalies as $row)
                        <tr class="hover:bg-amber-50/40 dark:hover:bg-amber-900/10 transition-colors">
                            <td class="p-4 text-xs font-bold text-zinc-500">{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                            <td class="p-4 text-sm font-bold dark:text-white">{{ $row['description'] ?: 'Sem descrição' }}</td>
                            <td class="p-4 text-xs font-black uppercase text-zinc-400">{{ $row['category'] }}</td>
                            <td class="p-4 text-right text-sm font-black text-red-500 tabular-nums">{{ number_format($row['amount'], 2, ',', '.') }}€</td>
                            <td class="p-4 text-right text-sm font-black text-zinc-500 tabular-nums">{{ number_format($row['median'], 2, ',', '.') }}€</td>
                            <td class="p-4 text-right text-sm font-black text-amber-600 tabular-nums">+{{ number_format($row['delta'], 2, ',', '.') }}€</td>
                            <td class="p-4 text-right">
                                <span class="inline-flex px-2 py-1 rounded-lg bg-amber-500/10 text-amber-600 text-[10px] font-black">
                                    {{ number_format($row['score'], 1, ',', '.') }}σ
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <flux:icon name="check-circle" class="size-10 text-emerald-400" />
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Sem anomalias relevantes este mês</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

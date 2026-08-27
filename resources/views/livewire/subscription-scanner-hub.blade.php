<div class="space-y-10 pb-24">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="absolute inset-0 bg-violet-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="magnifying-glass" class="w-10 h-10 text-violet-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Scanner de Subscrições</h1>
                    <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Deteção IA</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Identifica cobranças recorrentes nos teus gastos ainda não registadas em Assinaturas</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Histórico</label>
            <select wire:model.live="lookbackMonths" class="h-10 rounded-xl border-0 bg-zinc-50 px-3 text-xs font-black uppercase tracking-widest dark:bg-zinc-950 dark:text-white">
                <option value="4">4 meses</option>
                <option value="6">6 meses</option>
                <option value="8">8 meses</option>
                <option value="12">12 meses</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-zinc-950 text-white p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-violet-400 mb-2">Subscrições Sugeridas</p>
            <h3 class="text-5xl font-black tracking-tighter italic">{{ $totalDetected }}</h3>
            <p class="text-[10px] text-zinc-500 mt-2 uppercase font-bold">não registadas no hub</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-2">Impacto Estimado</p>
            <h3 class="text-5xl font-black tracking-tighter italic text-red-500">{{ number_format($totalEstimatedMonthly, 2, ',', '.') }}€</h3>
            <p class="text-[10px] text-zinc-400 mt-2 uppercase font-bold">mensal potencial</p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Sugestões Inteligentes</h3>
        </div>

        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
            @forelse($suggestions as $s)
                <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-5 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition-colors">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <p class="text-lg font-black dark:text-white">{{ $s['merchant'] }}</p>
                            <span class="inline-flex px-2 py-1 rounded-lg bg-violet-500/10 text-violet-600 text-[10px] font-black uppercase tracking-wider">
                                Confiança {{ $s['confidence'] }}%
                            </span>
                        </div>
                        <p class="text-[11px] font-bold text-zinc-500 uppercase tracking-widest">
                            {{ $s['occurrences'] }} cobranças · {{ $s['months'] }} meses · dia provável {{ $s['billing_day'] }}
                        </p>
                        <p class="text-xs text-zinc-400">Última ocorrência: {{ $s['last_seen']->format('d/m/Y') }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-2xl font-black text-red-500 tabular-nums">{{ number_format($s['avg_amount'], 2, ',', '.') }}€</p>
                            <p class="text-[10px] font-black uppercase text-zinc-400">valor médio</p>
                        </div>
                        <flux:button
                            wire:click="createSubscription('{{ $s['signature'] }}')"
                            variant="primary"
                            class="rounded-2xl px-5 h-11 font-black uppercase text-[10px] tracking-widest !bg-violet-600 hover:!bg-violet-500 border-none shadow-lg shadow-violet-500/20"
                        >
                            Criar Assinatura
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="p-20 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <flux:icon name="check-circle" class="size-12 text-emerald-400" />
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Nenhuma subscrição esquecida detetada</p>
                        <p class="text-xs text-zinc-500 max-w-md">Continua a importar extratos para melhorar as deteções automáticas.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

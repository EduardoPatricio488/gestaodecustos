<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tight">Agenda Financeira</h1>
            <p class="text-sm text-zinc-500 font-medium italic">Projeção e histórico de fluxos</p>
        </div>

        <div class="flex items-center gap-4 bg-white dark:bg-zinc-900 p-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:button wire:click="prevMonth" variant="ghost" icon="chevron-left" size="sm" />
            <span class="text-sm font-black uppercase dark:text-white min-w-[120px] text-center">{{ $monthName }}</span>
            <flux:button wire:click="nextMonth" variant="ghost" icon="chevron-right" size="sm" />
        </div>
    </div>

    <!-- CALENDÁRIO GRID -->
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] overflow-hidden shadow-xl">
        <div class="grid grid-cols-7 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
            @foreach(['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'] as $dayName)
                <div class="py-3 text-center text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ $dayName }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 divide-x divide-y divide-zinc-100 dark:divide-zinc-800 border-l border-t border-zinc-100 dark:border-zinc-800">
            @foreach($days as $day)
                <div class="min-h-[110px] p-2 transition-colors {{ $day['is_current_month'] ? 'bg-white dark:bg-zinc-900' : 'bg-zinc-50/50 dark:bg-zinc-950/30' }} hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-bold {{ $day['date']->isToday() ? 'bg-brand-500 text-white w-6 h-6 flex items-center justify-center rounded-full' : 'text-zinc-400' }}">
                            {{ $day['date']->day }}
                        </span>

                        @if($day['total_in'] > 0)
                            <span class="text-[9px] font-black text-emerald-600">+{{ number_format($day['total_in'], 0) }}€</span>
                        @endif
                    </div>

                    <div class="mt-2 space-y-1">
                        <!-- RENDIMENTOS FIXOS (SALÁRIO) -->
                        @foreach($day['fixedIncomes'] as $rec)
                            <div class="px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[9px] font-black rounded uppercase truncate" title="Rendimento: {{ $rec->description }}">
                                💰 {{ $rec->description }}
                            </div>
                        @endforeach

                        <!-- ASSINATURAS (CONTAS FIXAS) -->
                        @foreach($day['subscriptions'] as $sub)
                            <div class="px-1.5 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[9px] font-black rounded uppercase truncate" title="Conta: {{ $sub->name }}">
                                💳 {{ $sub->name }}
                            </div>
                        @endforeach

                        <!-- GASTOS REAIS (INDICADOR) -->
                        @if(count($day['expenses']) > 0)
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($day['expenses'] as $exp)
                                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-600" title="{{ $exp->amount }}€ - {{ $exp->description }}"></div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- LEGENDA RÁPIDA -->
    <div class="flex gap-6 justify-center text-[10px] font-bold uppercase tracking-widest text-zinc-500">
        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-emerald-100 dark:bg-emerald-900/30 rounded border border-emerald-200 dark:border-emerald-800"></div> Rendimentos</div>
        <div class="flex items-center gap-2"><div class="w-3 h-3 bg-red-100 dark:bg-red-900/30 rounded border border-red-200 dark:border-red-800"></div> Contas Fixas</div>
        <div class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-zinc-400 rounded-full"></div> Gastos Únicos</div>
    </div>
</div>

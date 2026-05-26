<div class="space-y-10 pb-20">
    {{-- 1. HEADER SaaS PREMIUM --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="calendar-days" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Calendário Global</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Operações 360º</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Controlo sincronizado de <span class="text-brand-600 font-bold uppercase">RH e Fluxo de Caixa</span></p>
                </div>
            </div>

            {{-- NAVEGAÇÃO DE MESES --}}
            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-xl">
                <flux:button wire:click="previousMonth" variant="ghost" icon="chevron-left" class="rounded-2xl hover:bg-zinc-100 dark:hover:bg-zinc-800" />
                <div class="min-w-[160px] text-center">
                    <span class="text-sm font-black uppercase tracking-[0.2em] text-zinc-800 dark:text-zinc-200">{{ $monthName }}</span>
                </div>
                <flux:button wire:click="nextMonth" variant="ghost" icon="chevron-right" class="rounded-2xl hover:bg-zinc-100 dark:hover:bg-zinc-800" />
            </div>
        </div>
    </div>

    {{-- 2. LEGENDA ESTRATÉGICA --}}
    <div class="flex flex-wrap items-center gap-3 px-2">
        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
            <div class="size-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Receitas</span>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-red-500/5 border border-red-500/10 rounded-xl">
            <div class="size-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]"></div>
            <span class="text-[10px] font-black uppercase tracking-widest text-red-700 dark:text-red-400">Despesas</span>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-brand-500/5 border border-brand-500/10 rounded-xl">
            <div class="size-2 rounded-full bg-brand-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
            <span class="text-[10px] font-black uppercase tracking-widest text-brand-700 dark:text-brand-400">Férias & Ausências</span>
        </div>
    </div>

    {{-- 3. GRELHA DE CALENDÁRIO COM PRIVACIDADE --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-2xl group">
        <div class="grid grid-cols-7 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 text-center py-5">
            @foreach(['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'] as $dayName)
                <span class="text-[10px] font-black uppercase tracking-[0.25em] text-zinc-400 dark:text-zinc-500">
                    <span class="hidden md:inline">{{ $dayName }}</span>
                    <span class="md:hidden">{{ substr($dayName, 0, 3) }}</span>
                </span>
            @endforeach
        </div>

        <div class="grid grid-cols-7 auto-rows-[130px] md:auto-rows-[180px]">
            @foreach($days as $day)
                <div class="relative p-3 border-r border-b border-zinc-100 dark:border-zinc-800 last:border-r-0 group/day transition-all duration-300
                    {{ !$day['isCurrentMonth'] ? 'bg-zinc-50/30 dark:bg-zinc-950/20' : 'bg-white dark:bg-zinc-900' }}
                    {{ $day['isToday'] ? 'ring-2 ring-inset ring-brand-500/20' : '' }}
                    hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40">

                    @if($day['isToday'])
                        <div class="absolute top-0 left-0 w-full h-1 bg-brand-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                    @endif

                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-flex size-8 items-center justify-center rounded-xl text-xs font-black transition-all
                            {{ $day['isToday'] ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : ($day['isCurrentMonth'] ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-300 dark:text-zinc-700') }}">
                            {{ $day['date']->day }}
                        </span>
                    </div>

                    {{-- Lista de Eventos --}}
                    <div class="space-y-1.5 overflow-y-auto max-h-[70%] custom-scrollbar pr-1">
                        @foreach($day['events'] as $event)
                            <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-tighter border transition-all
                                {{ $event['type'] === 'absence' ? 'bg-brand-50 text-brand-700 border-brand-100 dark:bg-brand-900/20 dark:text-brand-400 dark:border-brand-800/50' : '' }}
                                {{ $event['type'] === 'expense' ? 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/50' : '' }}
                                {{ $event['type'] === 'income' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/50' : '' }}">

                                <flux:icon :name="$event['icon']" variant="mini" class="size-3 opacity-70" />

                                {{-- APLICAÇÃO DO BLUR NOS VALORES FINANCEIROS --}}
                                <span :class="privacyMode && ('{{ $event['type'] }}' === 'expense' || '{{ $event['type'] }}' === 'income') ? 'blur-sm select-none' : ''"
                                      class="transition-all duration-500 truncate">
                                    {{ $event['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if(!$day['isCurrentMonth'])
                        <div class="absolute inset-0 bg-white/40 dark:bg-zinc-950/40 pointer-events-none"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    {{-- 4. RODAPÉ DO CALENDÁRIO (ESTILO SaaS REPORT COM PRIVACIDADE) --}}
    <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 mt-10 flex flex-col md:flex-row justify-between items-center gap-4 opacity-70">
        <div class="flex items-center gap-4">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
                © {{ date('Y') }} {{ auth()->user()->currentWorkspace->name }} · Calendário Estratégico
            </p>
            <div class="h-4 w-px bg-zinc-200 dark:bg-zinc-800 hidden md:block"></div>
            <div class="flex items-center gap-2">
                <div class="size-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Sincronização em Tempo Real</span>
            </div>
        </div>

        <div class="flex gap-4">
             <flux:button variant="ghost" size="xs" class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Imprimir Relatório</flux:button>
             <flux:button variant="ghost" size="xs" class="text-[9px] font-black uppercase tracking-widest text-zinc-500">Exportar iCal</flux:button>
        </div>
    </div>

    {{-- 5. ESTILOS DE INTERFACE (SCROLLBARS & ANIMAÇÕES) --}}
    <style>
        /* Barra de scroll personalizada e discreta para os eventos diários */
        .custom-scrollbar::-webkit-scrollbar {
            width: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e4e4e7; /* zinc-200 */
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #27272a; /* zinc-800 */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #3b82f6; /* brand-500 */
        }

        /* Suavizar a entrada do calendário ao trocar de mês */
        .glass-card {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Utilitários Globais */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>

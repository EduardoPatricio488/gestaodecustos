<div
    class="space-y-8 pb-24 px-4 sm:px-6 lg:px-8"
    x-data="{
        filterOpen: false,
        quickAddModal: false,
        selectedDate: '',
        formattedDate: ''
    }"
>
    {{-- ── 1. HEADER & CONTROLO DE NAVEGAÇÃO ── --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-indigo-500/20 blur-2xl rounded-full group-hover:bg-indigo-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-indigo-500/10">
                    <flux:icon name="calendar-days" class="w-10 h-10 text-indigo-600" />
                </div>
            </div>

            <div>
                <h1 class="text-3xl sm:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                    Terminal <span class="text-zinc-400 dark:text-zinc-600">Cronológico</span>
                </h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Sincronização global de fluxos financeiros e atividades</p>
            </div>
        </div>

        {{-- Controlos de Meses + Botão Filtro --}}
        <div class="flex items-center gap-3">
            <div class="flex items-center bg-white dark:bg-zinc-900 p-1.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <button wire:click="prevMonth" class="p-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-indigo-600 transition-all">
                    <flux:icon name="chevron-left" class="size-4" />
                </button>
                <div class="px-6 text-center min-w-[150px]">
                    <span class="text-sm font-black uppercase tracking-tight dark:text-white">{{ $currentMonthName }} {{ $year }}</span>
                </div>
                <button wire:click="nextMonth" class="p-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-indigo-600 transition-all">
                    <flux:icon name="chevron-right" class="size-4" />
                </button>
            </div>

            {{-- Botão Filtros --}}
            <button @click="filterOpen = !filterOpen" :class="filterOpen ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-zinc-900 text-zinc-500'" class="p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:scale-105">
                <flux:icon name="adjustments-horizontal" class="size-5" />
            </button>
        </div>
    </div>

    {{-- ── 2. BARRA DE FILTROS (COLLAPSIBLE) ── --}}
    <div x-show="filterOpen" x-collapse x-cloak>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 flex flex-wrap gap-4 shadow-sm mb-6">
            @foreach([
                'incomes' => ['label' => 'Receitas', 'color' => 'emerald'],
                'expenses' => ['label' => 'Despesas', 'color' => 'red'],
                'fitness' => ['label' => 'Treino', 'color' => 'orange'],
                'reminders' => ['label' => 'Lembretes', 'color' => 'indigo']
            ] as $key => $filter)
                <button
                    {{-- wire:click="toggleFilter('{{ $key }}')" --}}
                    class="flex items-center gap-3 px-5 py-2.5 rounded-2xl border-2 transition-all border-zinc-100 dark:border-zinc-800 hover:border-{{ $filter['color'] }}-500/50 group"
                >
                    <div class="size-3 rounded-full bg-{{ $filter['color'] }}-500 shadow-[0_0_8px_rgba(var(--{{ $filter['color'] }}-500),0.4)]"></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-zinc-500 group-hover:text-zinc-800 dark:group-hover:text-white">{{ $filter['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── 3. GRELHA DO CALENDÁRIO OTIMIZADA ── --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] shadow-xl overflow-hidden">

        {{-- Dias da Semana --}}
        <div class="grid grid-cols-7 bg-zinc-50/50 dark:bg-zinc-950/50 border-b border-zinc-100 dark:border-zinc-800">
            @foreach(['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'] as $dayName)
                <div class="py-4 text-center">
                    <span class="hidden sm:inline text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">{{ $dayName }}</span>
                    <span class="sm:hidden text-[10px] font-black uppercase text-zinc-400">{{ substr($dayName, 0, 1) }}</span>
                </div>
            @endforeach
        </div>

        {{-- Corpo do Calendário --}}
        <div class="grid grid-cols-7">
            @for($i = 0; $i < $paddingDays; $i++)
                <div class="min-h-[100px] sm:min-h-[140px] border-r border-b border-zinc-50 dark:border-zinc-800/40 bg-zinc-50/20 dark:bg-zinc-950/10"></div>
            @endfor

            @for($day = 1; $day <= $totalDays; $day++)
                @php
                    $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $events = $this->dayEvents->get($dateKey, collect());
                    $isToday = $dateKey === now()->format('Y-m-d');

                    $dailyIncome = $events->where('type', 'income')->sum('amount');
                    $dailyExpense = $events->where('type', 'expense')->sum('amount');
                @endphp

                <div
                    {{-- wire:click="openDayDetail('{{ $dateKey }}')" --}}
                    class="relative min-h-[110px] sm:min-h-[150px] border-r border-b border-zinc-100 dark:border-zinc-800 p-3 transition-all hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 group/day cursor-pointer"
                >
                    {{-- Cabeçalho da Célula --}}
                    <div class="flex justify-between items-start mb-2">
                        <span @class([
                            'text-sm font-black transition-all group-hover/day:scale-110',
                            'text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 size-8 flex items-center justify-center rounded-xl' => $isToday,
                            'text-zinc-400 dark:text-zinc-600' => !$isToday
                        ])>
                            {{ sprintf('%02d', $day) }}
                        </span>

                        {{-- Resumo Rápido (Valores) --}}
                        <div class="flex flex-col items-end gap-1 opacity-0 group-hover/day:opacity-100 transition-opacity">
                            @if($dailyIncome > 0)
                                <span class="text-[8px] font-black text-emerald-600">+{{ number_format($dailyIncome, 0) }}€</span>
                            @endif
                            @if($dailyExpense > 0)
                                <span class="text-[8px] font-black text-red-500">-{{ number_format($dailyExpense, 0) }}€</span>
                            @endif
                        </div>
                    </div>

                    {{-- Lista de Eventos Maiores --}}
                    <div class="space-y-1.5 overflow-y-auto max-h-[80px] custom-scrollbar pr-1">
                        @foreach($events as $event)
                            <div
                                title="{{ $event['label'] }}"
                                class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 shadow-sm hover:border-indigo-500/50 transition-colors"
                            >
                                <div class="size-2 rounded-full {{ str_replace('text', 'bg', $event['color']) }} shadow-sm"></div>
                                <span class="text-[9px] font-black text-zinc-600 dark:text-zinc-300 uppercase truncate leading-none">
                                    {{ $event['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Quick Add (+) --}}
                   <div
    class="absolute bottom-2 right-2 opacity-0 group-hover/day:opacity-100 transition-all scale-75 group-hover/day:scale-100"
    @click.stop="
        selectedDate = '{{ $dateKey }}';
        formattedDate = '{{ sprintf('%02d', $day) }} de {{ $currentMonthName }}';
        quickAddModal = true;
    "
>
    <div class="p-2 bg-indigo-600 text-white rounded-xl shadow-lg hover:bg-indigo-500 cursor-pointer transition-colors">
        <flux:icon name="plus" variant="micro" class="size-4" />
    </div>
</div>
                </div>
            @endfor

            {{-- Padding Final --}}
            @php $remaining = (7 - (($paddingDays + $totalDays) % 7)) % 7; @endphp
            @for($i = 0; $i < $remaining; $i++)
                <div class="min-h-[100px] sm:min-h-[140px] border-r border-b border-zinc-50 dark:border-zinc-800/40 bg-zinc-50/20 dark:bg-zinc-950/10"></div>
            @endfor
        </div>
    </div>

    {{-- ── 4. INSIGHTS DO MÊS (ADD-ON INTERESSANTE) ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <h4 class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mb-4">Pico de Atividade</h4>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-500/10 rounded-2xl text-red-500">
                    <flux:icon name="fire" class="size-6" />
                </div>
                <div>
                    <p class="text-xl font-black dark:text-white leading-none">Dia 15</p>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase mt-1">Maior volume de registos</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <h4 class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mb-4">Consistência Fitness</h4>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-500/10 rounded-2xl text-orange-500">
                    <flux:icon name="bolt" class="size-6" />
                </div>
                <div>
                    <p class="text-xl font-black dark:text-white leading-none">12 Treinos</p>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase mt-1">42% do mês ativo</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <h4 class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mb-4">Eficiência de Lembretes</h4>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-500/10 rounded-2xl text-indigo-500">
                    <flux:icon name="check-circle" class="size-6" />
                </div>
                <div>
                    <p class="text-xl font-black dark:text-white leading-none">94%</p>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase mt-1">Taxa de conclusão pontual</p>
                </div>
            </div>
        </div>
    </div>
{{-- ── MODAL: COMANDO RÁPIDO DE REGISTO (CENTRADO) ── --}}
    <div
        x-show="quickAddModal"
        x-cloak
        {{-- fixed inset-0 garante que cobre o ecrã todo. z-[100] coloca-o acima de tudo --}}
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        {{-- Overlay de Fundo (Backdrop) --}}
        <div
            class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md"
            @click="quickAddModal = false"
        ></div>

        {{-- Conteúdo do Modal --}}
        <div
            x-show="quickAddModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative w-full max-w-lg bg-white dark:bg-zinc-900 rounded-[3rem] p-8 shadow-2xl border border-zinc-200 dark:border-zinc-800"
        >
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black uppercase italic tracking-tighter dark:text-white">Agendar Registo</h2>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">
                    Selecionaste o dia <span class="text-indigo-600" x-text="formattedDate"></span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- OPÇÃO: DESPESA --}}
                <button @click="window.location.href='/expenses/create?date=' + selectedDate" class="group flex flex-col items-center p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-red-500/50 transition-all">
                    <div class="size-12 bg-red-500/10 rounded-2xl flex items-center justify-center text-red-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="minus-circle" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Despesa</span>
                </button>

                {{-- OPÇÃO: RECEITA --}}
                <button @click="window.location.href='/receitas?date=' + selectedDate" class="group flex flex-col items-center p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-emerald-500/50 transition-all">
                    <div class="size-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="plus-circle" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Receita</span>
                </button>

                {{-- OPÇÃO: TREINO --}}
                <button @click="window.location.href='/fitness?date=' + selectedDate" class="group flex flex-col items-center p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-orange-500/50 transition-all">
                    <div class="size-12 bg-orange-500/10 rounded-2xl flex items-center justify-center text-orange-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="bolt" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Treino</span>
                </button>

                {{-- OPÇÃO: LEMBRETE --}}
                <button @click="window.location.href='/lembretes?date=' + selectedDate" class="group flex flex-col items-center p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-indigo-500/50 transition-all">
                    <div class="size-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="clock" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Lembrete</span>
                </button>
            </div>

            <button @click="quickAddModal = false" class="w-full mt-8 py-4 text-[10px] font-black uppercase text-zinc-400 hover:text-zinc-600 transition-colors">
                Fechar Janela
            </button>
        </div>
    </div>
</div>

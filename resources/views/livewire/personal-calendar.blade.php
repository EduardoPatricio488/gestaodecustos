@php
    $currentTime = now();
    $pendingFromDate = $currentTime->format('H:i:s') < '18:00:00'
        ? $currentTime->toDateString()
        : $currentTime->copy()->addDay()->toDateString();
@endphp
<div
    class="space-y-8 pb-4 px-4 sm:px-6 lg:px-8"
    data-calendar-refresh-delay="{{ $calendarRefreshDelay }}"
    x-data="{
        filterOpen: false,
        quickAddModal: false,
        selectedDate: '',
        formattedDate: '',
        calendarClockTimer: null,
        calendarClockStopped: false,
        init() {
            this.scheduleCalendarRefresh(Number(this.$el.dataset.calendarRefreshDelay));
        },
        scheduleCalendarRefresh(delay) {
            if (this.calendarClockStopped) return;
            this.calendarClockTimer = setTimeout(async () => {
                try {
                    this.scheduleCalendarRefresh(await this.$wire.refreshCalendarClock());
                } catch (error) {
                    console.error('Não foi possível atualizar o calendário automaticamente.', error);
                    this.$dispatch('toast', { variant: 'error', text: 'Não foi possível atualizar o calendário automaticamente. Atualiza a página.' });
                }
            }, delay);
        },
        destroy() {
            this.calendarClockStopped = true;
            clearTimeout(this.calendarClockTimer);
        }
    }"
>
    {{-- ── 1. HEADER & CONTROLO DE NAVEGAÇÃO ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex min-w-0 basis-full items-center gap-4 xl:min-w-[220px] xl:flex-1 xl:basis-0">
            <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl border border-zinc-200 bg-white text-indigo-600 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <flux:icon name="calendar-days" class="size-7" />
            </div>

            <div class="min-w-0">
                <h1 class="text-2xl font-black uppercase italic leading-none tracking-tighter sm:text-3xl dark:text-white">
                    Terminal <span class="text-zinc-400 dark:text-zinc-600">Cronológico</span>
                </h1>
                <p class="mt-1 text-xs font-medium text-zinc-500 sm:text-sm">Sincronização global de fluxos financeiros e atividades</p>
            </div>
        </div>

        <div class="ml-auto flex max-w-full flex-wrap items-start justify-end gap-3">
            <button type="button" aria-label="Filtros" :aria-expanded="filterOpen.toString()" @click="filterOpen = !filterOpen" :class="filterOpen ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-zinc-900 text-zinc-500'" class="inline-flex size-11 shrink-0 items-center justify-center rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:scale-105">
                <flux:icon name="adjustments-horizontal" class="size-5" />
            </button>

            <div class="flex flex-wrap justify-end gap-2">
                <button type="button" wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf" title="Exporta todos os registos do mês, mesmo com filtros ativos" class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white px-4 text-xs font-black uppercase tracking-wider text-zinc-600 shadow-sm transition hover:bg-zinc-50 disabled:opacity-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">
                    <flux:icon name="arrow-down-tray" class="size-4" />
                    Exportar PDF
                </button>
                <button
                    type="button"
                    wire:click="openVacationModal"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 text-xs font-black uppercase tracking-wider text-emerald-700 shadow-sm transition hover:bg-emerald-100 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300 dark:hover:bg-emerald-950/50"
                >
                    <flux:icon name="sun" class="size-4 shrink-0" />
                    Registar férias
                </button>
            </div>
        </div>
    </div>

    {{-- ── RESUMO POR DATA ── --}}
    <div x-data="{ summaryDateOpen: false }" class="space-y-3">
        <button type="button" aria-label="Escolher data dos totais: {{ \Carbon\Carbon::parse($monthSummary['cutoff'])->format('d/m/Y') }}" aria-controls="calendar-summary-date" :aria-expanded="summaryDateOpen.toString()" @click="summaryDateOpen = !summaryDateOpen" class="group inline-flex max-w-full items-center gap-3 rounded-2xl border border-indigo-100 bg-white px-3 py-2 text-left shadow-sm transition hover:border-indigo-300 hover:shadow-md dark:border-indigo-900/50 dark:bg-zinc-900 dark:hover:border-indigo-700">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400">
                <flux:icon name="calendar-days" class="size-5" />
            </span>
            <span class="text-lg font-black tabular-nums tracking-tight text-zinc-800 dark:text-white">{{ \Carbon\Carbon::parse($monthSummary['cutoff'])->format('d/m/Y') }}</span>
            <flux:icon name="chevron-down" class="size-4 shrink-0 text-indigo-500 transition-transform" x-bind:class="{ 'rotate-180': summaryDateOpen }" />
        </button>
        <div id="calendar-summary-date" x-show="summaryDateOpen" x-collapse x-cloak>
            <form wire:submit="applySummaryDate" class="inline-block max-w-full space-y-3 rounded-2xl bg-white p-4 dark:bg-zinc-800/60">
                <p class="text-base font-medium text-zinc-600 dark:text-zinc-400">Escolhe a data para ver os registos desde o início desse mês.</p>
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="summary-day" class="mb-1 block text-xs font-bold text-zinc-600 dark:text-zinc-300">Dia</label>
                        <select id="summary-day" wire:model="summaryDayInput" wire:change="applySummaryDate" class="h-10 rounded-xl border border-zinc-200 bg-white px-3 text-sm text-zinc-800 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            @for($day = 1; $day <= 31; $day++)
                                <option value="{{ $day }}">{{ sprintf('%02d', $day) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="summary-month" class="mb-1 block text-xs font-bold text-zinc-600 dark:text-zinc-300">Mês</label>
                        <select id="summary-month" wire:model="summaryMonthInput" wire:change="applySummaryDate" class="h-10 rounded-xl border border-zinc-200 bg-white px-3 text-sm text-zinc-800 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            @foreach(['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'] as $index => $monthName)
                                <option value="{{ $index + 1 }}">{{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="summary-year" class="mb-1 block text-xs font-bold text-zinc-600 dark:text-zinc-300">Ano</label>
                        <input id="summary-year" type="number" min="1" max="9999" step="1" wire:model="summaryYearInput" wire:change="applySummaryDate" class="h-10 w-24 rounded-xl border border-zinc-200 bg-white px-3 text-sm text-zinc-800 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    </div>
                    <button type="button" wire:click="resetSummaryDate" @click="summaryDateOpen = false" class="h-10 rounded-xl border border-zinc-200 px-4 text-xs font-bold text-indigo-600 transition hover:bg-indigo-50 dark:border-zinc-700 dark:text-indigo-400 dark:hover:bg-zinc-800">Voltar a hoje</button>
                </div>
                @foreach(['summaryDayInput', 'summaryMonthInput', 'summaryYearInput'] as $field)
                    @error($field)
                        <p role="alert" class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                @endforeach
            </form>
        </div>
        @if($monthSummary['includes_forecast'])
            <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Inclui valores previstos até à data selecionada.</p>
        @elseif($monthSummary['elapsed_days'] === 0)
            <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">O mês selecionado ainda não começou.</p>
        @endif
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">{{ $monthSummary['is_today'] ? 'Entradas até hoje' : 'Entradas até à data' }}</p>
                <p class="text-2xl font-black text-emerald-600 italic tabular-nums">{{ number_format($monthSummary['income'], 2, ',', '.') }}€</p>
                @if($monthSummary['is_today'])
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Entradas de hoje contam a partir das 18h.</p>
                @endif
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">{{ $monthSummary['is_today'] ? 'Saídas até hoje' : 'Saídas até à data' }}</p>
                <p class="text-2xl font-black text-red-500 italic tabular-nums">{{ number_format($monthSummary['outflow'], 2, ',', '.') }}€</p>
                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Despesas, assinaturas e parcelas incluídas.</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">{{ $monthSummary['is_today'] ? 'Total até hoje' : 'Total até à data' }}</p>
                <p class="text-2xl font-black italic tabular-nums {{ $monthSummary['net_total'] < 0 ? 'text-red-500' : 'text-emerald-600' }}">{{ number_format($monthSummary['net_total'], 2, ',', '.') }}€</p>
                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Entradas menos saídas do mês</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Média diária do balanço</p>
                @if($monthSummary['daily_average'] !== null)
                    <p class="text-2xl font-black italic tabular-nums {{ $monthSummary['daily_average'] < 0 ? 'text-red-500' : ($monthSummary['daily_average'] > 0 ? 'text-emerald-600' : 'text-zinc-500') }}">{{ number_format($monthSummary['daily_average'], 2, ',', '.') }}€</p>
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Entradas menos saídas ÷ {{ $monthSummary['elapsed_days'] }} {{ $monthSummary['elapsed_days'] === 1 ? 'dia' : 'dias' }} {{ $monthSummary['includes_forecast'] ? ($monthSummary['elapsed_days'] === 1 ? 'considerado' : 'considerados') : ($monthSummary['elapsed_days'] === 1 ? 'decorrido' : 'decorridos') }}</p>
                @else
                    <p class="text-2xl font-black text-zinc-400">—</p>
                    <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">Sem dias decorridos neste mês.</p>
                @endif
            </div>
        </div>
    </div>

    @php
        $calendarIndicatorColors = [
            'text-emerald-500' => 'text-emerald-600 dark:text-emerald-400',
            'text-emerald-600' => 'text-emerald-600 dark:text-emerald-400',
            'text-red-500' => 'text-red-600 dark:text-red-400',
            'text-purple-500' => 'text-purple-600 dark:text-purple-400',
            'text-blue-500' => 'text-blue-600 dark:text-blue-400',
            'text-amber-500' => 'text-amber-600 dark:text-amber-400',
            'text-orange-500' => 'text-orange-600 dark:text-orange-400',
            'text-indigo-500' => 'text-indigo-600 dark:text-indigo-400',
        ];
    @endphp
    {{-- ── 2. BARRA DE FILTROS (COLLAPSIBLE) ── --}}
    <div x-show="filterOpen" x-collapse x-cloak>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 flex flex-wrap gap-4 shadow-sm mb-6">
            <p class="w-full text-base font-medium text-zinc-500 dark:text-zinc-400">Seleciona um ou mais símbolos. Sem seleção, são mostrados todos os registos.</p>
            @php
                // Classes Tailwind escritas por extenso (não concatenadas) para o compilador conseguir detetá-las.
                $filterStyles = [
                    'emerald' => ['active' => 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400', 'idle' => 'border-zinc-100 dark:border-zinc-800 hover:border-emerald-500/50'],
                    'red' => ['active' => 'border-red-500 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400', 'idle' => 'border-zinc-100 dark:border-zinc-800 hover:border-red-500/50'],
                    'blue' => ['active' => 'border-blue-500 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400', 'idle' => 'border-zinc-100 dark:border-zinc-800 hover:border-blue-500/50'],
                    'purple' => ['active' => 'border-purple-500 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400', 'idle' => 'border-zinc-100 dark:border-zinc-800 hover:border-purple-500/50'],
                    'amber' => ['active' => 'border-amber-500 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400', 'idle' => 'border-zinc-100 dark:border-zinc-800 hover:border-amber-500/50'],
                    'orange' => ['active' => 'border-orange-500 bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400', 'idle' => 'border-zinc-100 dark:border-zinc-800 hover:border-orange-500/50'],
                    'indigo' => ['active' => 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400', 'idle' => 'border-zinc-100 dark:border-zinc-800 hover:border-indigo-500/50'],
                ];
            @endphp
            @foreach(\App\Livewire\PersonalCalendar::CALENDAR_FILTERS as $key => $filter)
                @php
                    $isActive = in_array($key, $activeFilters, true);
                    $style = $filterStyles[$filter['color']];
                @endphp
                <button
                    type="button"
                    wire:click="toggleFilter('{{ $key }}')"
                    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                    class="flex items-center gap-3 px-5 py-2.5 rounded-2xl border-2 transition-all group {{ $isActive ? $style['active'] : $style['idle'].' text-zinc-500 group-hover:text-zinc-800 dark:group-hover:text-white' }}"
                >
                    <flux:icon :name="$filter['icon']" class="size-4 shrink-0 {{ $calendarIndicatorColors['text-'.$filter['color'].'-500'] }}" />
                    <span class="text-[11px] font-black uppercase tracking-widest">{{ $filter['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── 3. GRELHA DO CALENDÁRIO OTIMIZADA ── --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] shadow-xl overflow-hidden">
        <div class="flex flex-wrap items-center justify-end gap-x-4 gap-y-2 border-b border-zinc-100 px-5 py-3 text-[9px] font-black uppercase tracking-wider text-zinc-500 dark:border-zinc-800">
            <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm border border-emerald-200 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-950/40"></span>Dias úteis</span>
            <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm border border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800"></span>Dias não úteis</span>
            <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm border border-amber-200 bg-amber-50 dark:border-amber-900 dark:bg-amber-950/40"></span>Feriados</span>
        </div>

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
                    $events = $this->visibleDayEvents->get($dateKey, collect());
                    $dayHolidays = $this->dayEvents->get($dateKey, collect())->where('subtype', 'public_holiday');
                    $isWeekend = \Carbon\Carbon::parse($dateKey)->isWeekend();
                    $dayType = $dayHolidays->isNotEmpty() ? 'holiday' : ($isWeekend ? 'non-working' : 'working');
                    $isToday = $dateKey === now()->format('Y-m-d');
                    $isPendingDay = $dateKey >= $pendingFromDate;

                    $dailyIncome = $events->whereIn('type', ['income', 'salary'])->sum('amount');
                    $dailyExpense = $events->whereIn('type', ['expense', 'subscription', 'debt'])->sum('amount');
                    $dailyTotal = $dailyIncome - $dailyExpense;
                    $hasFinancialRecords = $events->contains(
                        fn ($event) => isset($event['amount'])
                            && in_array($event['type'], ['income', 'salary', 'expense', 'subscription', 'debt'], true)
                    );
                    $eventIndicators = $events
                        ->filter(fn ($event) => !empty($event['calendar_icon']))
                        ->groupBy(fn ($event) => $event['calendar_icon'].'|'.($calendarIndicatorColors[$event['color']] ?? $event['color']))
                        ->map(function ($group) use ($calendarIndicatorColors) {
                            $indicator = $group->first();
                            $count = $group->count();
                            $labels = $group->pluck('calendar_icon_label')->filter()->unique()->implode(', ');

                            return array_merge($indicator, [
                                'calendar_icon_count' => $count,
                                'calendar_icon_label' => $count > 1 ? $labels.': '.$count.' registos' : $indicator['calendar_icon_label'],
                                'calendar_icon_color' => $calendarIndicatorColors[$indicator['color']] ?? $indicator['color'],
                            ]);
                        });
                @endphp

                <div
                    wire:click="openDayDetail('{{ $dateKey }}')"
                    data-day-type="{{ $dayType }}"
                    @if($isToday) aria-current="date" @endif
                    class="relative min-h-[110px] sm:min-h-[150px] border-r border-b border-zinc-100 dark:border-zinc-800 p-3 transition-all hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 group/day cursor-pointer {{ $dayType === 'holiday' ? 'bg-amber-50/80 dark:bg-amber-950/20' : ($dayType === 'non-working' ? 'bg-zinc-100/70 dark:bg-zinc-950/50' : 'bg-emerald-50/30 dark:bg-emerald-950/10') }}"
                >
                    {{-- Cabeçalho da Célula --}}
                    <div class="flex flex-col items-start gap-1 sm:flex-row sm:items-start sm:justify-between">
                        <div class="inline-flex items-center gap-1">
                            <span @class([
                                'font-black transition-all group-hover/day:scale-110',
                                'size-8 sm:size-9 flex items-center justify-center rounded-full bg-sky-600 text-base text-white dark:bg-sky-500' => $isToday,
                                'text-sm text-zinc-400 dark:text-zinc-600' => !$isToday
                            ])>
                                {{ sprintf('%02d', $day) }}
                            </span>
                        </div>
                        @if($hasFinancialRecords)
                            <span
                                title="{{ $isPendingDay ? 'Total previsto (pendente)' : 'Total do dia' }}"
                                class="inline-flex min-w-0 items-center self-end whitespace-nowrap rounded-lg px-1.5 py-1 text-[8px] font-black tabular-nums shadow-sm sm:self-auto {{ $isPendingDay ? 'sm:text-[9px] md:text-[10px] border border-dashed border-amber-400 bg-amber-50 text-amber-800 dark:border-amber-600 dark:bg-amber-950/40 dark:text-amber-300' : 'sm:text-[10px] md:text-xs '.($dailyTotal > 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ($dailyTotal < 0 ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300')) }}"
                            >
                                <span>{{ $isPendingDay ? 'Prev. ' : 'Total ' }}</span><span class="{{ $isPendingDay ? 'text-[8px] sm:text-[10px]' : 'text-[9px] sm:text-xs lg:text-sm' }}">{{ $dailyTotal > 0 ? '+' : ($dailyTotal < 0 ? '−' : '') }}{{ number_format(abs($dailyTotal), 2, ',', '.') }}€</span>
                            </span>
                        @endif
                    </div>

                    @if($eventIndicators->isNotEmpty())
                        <div class="mt-2 flex flex-wrap items-center gap-1">
                            @foreach($eventIndicators as $calendarEvent)
                                <span
                                    data-calendar-event-type="{{ $calendarEvent['type'] }}"
                                    data-calendar-event-count="{{ $calendarEvent['calendar_icon_count'] }}"
                                    role="img"
                                    aria-label="{{ $calendarEvent['calendar_icon_label'] }}"
                                    @class([
                                        'relative inline-flex items-center justify-center bg-white/70 dark:bg-zinc-900/50',
                                        $calendarEvent['calendar_icon_color'],
                                        'size-7 sm:size-8 rounded-lg' => $calendarEvent['calendar_icon_count'] > 1,
                                        'size-6 rounded-md' => $calendarEvent['calendar_icon_count'] === 1,
                                    ])
                                >
                                    <flux:icon
                                        name="{{ $calendarEvent['calendar_icon'] }}"
                                        class="{{ $calendarEvent['calendar_icon_count'] > 1 ? 'size-6' : 'size-5' }}"
                                    />
                                    @if($calendarEvent['calendar_icon_count'] > 1)
                                        <span class="absolute -right-1 -top-1 inline-flex min-h-4 min-w-4 items-center justify-center rounded-full bg-zinc-900 px-1 text-[8px] font-black leading-none text-white dark:bg-white dark:text-zinc-900">
                                            {{ $calendarEvent['calendar_icon_count'] }}
                                        </span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif

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

    {{-- ── 4. INDICADORES FINANCEIROS DO MÊS ── --}}
    <p class="text-sm font-bold text-zinc-500 sm:text-base dark:text-zinc-400">Previsão para todo o mês de {{ $currentMonthName }} e balanço anual</p>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 !mb-0">
        <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm dark:border-emerald-900/50 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-emerald-500/10 p-3 text-emerald-600 dark:text-emerald-400">
                    <flux:icon name="arrow-down-left" class="size-5" />
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Entradas previstas no mês</h4>
                    <p class="mt-1 text-xl font-black tabular-nums text-emerald-600 dark:text-emerald-400">{{ number_format($monthlyFinanceInsights['received'], 2, ',', '.') }} €</p>
                </div>
            </div>
            <p class="mt-3 text-[10px] font-semibold text-zinc-400">Receitas, salários, subsídio e valores a receber no mês</p>
        </div>

        <div class="rounded-3xl border border-red-100 bg-white p-5 shadow-sm dark:border-red-900/50 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-red-500/10 p-3 text-red-500">
                    <flux:icon name="arrow-up-right" class="size-5" />
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Saídas previstas no mês</h4>
                    <p class="mt-1 text-xl font-black tabular-nums text-red-500">{{ number_format($monthlyFinanceInsights['spent'], 2, ',', '.') }} €</p>
                </div>
            </div>
            <p class="mt-3 text-[10px] font-semibold text-zinc-400">Despesas, assinaturas e parcelas do mês</p>
            @if($monthlyFinanceInsights['has_unknown_subscriptions'])
                <p class="mt-1 text-[10px] font-semibold text-amber-700 dark:text-amber-400">Assinaturas sem ciclo ou data definidos não entram na previsão.</p>
            @endif
            <p class="mt-1 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400">
                Despesas registadas:
                @if($this->expenseComparison['difference'] > 0)
                    +{{ number_format($this->expenseComparison['difference'], 2, ',', '.') }} € face ao mês anterior
                @elseif($this->expenseComparison['difference'] < 0)
                    −{{ number_format(abs($this->expenseComparison['difference']), 2, ',', '.') }} € face ao mês anterior
                @else
                    Igual ao mês anterior
                @endif
                ({{ number_format($this->expenseComparison['previous'], 2, ',', '.') }} €)
            </p>
        </div>

        <div class="rounded-3xl border border-indigo-100 bg-white p-5 shadow-sm dark:border-indigo-900/50 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-indigo-500/10 p-3 text-indigo-600 dark:text-indigo-400">
                    <flux:icon name="scale" class="size-5" />
                </div>
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Saldo previsto do mês</h4>
                    <p class="mt-1 text-xl font-black tabular-nums {{ $monthlyFinanceInsights['balance'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($monthlyFinanceInsights['balance'] < 0 ? 'text-red-500' : 'text-zinc-400') }}">{{ number_format($monthlyFinanceInsights['balance'], 2, ',', '.') }} €</p>
                </div>
            </div>
            <p class="mt-3 text-[10px] font-semibold text-zinc-400">Entradas previstas menos saídas previstas</p>
        </div>

        <div class="rounded-3xl border border-amber-100 bg-white p-5 shadow-sm dark:border-amber-900/50 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-amber-500/10 p-3 text-amber-600 dark:text-amber-400">
                    <flux:icon name="scale" class="size-5" />
                </div>
                <div class="min-w-0">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Balanço do ano {{ $year }}</h4>
                    <p class="mt-1 text-xl font-black tabular-nums {{ $annualFinanceInsights['balance'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($annualFinanceInsights['balance'] < 0 ? 'text-red-500' : 'text-zinc-400') }}">{{ number_format($annualFinanceInsights['balance'], 2, ',', '.') }} €</p>
                </div>
            </div>
            <p class="mt-3 text-[10px] font-semibold text-zinc-400">Entradas menos saídas de janeiro a dezembro, incluindo previsões</p>
            @if($annualFinanceInsights['has_unknown_subscriptions'])
                <p class="mt-1 text-[10px] font-semibold text-amber-700 dark:text-amber-400">Assinaturas sem ciclo ou data definidos não entram na previsão.</p>
            @endif
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
            class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto bg-white dark:bg-zinc-900 rounded-[3rem] p-8 shadow-2xl border border-zinc-200 dark:border-zinc-800"
        >
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black uppercase italic tracking-tighter dark:text-white">Agendar Registo</h2>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">
                    Selecionaste o dia <span class="text-indigo-600" x-text="formattedDate"></span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                {{-- OPÇÃO: DESPESA --}}
                <button type="button" @click="quickAddModal = false; Livewire.dispatch('calendar-expense-create', { date: selectedDate })" class="group flex flex-col items-center p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-red-500/50 transition-all">
                    <div class="size-12 bg-red-500/10 rounded-2xl flex items-center justify-center text-red-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="minus-circle" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Nova Despesa</span>
                </button>

                {{-- OPÇÃO: RECEITA EXTRA --}}
                <button type="button" @click="quickAddModal = false; Livewire.dispatch('dashboard-income-create', { date: selectedDate })" class="group flex flex-col items-center p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-emerald-500/50 transition-all">
                    <div class="size-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="plus-circle" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Nova Receita extra</span>
                </button>

                {{-- OPÇÃO: BÓNUS --}}
                <button type="button" @click="quickAddModal = false; Livewire.dispatch('calendar-bonus-create', { date: selectedDate })" class="group flex flex-col items-center p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-amber-500/50 transition-all">
                    <div class="size-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="gift" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Novo Bónus</span>
                </button>

                {{-- OPÇÃO: ASSINATURA --}}
                <button type="button" @click="quickAddModal = false; Livewire.dispatch('dashboard-subscription-create', { date: selectedDate })" class="group flex flex-col items-center p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-violet-500/50 transition-all">
                    <div class="size-12 bg-violet-500/10 rounded-2xl flex items-center justify-center text-violet-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="arrow-path" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Nova Assinatura</span>
                </button>

                {{-- OPÇÃO: TRANSFERÊNCIA BANCÁRIA --}}
                <button type="button" @click="quickAddModal = false; Livewire.dispatch('calendar-bank-transfer-create', { date: selectedDate })" class="group flex flex-col items-center p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-blue-500/50 transition-all">
                    <div class="size-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="arrows-right-left" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Transferência bancária</span>
                </button>

                {{-- OPÇÃO: LEMBRETE --}}
                <button type="button" @click="quickAddModal = false; Livewire.dispatch('calendar-reminder-create', { date: selectedDate })" class="group flex flex-col items-center p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-700 hover:border-indigo-500/50 transition-all">
                    <div class="size-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 mb-3 group-hover:scale-110 transition-transform">
                        <flux:icon name="clock" class="size-6" />
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest dark:text-white">Novo lembrete</span>
                </button>
            </div>

            <button @click="quickAddModal = false" class="w-full mt-8 py-4 text-[10px] font-black uppercase text-zinc-400 hover:text-zinc-600 transition-colors">
                Fechar Janela
            </button>
        </div>
    </div>

    <div class="contents">
        <livewire:expenses :dashboard-mode="true" />
        <livewire:expense-form :dashboard-mode="true" />
        <livewire:income-hub :dashboard-mode="true" />
        <livewire:subscription-hub :dashboard-mode="true" />
        <livewire:bank-transactions-hub :dashboard-mode="true" />
        <livewire:hub.reminders-hub :dashboard-mode="true" />
    </div>

    @if($showVacationModal)
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 sm:p-6">
            <button type="button" class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md" aria-label="Fechar" wire:click="closeVacationModal"></button>

            <div class="relative w-full max-w-lg rounded-[2rem] border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white">Registar férias</h2>
                        <p class="mt-1 text-xs text-zinc-500">Vê os dias úteis e o subsídio de alimentação estimado que não receberás.</p>
                    </div>
                    <button type="button" wire:click="closeVacationModal" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white" aria-label="Fechar">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Primeiro dia</span>
                        <input type="date" wire:model.live="vacationForm.start_date" class="h-11 w-full rounded-xl border-0 bg-zinc-100 px-3 text-sm text-zinc-800 dark:bg-zinc-800 dark:text-white">
                        @error('vacationForm.start_date')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </label>
                    <label class="space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Último dia</span>
                        <input type="date" wire:model.live="vacationForm.end_date" class="h-11 w-full rounded-xl border-0 bg-zinc-100 px-3 text-sm text-zinc-800 dark:bg-zinc-800 dark:text-white">
                        @error('vacationForm.end_date')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </label>
                </div>

                @if($this->vacationSummary)
                    @php
                        $vacationSummary = $this->vacationSummary;
                    @endphp
                    <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/20">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Dias no período</p>
                                <p class="mt-1 font-black text-zinc-800 dark:text-zinc-100">{{ $vacationSummary['calendar_days'] }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Dias úteis</p>
                                <p class="mt-1 font-black text-zinc-800 dark:text-zinc-100">{{ $vacationSummary['working_days'] }}</p>
                            </div>
                        </div>
                        <p class="mt-3 text-[10px] font-semibold text-zinc-600 dark:text-zinc-300">
                            @if($vacationSummary['weekend_days'] > 0)
                                Inclui {{ $vacationSummary['weekend_days'] }} {{ $vacationSummary['weekend_days'] === 1 ? 'dia' : 'dias' }} de fim de semana.
                            @else
                                Não inclui dias de fim de semana.
                            @endif
                        </p>
                        @if($vacationSummary['holiday_days'] > 0)
                            <p class="mt-2 text-[10px] font-semibold text-amber-700 dark:text-amber-300">
                                Inclui {{ $vacationSummary['holiday_days'] }} {{ $vacationSummary['holiday_days'] === 1 ? 'feriado' : 'feriados' }}; não conta como dia útil nem gera subsídio de alimentação.
                            </p>
                        @endif
                        <p class="mt-3 text-xs font-black text-emerald-800 dark:text-emerald-300">
                            Subsídio de alimentação que não receberás:
                            {{ number_format($vacationSummary['foregone_allowance'], 2, ',', '.') }} €
                        </p>
                        @unless($vacationSummary['has_meal_allowance'])
                            <p class="mt-1 text-[10px] text-zinc-500">Não foi encontrado um contrato ativo com subsídio de alimentação.</p>
                        @endunless
                    </div>
                @else
                    <p class="mt-3 text-[10px] text-amber-600">Seleciona um intervalo de datas válido para calcular o resumo.</p>
                @endif

                <label class="mt-4 block space-y-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Notas (opcional)</span>
                    <textarea wire:model="vacationForm.notes" rows="2" maxlength="1000" class="w-full resize-none rounded-xl border-0 bg-zinc-100 px-3 py-2 text-sm text-zinc-800 dark:bg-zinc-800 dark:text-white"></textarea>
                    @error('vacationForm.notes')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                </label>

                @if($this->vacationPeriods->isNotEmpty())
                    <div class="mt-6 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <h3 class="mb-3 text-[10px] font-black uppercase tracking-wider text-zinc-500">Férias neste mês</h3>
                        <div class="max-h-36 space-y-2 overflow-y-auto">
                            @foreach($this->vacationPeriods as $period)
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-zinc-50 px-3 py-2 dark:bg-zinc-800/70">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-zinc-700 dark:text-zinc-200">
                                            {{ $period->start_date->format('d/m/Y') }}–{{ $period->end_date->format('d/m/Y') }}
                                        </p>
                                        @if($period->notes)
                                            <p class="truncate text-[10px] text-zinc-500">{{ $period->notes }}</p>
                                        @endif
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="deleteVacationPeriod({{ $period->id }})"
                                        wire:confirm="Remover este período de férias?"
                                        class="shrink-0 text-[10px] font-bold text-red-500 hover:text-red-700"
                                    >
                                        Remover
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex gap-3">
                    <button type="button" wire:click="closeVacationModal" class="flex-1 rounded-xl px-4 py-3 text-xs font-black uppercase text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">Cancelar</button>
                    <button type="button" wire:click="saveVacationPeriod" wire:loading.attr="disabled" wire:target="saveVacationPeriod" class="flex-1 rounded-xl bg-emerald-600 px-4 py-3 text-xs font-black uppercase text-white transition hover:bg-emerald-700 disabled:opacity-50">Guardar férias</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── MODAL: DETALHE COMPLETO DO DIA ── --}}
    @if($showDayDetailModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">
            <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md" wire:click="closeDayDetail"></div>

            <div class="relative w-full max-w-lg max-h-[85vh] flex flex-col bg-white dark:bg-zinc-900 rounded-[3rem] shadow-2xl border border-zinc-200 dark:border-zinc-800">
                <div class="p-8 pb-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between shrink-0">
                    <div>
                        <h2 class="text-xl font-black uppercase italic tracking-tighter dark:text-white leading-none">
                            @if($selectedDayKey)
                                {{ \Carbon\Carbon::parse($selectedDayKey)->day }} de {{ \Carbon\Carbon::parse($selectedDayKey)->translatedFormat('F') }}
                            @endif
                        </h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1.5">
                            {{ $this->selectedDayEvents->count() }} registo(s) neste dia
                        </p>
                        @if($this->selectedDaySummary['has_financial_records'])
                            @php
                                $dayIncome = $this->selectedDaySummary['income'];
                                $dayOutflow = $this->selectedDaySummary['outflow'];
                                $dayTotal = $this->selectedDaySummary['total'];
                                $isPendingDay = $selectedDayKey >= $pendingFromDate;
                            @endphp
                            <div class="mt-3 grid grid-cols-3 gap-2">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider text-zinc-400">Receitas</p>
                                    <p class="text-xs font-black tabular-nums text-emerald-600">+{{ number_format($dayIncome, 2, ',', '.') }}€</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider text-zinc-400">Despesas</p>
                                    <p class="text-xs font-black tabular-nums text-red-500">{{ $dayOutflow > 0 ? '−' : '' }}{{ number_format($dayOutflow, 2, ',', '.') }}€</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider text-zinc-400">{{ $isPendingDay ? 'Total previsto' : 'Total do dia' }}</p>
                                    <p class="text-xs font-black tabular-nums {{ $isPendingDay ? 'text-amber-700 dark:text-amber-300' : ($dayTotal > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($dayTotal < 0 ? 'text-red-500' : 'text-zinc-400')) }}">
                                        {{ $dayTotal > 0 ? '+' : ($dayTotal < 0 ? '−' : '') }}{{ number_format(abs($dayTotal), 2, ',', '.') }}€
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button wire:click="closeDayDetail" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <div class="p-8 pt-4 space-y-3 overflow-y-auto custom-scrollbar flex-1">
                    @if($selectedDayKey && $riskDays->has($selectedDayKey))
                        @php($riskBalance = $riskDays->get($selectedDayKey)['projected_balance'])
                        <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <div class="size-2.5 rounded-full shrink-0 bg-indigo-600"></div>
                                        <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300">Dia de risco</span>
                                    </div>
                                    <span class="ml-5 mt-1 block text-[10px] font-medium text-indigo-600 dark:text-indigo-400">Saldo projetado acumulado negativo</span>
                                </div>
                                <span class="text-sm font-black tabular-nums text-indigo-700 dark:text-indigo-300">
                                    −{{ number_format(abs($riskBalance), 2, ',', '.') }}€
                                </span>
                            </div>
                        </div>
                    @endif
                    @forelse($this->selectedDayEvents as $event)
                        @php($isMealAllowanceEvent = ($event['subtype'] ?? null) === 'meal_allowance')
                        @php($isWaiting = ($isMealAllowanceEvent && $event['status'] === 'expected') || (($event['subtype'] ?? null) === 'salary_projection' && $event['date'] >= $pendingFromDate))
                        @if($isMealAllowanceEvent)
                            @php($formKey = 'mealAllowanceForms.'.$event['contract_id'])
                        @endif
                        <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-3">
                                        @if($event['calendar_icon'])
                                            <span role="img" aria-label="Ícone: {{ $event['calendar_icon_label'] }}" class="inline-flex size-5 shrink-0 items-center justify-center {{ $calendarIndicatorColors[$event['color']] ?? $event['color'] }}">
                                                <flux:icon name="{{ $event['calendar_icon'] }}" class="size-5" />
                                            </span>
                                        @else
                                            <span class="inline-flex size-5 shrink-0 items-center justify-center" aria-hidden="true">
                                                <span class="size-2.5 rounded-full {{ str_replace('text', 'bg', $event['color']) }}"></span>
                                            </span>
                                        @endif
                                        <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ $event['label'] }}</span>
                                    </div>
                                    @if($isMealAllowanceEvent)
                                        <span class="ml-8 mt-1 inline-block text-[9px] font-black uppercase tracking-wider {{ $isWaiting ? 'text-orange-600 dark:text-orange-400' : $event['color'] }}">
                                            {{ match ($event['status']) { 'expected' => 'À espera', 'received' => 'Pago', 'partial' => 'Parcial', 'missed' => 'Não recebido' } }}
                                        </span>
                                        @if($event['notes'])
                                            <span class="ml-8 mt-1 block text-[10px] font-medium text-zinc-500">{{ $event['notes'] }}</span>
                                        @endif
                                    @elseif($isWaiting)
                                        <span class="ml-8 mt-1 inline-block text-[9px] font-black uppercase tracking-wider text-orange-600 dark:text-orange-400">À espera</span>
                                    @endif
                                    @if(isset($event['meta']))
                                        <span class="ml-8 mt-1 block text-[10px] font-medium text-zinc-500">{{ $event['meta'] }}</span>
                                    @endif
                                </div>
                                @if(isset($event['amount']))
                                    <span class="text-sm font-black shrink-0 {{ $isWaiting ? 'text-orange-600 dark:text-orange-400' : $event['color'] }}">
                                        {{ in_array($event['type'], ['income', 'salary']) ? '+' : ($event['type'] === 'transfer' ? '' : '-') }}{{ number_format($event['amount'], 2, ',', '.') }}€
                                    </span>
                                @endif
                            </div>
                            @if(($event['subtype'] ?? null) === 'payday')
                                <div class="ml-8 mt-3 space-y-1 text-xs text-zinc-600 dark:text-zinc-300">
                                    <div class="flex justify-between gap-3">
                                        <span>Salário líquido mensal previsto</span>
                                        <span class="font-black tabular-nums">{{ number_format($event['payday_net_salary'], 2, ',', '.') }} €</span>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                        <span>Subsídio de alimentação mensal previsto</span>
                                        <span class="font-black tabular-nums">{{ number_format($event['payday_meal_allowance'], 2, ',', '.') }} €</span>
                                    </div>
                                </div>
                            @endif
                            @if($event['type'] === 'transfer' && ($event['editable'] ?? false) && !auth()->user()->isViewer())
                                <button
                                    type="button"
                                    wire:click="editBankTransfer({{ $event['transfer_id'] }})"
                                    class="ml-8 mt-2 text-[10px] font-black uppercase tracking-wider text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                                >Editar</button>
                            @elseif($isMealAllowanceEvent && !auth()->user()->isViewer() && $this->editingEventKey !== $event['event_key'])
                                <button
                                    type="button"
                                    wire:click="beginEditMealAllowance({{ $event['contract_id'] }}, '{{ $event['date'] }}')"
                                    class="ml-8 mt-2 text-[10px] font-black uppercase tracking-wider text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                                >Editar</button>
                            @elseif(($event['editable'] ?? false) && !auth()->user()->isViewer() && !$isMealAllowanceEvent)
                                <button
                                    type="button"
                                    wire:click="beginEditEvent('{{ $event['event_key'] }}')"
                                    class="ml-8 mt-2 text-[10px] font-black uppercase tracking-wider text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                                >Editar</button>
                            @endif
                            @if(!$isMealAllowanceEvent && ($this->editingEventKey ?? null) === ($event['event_key'] ?? null))
                                <div class="mt-4 space-y-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                                    @if(($event['subtype'] ?? null) === 'vacation')
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="space-y-1">
                                                <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Início</span>
                                                <input type="date" wire:model="eventEditForm.start_date" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-xs font-bold text-zinc-800 dark:bg-zinc-900 dark:text-white">
                                                @if($errors->has('eventEditForm.start_date'))<span class="text-[10px] text-red-500">{{ $errors->first('eventEditForm.start_date') }}</span>@endif
                                            </label>
                                            <label class="space-y-1">
                                                <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Fim</span>
                                                <input type="date" wire:model="eventEditForm.end_date" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-xs font-bold text-zinc-800 dark:bg-zinc-900 dark:text-white">
                                                @if($errors->has('eventEditForm.end_date'))<span class="text-[10px] text-red-500">{{ $errors->first('eventEditForm.end_date') }}</span>@endif
                                            </label>
                                        </div>
                                        <label class="block space-y-1">
                                            <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Notas</span>
                                            <textarea wire:model="eventEditForm.notes" rows="2" maxlength="1000" class="w-full resize-none rounded-lg border-0 bg-white px-3 py-2 text-xs text-zinc-800 dark:bg-zinc-900 dark:text-white"></textarea>
                                        </label>
                                    @else
                                        <label class="block space-y-1">
                                            <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Descrição</span>
                                            <input type="text" maxlength="255" wire:model="eventEditForm.label" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-xs font-bold text-zinc-800 dark:bg-zinc-900 dark:text-white">
                                            @if($errors->has('eventEditForm.label'))<span class="text-[10px] text-red-500">{{ $errors->first('eventEditForm.label') }}</span>@endif
                                        </label>
                                        <label class="block space-y-1">
                                            <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Data</span>
                                            <input type="date" wire:model="eventEditForm.date" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-xs font-bold text-zinc-800 dark:bg-zinc-900 dark:text-white">
                                            @if($errors->has('eventEditForm.date'))<span class="text-[10px] text-red-500">{{ $errors->first('eventEditForm.date') }}</span>@endif
                                        </label>
                                        @if(isset($event['amount']))
                                            <label class="block space-y-1">
                                                <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Valor ({{ $event['currency'] ?? 'EUR' }})</span>
                                                <input type="number" min="0" step="0.01" wire:model="eventEditForm.amount" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-xs font-bold text-zinc-800 dark:bg-zinc-900 dark:text-white">
                                                @if($errors->has('eventEditForm.amount'))<span class="text-[10px] text-red-500">{{ $errors->first('eventEditForm.amount') }}</span>@endif
                                            </label>
                                        @endif
                                        @if($event['type'] === 'fitness')
                                            <label class="block space-y-1">
                                                <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Duração (minutos)</span>
                                                <input type="number" min="1" max="1440" step="1" wire:model="eventEditForm.duration_minutes" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-xs font-bold text-zinc-800 dark:bg-zinc-900 dark:text-white">
                                                @if($errors->has('eventEditForm.duration_minutes'))<span class="text-[10px] text-red-500">{{ $errors->first('eventEditForm.duration_minutes') }}</span>@endif
                                            </label>
                                        @endif
                                        @if($event['type'] === 'reminder' && !in_array($event['subtype'] ?? null, ['public_holiday', 'work_milestone'], true))
                                            <label class="flex items-center gap-2 text-xs font-bold text-zinc-600 dark:text-zinc-300">
                                                <input type="checkbox" wire:model="eventEditForm.is_completed" class="rounded border-zinc-300 text-indigo-600">
                                                Concluído
                                            </label>
                                        @endif
                                    @endif
                                    <div class="flex justify-end gap-2">
                                        <button type="button" wire:click="cancelEditEvent" class="rounded-lg px-3 py-2 text-[10px] font-black uppercase tracking-wider text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-700">Cancelar</button>
                                        <button type="button" wire:click="saveEvent('{{ $event['event_key'] }}')" wire:loading.attr="disabled" wire:target="saveEvent" class="rounded-lg bg-indigo-600 px-4 py-2 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-indigo-700 disabled:opacity-50">Guardar</button>
                                    </div>
                                </div>
                            @endif
                            @if($isMealAllowanceEvent && $this->editingEventKey === $event['event_key'])
                                <p class="ml-5 mt-3 text-[10px] font-semibold text-zinc-500">Previsto: {{ number_format($event['expected_amount'], 2, ',', '.') }} €</p>
                                <div class="mt-3 grid grid-cols-1 gap-3">
                                    <label class="space-y-1">
                                        <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Valor recebido</span>
                                        <input type="number" min="0" step="0.01" wire:model="{{ $formKey }}.received_amount" class="h-10 w-full rounded-lg border-0 bg-white px-3 text-xs font-bold text-zinc-800 dark:bg-zinc-900 dark:text-white">
                                        @if($errors->has($formKey.'.received_amount'))<span class="text-[10px] text-red-500">{{ $errors->first($formKey.'.received_amount') }}</span>@endif
                                    </label>
                                </div>
                                <label class="mt-3 block space-y-1">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-zinc-500">Notas</span>
                                    <textarea wire:model="{{ $formKey }}.notes" rows="2" maxlength="1000" class="w-full resize-none rounded-lg border-0 bg-white px-3 py-2 text-xs text-zinc-800 dark:bg-zinc-900 dark:text-white" placeholder="Opcional"></textarea>
                                    @if($errors->has($formKey.'.notes'))<span class="text-[10px] text-red-500">{{ $errors->first($formKey.'.notes') }}</span>@endif
                                </label>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <button type="button" wire:click="cancelEditEvent" class="rounded-lg px-3 py-2 text-[10px] font-black uppercase tracking-wider text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-700">Cancelar</button>
                                    <button type="button" wire:click="$set('{{ $formKey }}.received_amount', '0.00')" class="text-[10px] font-bold text-red-600 hover:text-red-700 dark:text-red-400">Não recebido · 0 €</button>
                                    <button type="button" wire:click="saveMealAllowanceDay({{ $event['contract_id'] }}, '{{ $event['date'] }}')" wire:loading.attr="disabled" wire:target="saveMealAllowanceDay" class="rounded-lg bg-emerald-600 px-4 py-2 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-emerald-700 disabled:opacity-50">Guardar dia</button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <flux:icon name="calendar" class="size-8 mx-auto mb-3 text-zinc-200 dark:text-zinc-700" />
                            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Sem registos neste dia</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-8 pt-4 border-t border-zinc-100 dark:border-zinc-800 shrink-0">
                    <button wire:click="closeDayDetail" class="w-full py-4 text-[10px] font-black uppercase text-zinc-400 hover:text-zinc-600 dark:hover:text-white transition-colors">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

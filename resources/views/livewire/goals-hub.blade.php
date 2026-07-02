<div
    x-data
    class="space-y-8 pb-24"
>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- FLASH --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if(session('ok'))
        <div
            class="fixed top-6 right-6 z-50 bg-emerald-500 text-white px-5 py-3 rounded-2xl shadow-xl font-bold text-sm flex items-center gap-2"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3500)"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <flux:icon name="check-circle" class="w-4 h-4" />
            {{ session('ok') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 1. HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 px-1">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                    <flux:icon name="trophy" class="w-8 h-8 text-brand-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">
                        Metas de Futuro
                    </h1>
                    <span class="text-[9px] font-black uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-3 py-1 rounded-full">
                        Roadmap Financeiro
                    </span>
                </div>
                <p class="text-xs text-zinc-400 mt-1">
                    Converte capital em conquistas · {{ now()->format('d M Y') }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- BOTÃO NOVA META --}}
            <flux:button
    @click="$dispatch('modal-show-goal')"

    variant="primary"
    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
    <flux:icon name="calendar-days" class="size-4 mr-2" />
    Nova Meta
</flux:button>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 2. KPIs --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Hero: Capital acumulado --}}
        <div class="col-span-2 bg-zinc-950 text-white p-7 rounded-3xl shadow-2xl relative overflow-hidden border border-zinc-800">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-brand-500/10 blur-3xl rounded-full pointer-events-none"></div>
            <div class="relative z-10">
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-brand-400 mb-2">
                    Capital Acumulado
                </p>
                <h3 class="text-5xl font-black tracking-tighter italic tabular-nums">
                    {{ number_format($totalCurrent, 0, ',', ' ') }}
                    <small class="text-xl not-italic">€</small>
                </h3>
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex-1 h-1.5 bg-white/10 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-brand-500 rounded-full shadow-[0_0_8px_#3b82f6]"
                            style="width:{{ min(100,$globalPct) }}%"
                        ></div>
                    </div>
                    <span class="text-xs font-black text-brand-400 tabular-nums">
                        {{ round($globalPct) }}%
                    </span>
                </div>
                <p class="mt-2 text-[10px] text-zinc-500 font-bold">
                    de {{ number_format($totalTarget, 0, ',', ' ') }} € em objetivos
                </p>
            </div>
        </div>

        {{-- Gap --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">
                Falta Alcançar
            </p>
            <p class="text-3xl font-black text-orange-500 tracking-tighter italic tabular-nums">
                {{ number_format($totalGap, 0, ',', ' ') }} €
            </p>
            <p class="text-[9px] text-zinc-400 mt-2 font-bold">
                em {{ $goals->where('isCompleted', false)->count() }} metas ativas
            </p>
        </div>

        {{-- Status rápido --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-3">
                Estado
            </p>
            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-emerald-500">✓ Concluídas</span>
                    <span class="text-sm font-black text-emerald-500">{{ $completed }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-amber-500">⚡ Urgentes</span>
                    <span class="text-sm font-black text-amber-500">{{ $urgent }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-red-400">✕ Vencidas</span>
                    <span class="text-sm font-black text-red-400">{{ $overdue }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 3. GRELHA DE METAS --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

        @forelse($goals as $goal)
            @php
                $borderColor = match(true) {
                    $goal->isCompleted => 'border-emerald-400/50 dark:border-emerald-500/30',
                    $goal->isOverdue   => 'border-red-400/50 dark:border-red-500/30',
                    $goal->isUrgent    => 'border-amber-400/50 dark:border-amber-500/30',
                    default            => 'border-zinc-200 dark:border-zinc-800',
                };
                $accentColor = match(true) {
                    $goal->isCompleted => 'bg-emerald-500',
                    $goal->isOverdue   => 'bg-red-500',
                    $goal->isUrgent    => 'bg-amber-500',
                    default            => 'bg-brand-500',
                };
                $labelColor = match(true) {
                    $goal->isCompleted => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/20',
                    $goal->isOverdue   => 'text-red-400 bg-red-50 dark:bg-red-900/20',
                    $goal->isUrgent    => 'text-amber-500 bg-amber-50 dark:bg-amber-900/20',
                    default            => 'text-brand-600 bg-brand-50 dark:bg-brand-900/20',
                };
                $statusLabel = match(true) {
                    $goal->isCompleted => '✓ Concluído',
                    $goal->isOverdue   => '✕ Vencido',
                    $goal->isUrgent    => '⚡ Urgente',
                    default            => '→ Em curso',
                };
            @endphp

            <div class="bg-white dark:bg-zinc-900 border {{ $borderColor }} rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300 group relative overflow-hidden">

                {{-- Barra lateral de estado --}}
                <div class="absolute left-0 top-6 bottom-6 w-1 {{ $accentColor }} rounded-r-full opacity-60 group-hover:opacity-100 transition-opacity"></div>

                {{-- Acções topo --}}
                <div class="flex items-start justify-between mb-4 pl-2">
                    <div class="flex-1 min-w-0 pr-3">
                        <h3 class="text-base font-black dark:text-white uppercase tracking-tight italic leading-tight truncate group-hover:text-brand-600 transition-colors">
                            {{ $goal->name }}
                        </h3>
                        <span class="inline-flex mt-1.5 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg {{ $labelColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        @if(!$goal->isCompleted)
                            <button
                                wire:click="openDeposit({{ $goal->id }})"
                                class="w-7 h-7 flex items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                title="Depositar"
                            >
                                <flux:icon name="plus" class="w-3.5 h-3.5" />
                            </button>
                        @endif
                        <button
                            wire:click="edit({{ $goal->id }})"
                            class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-zinc-200 transition-colors"
                            title="Editar"
                        >
                            <flux:icon name="pencil" class="w-3.5 h-3.5" />
                        </button>
                        <button
                            wire:click="delete({{ $goal->id }})"
                            wire:confirm="Confirmas que queres apagar esta meta?"
                            class="w-7 h-7 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-400 hover:bg-red-100 transition-colors"
                            title="Apagar"
                        >
                            <flux:icon name="trash" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>

                {{-- Valores --}}
                <div class="flex justify-between items-end mb-4 pl-2">
                    <div>
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-0.5">
                            Acumulado
                        </p>
                        <p class="text-2xl font-black dark:text-white tracking-tighter italic tabular-nums">
                            {{ number_format($goal->current_amount, 0, ',', ' ') }}
                            <small class="text-sm">€</small>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-0.5">
                            Objetivo
                        </p>
                        <p class="text-sm font-black text-zinc-500 tabular-nums">
                            {{ number_format($goal->target_amount, 0, ',', ' ') }} €
                        </p>
                    </div>
                </div>

                {{-- Barra de progresso --}}
                <div class="pl-2 mb-4">
                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-1000 {{ $accentColor }}"
                            style="width:{{ min(100, $goal->perc) }}%"
                        ></div>
                    </div>
                    <div class="flex justify-between mt-1.5 text-[9px] font-black">
                        <span class="{{ $goal->isCompleted ? 'text-emerald-500' : 'text-brand-600' }}">
                            {{ round($goal->perc) }}%
                        </span>
                        <span class="text-zinc-400 tabular-nums">
                            falta {{ number_format($goal->gap, 0, ',', ' ') }} €
                        </span>
                    </div>
                </div>

                {{-- Footer: prazo + poupança necessária --}}
                <div class="pl-2 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        @if($goal->deadline)
                            <p class="text-[9px] font-black {{ $goal->isOverdue ? 'text-red-400' : ($goal->isUrgent ? 'text-amber-500' : 'text-zinc-400') }} uppercase tracking-widest">
                                @if($goal->daysLeft !== null && $goal->daysLeft >= 0)
                                 {{ floor($goal->daysLeft) }} dias restantes

                                @elseif($goal->isOverdue)
                                   Vencido há {{ abs(floor($goal->daysLeft)) }} dias

                                @endif
                            </p>
                            <p class="text-[9px] text-zinc-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="text-[9px] text-zinc-400 uppercase tracking-widest">
                                Sem prazo definido
                            </p>
                        @endif
                    </div>
                    @if($goal->monthlyNeeded && !$goal->isCompleted)
                        <div class="text-right">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">
                                Mensal necessário
                            </p>
                            <p class="text-xs font-black text-indigo-500 tabular-nums">
                                {{ number_format($goal->monthlyNeeded, 0, ',', ' ') }} €
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                <flux:icon name="sparkles" class="size-10 text-zinc-300 mx-auto mb-3" />
                <p class="text-zinc-400 font-black uppercase tracking-widest text-[10px] mb-4">
                    Sem metas definidas
                </p>
                <flux:button
                    @click="$dispatch('modal-show-goal')"
                    variant="primary"
                    size="sm"
                    icon="plus"
                    class="rounded-2xl font-black uppercase tracking-widest"
                >
                    Criar primeira meta
                </flux:button>
            </div>
        @endforelse
    </div>

















    {{-- MODAL: CRIAR / EDITAR META (IGUAL AO DAS DÍVIDAS) --}}
<div
    x-data="{
        open: false,
        show() {
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    }"
    x-on:modal-show-goal.window="show()"

    x-on:modal-close-goal.window="close()"

    x-on:keydown.escape.window="close()"
    style="display: none;"
    x-show="open"
>

    {{-- BACKDROP (SEM BLUR PESADO) --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.120ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/80"
    ></div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >

        {{-- PAINEL VERDE (IGUAL AO DAS DÍVIDAS) --}}
        <div
            @click.stop
            x-transition.scale.duration.120ms
            class="relative w-full max-w-lg rounded-[2rem] overflow-hidden
                   backdrop-blur-sm border border-white/20 shadow-[0_6px_22px_-4px_rgba(0,0,0,0.45)]
                   bg-emerald-500/15"
        >

            <form wire:submit.prevent="save" class="p-8 space-y-7">

                {{-- HEADER --}}
                <div class="space-y-3 text-center">

                    {{-- ÍCONE --}}
                    <div class="inline-flex p-3 rounded-2xl mx-auto bg-emerald-500/20 text-emerald-400">
                        <flux:icon name="flag" class="size-6" />
                    </div>

                    <h2 class="text-3xl font-black uppercase italic tracking-tight text-white">
                        {{ $editingGoalId ? 'Editar Meta' : 'Nova Meta' }}
                    </h2>

                    <p class="text-[10px] text-zinc-200 font-semibold uppercase tracking-[0.25em]">
                        Define os parâmetros da tua meta financeira
                    </p>
                </div>

                {{-- FORM BODY --}}
                <div class="space-y-6">

                    {{-- NOME --}}
                    <div>
                        <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                            Nome da Meta
                        </flux:label>
                        <flux:input wire:model="name"
                            placeholder="Ex: Fundo de Emergência..."
                            class="rounded-2xl bg-white/10 border-white/10 text-white placeholder-zinc-400" />
                        @error('name') <p class="text-red-400 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- VALOR ALVO + VALOR ATUAL --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                                Valor Alvo (€)
                            </flux:label>
                            <flux:input wire:model="target_amount" type="number" step="0.01"
                                class="rounded-2xl bg-white/10 border-white/10 text-white font-black placeholder-zinc-400" />
                        </div>

                        <div>
                            <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                                Valor Atual (€)
                            </flux:label>
                            <flux:input wire:model="current_amount" type="number" step="0.01"
                                class="rounded-2xl bg-white/10 border-white/10 text-white font-black placeholder-zinc-400" />
                        </div>
                    </div>

                    {{-- PRAZO --}}
                    <div>
                        <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                            Prazo (opcional)
                        </flux:label>
                        <flux:input wire:model="deadline" type="date"
                            class="rounded-2xl bg-white/10 border-white/10 text-white placeholder-zinc-400" />
                    </div>

                </div>

                {{-- AÇÕES --}}
                <div class="flex gap-3 pt-4">

                    {{-- CANCELAR --}}
                    <button type="button" @click="close()"
                        class="flex-1 px-4 py-3 text-[10px] font-black uppercase tracking-widest
                               text-zinc-300 hover:text-white transition-colors duration-100">
                        Cancelar
                    </button>

                    {{-- BOTÃO VERDE --}}
                    <button type="submit"
                        class="flex-[2] h-12 rounded-2xl font-black uppercase tracking-widest
                               text-white transition-all duration-120 shadow-md
                               bg-emerald-500 hover:bg-emerald-400 shadow-emerald-500/40">
                        {{ $editingGoalId ? 'Guardar Alterações' : 'Criar Meta' }}
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>






















{{-- MODAL: REGISTAR DEPÓSITO --}}
<div
    x-data="{
        open: false,
        show() {
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    }"
    x-on:modal-show-deposit.window="show()"
    x-on:modal-close-deposit.window="close()"
    x-on:keydown.escape.window="close()"
    style="display: none;"
    x-show="open"
>

    {{-- BACKDROP --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.120ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/80"
    ></div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >

        {{-- PAINEL --}}
        <div
            @click.stop
            x-transition.scale.duration.120ms
            class="relative w-full max-w-lg rounded-[2rem] overflow-hidden
                   backdrop-blur-sm border border-white/20 shadow-[0_6px_22px_-4px_rgba(0,0,0,0.45)]
                   bg-emerald-500/15"
        >

            <form wire:submit.prevent="saveDeposit" class="p-8 space-y-7">

                {{-- HEADER --}}
                <div class="space-y-3 text-center">
                    <div class="inline-flex p-3 rounded-2xl mx-auto bg-emerald-500/20 text-emerald-400">
                        <flux:icon name="arrow-down-tray" class="size-6" />
                    </div>

                    <h2 class="text-3xl font-black uppercase italic tracking-tight text-white">
                        Registar Depósito
                    </h2>

                    <p class="text-[10px] text-zinc-200 font-semibold uppercase tracking-[0.25em]">
                        Adiciona capital à tua meta
                    </p>
                </div>

                {{-- FORM --}}
                <div class="space-y-6">

                    <div>
                        <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">
                            Montante (€)
                        </flux:label>

                        <flux:input
                            wire:model="depositAmount"
                            type="number"
                            step="0.01"
                            placeholder="0,00"
                            class="rounded-2xl bg-white/10 border-white/10 text-white font-black placeholder-zinc-400"
                        />
                    </div>

                </div>

                {{-- AÇÕES --}}
                <div class="flex gap-3 pt-4">

                    <button type="button" @click="close()"
                        class="flex-1 px-4 py-3 text-[10px] font-black uppercase tracking-widest
                               text-zinc-300 hover:text-white transition-colors duration-100">
                        Cancelar
                    </button>

                    <button type="submit"
                        class="flex-[2] h-12 rounded-2xl font-black uppercase tracking-widest
                               text-white transition-all duration-120 shadow-md
                               bg-emerald-500 hover:bg-emerald-400 shadow-emerald-500/40">
                        Confirmar Depósito
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>


</div>

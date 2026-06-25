<div class="space-y-8 pb-24">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- FLASH --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if(session('ok'))
        <div class="fixed top-6 right-6 z-50 bg-emerald-500 text-white px-5 py-3 rounded-2xl shadow-xl font-bold text-sm flex items-center gap-2"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
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
                    <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Metas de Futuro</h1>
                    <span class="text-[9px] font-black uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-3 py-1 rounded-full">Roadmap Financeiro</span>
                </div>
                <p class="text-xs text-zinc-400 mt-1">Converte capital em conquistas · {{ now()->format('d M Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <flux:modal.trigger name="goal-modal">
                <flux:button variant="primary" icon="plus" class="rounded-2xl px-5 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                    Nova Meta
                </flux:button>
            </flux:modal.trigger>
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
                <p class="text-[9px] font-black uppercase tracking-[0.35em] text-brand-400 mb-2">Capital Acumulado</p>
                <h3 class="text-5xl font-black tracking-tighter italic tabular-nums">
                    {{ number_format($totalCurrent, 0, ',', ' ') }} <small class="text-xl not-italic">€</small>
                </h3>
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex-1 h-1.5 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-500 rounded-full shadow-[0_0_8px_#3b82f6]"
                             style="width:{{ min(100,$globalPct) }}%"></div>
                    </div>
                    <span class="text-xs font-black text-brand-400 tabular-nums">{{ round($globalPct) }}%</span>
                </div>
                <p class="mt-2 text-[10px] text-zinc-500 font-bold">de {{ number_format($totalTarget, 0, ',', ' ') }} € em objetivos</p>
            </div>
        </div>

        {{-- Gap --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Falta Alcançar</p>
            <p class="text-3xl font-black text-orange-500 tracking-tighter italic tabular-nums">{{ number_format($totalGap, 0, ',', ' ') }} €</p>
            <p class="text-[9px] text-zinc-400 mt-2 font-bold">em {{ $goals->where('isCompleted', false)->count() }} metas ativas</p>
        </div>

        {{-- Status rápido --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-3">Estado</p>
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
                            <button wire:click="openDeposit({{ $goal->id }})"
                                class="w-7 h-7 flex items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                title="Depositar">
                                <flux:icon name="plus" class="w-3.5 h-3.5" />
                            </button>
                        @endif
                        <button wire:click="edit({{ $goal->id }})"
                            class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-zinc-200 transition-colors"
                            title="Editar">
                            <flux:icon name="pencil" class="w-3.5 h-3.5" />
                        </button>
                        <button wire:click="delete({{ $goal->id }})"
                            wire:confirm="Confirmas que queres apagar esta meta?"
                            class="w-7 h-7 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-400 hover:bg-red-100 transition-colors"
                            title="Apagar">
                            <flux:icon name="trash" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>

                {{-- Valores --}}
                <div class="flex justify-between items-end mb-4 pl-2">
                    <div>
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-0.5">Acumulado</p>
                        <p class="text-2xl font-black dark:text-white tracking-tighter italic tabular-nums">
                            {{ number_format($goal->current_amount, 0, ',', ' ') }} <small class="text-sm">€</small>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-0.5">Objetivo</p>
                        <p class="text-sm font-black text-zinc-500 tabular-nums">{{ number_format($goal->target_amount, 0, ',', ' ') }} €</p>
                    </div>
                </div>

                {{-- Barra de progresso --}}
                <div class="pl-2 mb-4">
                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 {{ $accentColor }}"
                             style="width:{{ min(100, $goal->perc) }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1.5 text-[9px] font-black">
                        <span class="{{ $goal->isCompleted ? 'text-emerald-500' : 'text-brand-600' }}">{{ round($goal->perc) }}%</span>
                        <span class="text-zinc-400 tabular-nums">falta {{ number_format($goal->gap, 0, ',', ' ') }} €</span>
                    </div>
                </div>

                {{-- Footer: prazo + poupança necessária --}}
                <div class="pl-2 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        @if($goal->deadline)
                            <p class="text-[9px] font-black {{ $goal->isOverdue ? 'text-red-400' : ($goal->isUrgent ? 'text-amber-500' : 'text-zinc-400') }} uppercase tracking-widest">
                                @if($goal->daysLeft !== null && $goal->daysLeft >= 0)
                                    {{ $goal->daysLeft }} dias restantes
                                @elseif($goal->isOverdue)
                                    Vencido há {{ abs($goal->daysLeft) }} dias
                                @endif
                            </p>
                            <p class="text-[9px] text-zinc-400 mt-0.5">{{ \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y') }}</p>
                        @else
                            <p class="text-[9px] text-zinc-400 uppercase tracking-widest">Sem prazo definido</p>
                        @endif
                    </div>
                    @if($goal->monthlyNeeded && !$goal->isCompleted)
                        <div class="text-right">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Mensal necessário</p>
                            <p class="text-xs font-black text-indigo-500 tabular-nums">{{ number_format($goal->monthlyNeeded, 0, ',', ' ') }} €</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                <flux:icon name="sparkles" class="size-10 text-zinc-300 mx-auto mb-3" />
                <p class="text-zinc-400 font-black uppercase tracking-widest text-[10px] mb-4">Sem metas definidas</p>
                <flux:modal.trigger name="goal-modal">
                    <flux:button variant="primary" size="sm" icon="plus" class="rounded-2xl font-black uppercase tracking-widest">
                        Criar primeira meta
                    </flux:button>
                </flux:modal.trigger>
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 4. MODAL: CRIAR / EDITAR META --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="goal-modal" position="center" class="md:w-[520px] !p-0 overflow-hidden">
        <div class="flex flex-col max-h-[86vh] bg-white dark:bg-zinc-950 rounded-3xl border border-zinc-200 dark:border-zinc-800">

            {{-- HEADER --}}
            <div class="shrink-0 px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4 bg-white dark:bg-zinc-950 rounded-t-3xl">
                <div class="p-3 bg-brand-500/10 rounded-2xl text-brand-600">
                    <flux:icon name="flag" class="size-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                        {{ $editingGoalId ? 'Editar Meta' : 'Nova Meta' }}
                    </h2>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                        Define os parâmetros da tua conquista financeira
                    </p>
                </div>
                <button type="button" x-on:click="$dispatch('modal-close', { name: 'goal-modal' })"
                        class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            {{-- BODY --}}
            <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-5">

                {{-- Nome da Meta --}}
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-brand-600 z-10">
                        Nome da Meta
                    </label>
                    <input wire:model="name" type="text"
                           placeholder="Ex: Fundo de Emergência, Viagem, Casa..."
                           class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-brand-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                    @error('name') <p class="text-xs text-red-400 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Valor Alvo + Valor Atual --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                            Valor Alvo (€)
                        </label>
                        <input wire:model="target_amount" type="number" step="0.01" placeholder="0,00"
                               class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-brand-500 rounded-2xl py-4 px-5 text-lg font-black text-brand-600 outline-none transition-all">
                        @error('target_amount') <p class="text-xs text-red-400 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                            Valor Atual (€)
                        </label>
                        <input wire:model="current_amount" type="number" step="0.01" placeholder="0,00"
                               class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-brand-500 rounded-2xl py-4 px-5 text-lg font-black dark:text-white outline-none transition-all">
                        @error('current_amount') <p class="text-xs text-red-400 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Prazo --}}
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Prazo (opcional)
                    </label>
                    <input wire:model="deadline" type="date"
                           class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-brand-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="shrink-0 px-5 sm:px-6 py-4 flex flex-col sm:flex-row gap-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-b-3xl">
                <button type="button"
                        x-on:click="$dispatch('modal-close', { name: 'goal-modal' })"
                        class="w-full h-14 rounded-2xl text-zinc-500 hover:text-zinc-800 dark:hover:text-white
                               hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold uppercase text-xs tracking-widest
                               transition-all active:scale-95">
                    Cancelar
                </button>
                <button type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="w-full h-14 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-brand-500/20
                               transition-all text-xs active:scale-95 disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">{{ $editingGoalId ? 'Guardar Alterações' : 'Criar Meta' }}</span>
                    <span wire:loading wire:target="save">A guardar...</span>
                </button>
            </div>

        </div>
    </flux:modal>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 5. MODAL: DEPÓSITO RÁPIDO --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="deposit-modal" position="center" class="md:w-[400px] !p-0 overflow-hidden">
        <div class="flex flex-col bg-white dark:bg-zinc-950 rounded-3xl border border-zinc-200 dark:border-zinc-800">

            {{-- HEADER --}}
            <div class="shrink-0 px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4 bg-white dark:bg-zinc-950 rounded-t-3xl">
                <div class="p-3 bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="arrow-down-tray" class="size-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                        Registar Depósito
                    </h2>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                        Adiciona capital à tua meta
                    </p>
                </div>
                <button type="button" x-on:click="$dispatch('modal-close', { name: 'deposit-modal' })"
                        class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-5 sm:p-6">
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Montante (€)
                    </label>
                    <input wire:model="depositAmount" type="number" step="0.01" placeholder="0,00"
                           class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-2xl font-black text-emerald-600 text-center outline-none transition-all">
                    @error('depositAmount') <p class="text-xs text-red-400 font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="shrink-0 px-5 sm:px-6 py-4 flex flex-col sm:flex-row gap-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-b-3xl">
                <button type="button"
                        x-on:click="$dispatch('modal-close', { name: 'deposit-modal' })"
                        class="w-full h-14 rounded-2xl text-zinc-500 hover:text-zinc-800 dark:hover:text-white
                               hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold uppercase text-xs tracking-widest
                               transition-all active:scale-95">
                    Cancelar
                </button>
                <button type="button"
                        wire:click="saveDeposit"
                        wire:loading.attr="disabled"
                        wire:target="saveDeposit"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all text-xs active:scale-95 disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveDeposit">Confirmar Depósito</span>
                    <span wire:loading wire:target="saveDeposit">A guardar...</span>
                </button>
            </div>

        </div>
    </flux:modal>

</div>

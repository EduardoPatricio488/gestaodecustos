<div class="space-y-10 pb-24">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 1. HEADER DE TESOURARIA --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-72 bg-orange-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative">
                    <div class="absolute inset-0 bg-orange-500/20 blur-2xl rounded-full"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] shadow-2xl">
                        <flux:icon name="hand-raised" class="w-10 h-10 text-orange-500" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Dívidas & Créditos</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Fluxo de Terceiros</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-400 font-medium italic mt-2">Controlo de <span class="text-orange-500 font-bold uppercase tracking-tighter">Passivos</span> e <span class="text-emerald-500 font-bold uppercase tracking-tighter">Ativos Circulantes</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                {{-- BOTÃO NOVO (AJUSTADO): Limpa o formulário e abre o modal via Alpine --}}
                 <flux:button
    wire:click="openCreateModal"
    variant="primary"
    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
    <flux:icon name="calendar-days" class="size-4 mr-2" />
    Novo Registo
</flux:button>
            </div>
        </header>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 2. KPIs --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Total que Eu Devo --}}
        <div class="bg-zinc-950 text-white p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800">
            <div class="absolute -right-10 -top-10 size-48 bg-red-500/10 blur-3xl rounded-full pointer-events-none"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-red-400 mb-2">Total em Dívida (Passivo)</p>
                <h3 class="text-6xl font-black tracking-tighter italic">{{ number_format($totalIOwe, 2, ',', ' ') }} <small class="text-xl not-italic">€</small></h3>
                <div class="mt-5 flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="size-2 bg-red-500 rounded-full animate-pulse"></div>
                        <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">{{ $iOwe->count() }} registos pendentes</p>
                    </div>
                    @if($overdueCount > 0)
                        <span class="text-[9px] font-black bg-red-500/20 text-red-400 border border-red-500/30 px-2.5 py-1 rounded-xl uppercase tracking-widest">
                            ✕ {{ $overdueCount }} vencidas
                        </span>
                    @endif
                    @if($urgentCount > 0)
                        <span class="text-[9px] font-black bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2.5 py-1 rounded-xl uppercase tracking-widest">
                            ⚡ {{ $urgentCount }} urgentes
                        </span>
                    @endif
                </div>
            </div>
            <flux:icon name="arrow-trending-down" class="absolute -right-4 -bottom-4 size-28 text-white/5 -rotate-12" />
        </div>

        {{-- Total a Receber + Balanço líquido --}}
        <div class="grid grid-rows-2 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl px-8 py-5 shadow-sm relative overflow-hidden flex items-center justify-between">
                <div class="absolute -right-8 -top-8 size-32 bg-emerald-500/5 blur-3xl rounded-full pointer-events-none"></div>
                <div class="relative z-10">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Total a Receber (Ativo)</p>
                    <h3 class="text-3xl font-black text-emerald-500 tracking-tighter italic tabular-nums">{{ number_format($totalTheyOweMe, 2, ',', ' ') }} <small class="text-sm not-italic">€</small></h3>
                </div>
                <flux:icon name="arrow-trending-up" class="size-12 text-emerald-100 dark:text-emerald-900/30 flex-shrink-0" />
            </div>

            <div class="rounded-3xl px-8 py-5 shadow-sm border flex items-center justify-between
                {{ $netBalance >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-800/40' : 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800/40' }}">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.3em] mb-1 {{ $netBalance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">Balanço Líquido</p>
                    <h3 class="text-3xl font-black tracking-tighter italic tabular-nums {{ $netBalance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $netBalance >= 0 ? '+' : '' }}{{ number_format($netBalance, 2, ',', ' ') }} €
                    </h3>
                </div>
                <span class="text-2xl">{{ $netBalance >= 0 ? '↑' : '↓' }}</span>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 3. LEDGER PENDENTES --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- COLUNA: EU DEVO (PASSIVO) --}}
        <div class="space-y-5">
            <div class="flex items-center gap-3 px-1">
                <div class="p-2 bg-orange-500/10 rounded-lg text-orange-500">
                    <flux:icon name="arrow-down-circle" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Mapa de Passivos (Owe)</h2>
                <span class="ml-auto text-[9px] font-black bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-2.5 py-1 rounded-full">{{ $iOwe->count() }}</span>
            </div>

            <div class="space-y-3">
                @forelse($iOwe as $debt)
                    @php
                        $borderAccent = $debt->isOverdue ? 'hover:border-red-400/50 border-l-red-400' : ($debt->isUrgent ? 'hover:border-amber-400/50 border-l-amber-400' : 'hover:border-orange-400/50 border-l-zinc-200 dark:border-l-zinc-700');
                    @endphp
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 border-l-4 {{ $borderAccent }} rounded-[2rem] p-5 flex justify-between items-center group transition-all duration-300 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 flex items-center justify-center text-orange-500 font-black text-xs flex-shrink-0">
                                {{ strtoupper(substr($debt->person_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $debt->person_name }}</p>
                                @if($debt->description)
                                    <p class="text-[10px] text-zinc-400 italic mt-0.5 max-w-[160px] truncate">{{ $debt->description }}</p>
                                @endif
                                @if($debt->due_at)
                                    <span class="inline-flex mt-1 text-[8px] font-black px-2 py-0.5 rounded uppercase tracking-widest
                                        {{ $debt->isOverdue ? 'bg-red-100 dark:bg-red-900/30 text-red-500 border border-red-200 dark:border-red-800/50' : ($debt->isUrgent ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 border border-amber-200 dark:border-amber-800/50' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500') }}">
                                        {{ $debt->isOverdue ? 'Vencida' : 'Vence' }} {{ \Carbon\Carbon::parse($debt->due_at)->format('d M') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-lg font-black text-orange-500 tracking-tighter italic tabular-nums">{{ number_format($debt->amount, 2, ',', ' ') }} €</p>
                                @if($debt->daysLeft !== null && !$debt->isOverdue)
                                    <p class="text-[9px] text-zinc-400 font-bold">{{ $debt->daysLeft }}d restantes</p>
                                @endif
                            </div>
                            <div class="flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{-- Liquidar --}}
                                <button wire:click="togglePaid({{ $debt->id }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                    title="Liquidar">
                                    <flux:icon name="check" class="w-3.5 h-3.5" />
                                </button>
                                {{-- EDITAR (AJUSTADO): Agora dispara o modal-show para abrir o modal --}}
                                <button wire:click="edit({{ $debt->id }})"
    class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-zinc-200 transition-colors"
    title="Editar">
    <flux:icon name="pencil" class="w-3.5 h-3.5" />
</button>
                                {{-- Eliminar --}}
                                <button wire:click="delete({{ $debt->id }})" wire:confirm="Apagar registo?"
                                    class="w-7 h-7 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-400 hover:bg-red-100 transition-colors"
                                    title="Apagar">
                                    <flux:icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                        <p class="text-zinc-400 font-black uppercase tracking-widest text-[9px]">Passivo totalmente liquidado ✓</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- COLUNA: DEVEM-ME (ATIVO) --}}
        <div class="space-y-5">
            <div class="flex items-center gap-3 px-1">
                <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-500">
                    <flux:icon name="arrow-up-circle" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Mapa de Ativos (Owed)</h2>
                <span class="ml-auto text-[9px] font-black bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-2.5 py-1 rounded-full">{{ $theyOweMe->count() }}</span>
            </div>

            <div class="space-y-3">
                @forelse($theyOweMe as $debt)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 border-l-4 border-l-emerald-400/50 rounded-[2rem] p-5 flex justify-between items-center group hover:border-emerald-400/50 transition-all duration-300 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-500/10 flex items-center justify-center text-emerald-600 font-black text-xs flex-shrink-0">
                                {{ strtoupper(substr($debt->person_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $debt->person_name }}</p>
                                @if($debt->description)
                                    <p class="text-[10px] text-zinc-400 italic mt-0.5 max-w-[160px] truncate">{{ $debt->description }}</p>
                                @endif
                                @if($debt->due_at)
                                    <span class="inline-flex mt-1 text-[8px] font-black bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 border border-emerald-200 dark:border-emerald-800/50 px-2 py-0.5 rounded uppercase tracking-widest">
                                        Expectativa {{ \Carbon\Carbon::parse($debt->due_at)->format('d M') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-lg font-black text-emerald-500 tracking-tighter italic tabular-nums">{{ number_format($debt->amount, 2, ',', ' ') }} €</p>
                                @if($debt->daysLeft !== null)
                                    <p class="text-[9px] text-zinc-400 font-bold">em {{ $debt->daysLeft }}d</p>
                                @endif
                            </div>
                            <div class="flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{-- Confirmar Recebimento --}}
                                <button wire:click="togglePaid({{ $debt->id }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 hover:bg-emerald-100 transition-colors"
                                    title="Confirmar Recebimento">
                                    <flux:icon name="check" class="w-3.5 h-3.5" />
                                </button>
                                {{-- EDITAR (AJUSTADO): Abre o modal --}}
                                <button wire:click="edit({{ $debt->id }})"
                                    x-on:click="$dispatch('modal-show', { name: 'add-debt-modal' })"
                                    class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-zinc-200 transition-colors"
                                    title="Editar">
                                    <flux:icon name="pencil" class="w-3.5 h-3.5" />
                                </button>
                                {{-- Eliminar --}}
                                <button wire:click="delete({{ $debt->id }})" wire:confirm="Apagar registo?"
                                    class="w-7 h-7 flex items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/20 text-red-400 hover:bg-red-100 transition-colors"
                                    title="Apagar">
                                    <flux:icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                        <p class="text-zinc-400 font-black uppercase tracking-widest text-[9px]">Sem créditos pendentes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 4. HISTÓRICO DE LIQUIDAÇÕES --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm relative overflow-hidden">
        <div class="absolute right-8 top-8 opacity-5 pointer-events-none">
            <flux:icon name="check-badge" class="size-24 text-zinc-900 dark:text-white" />
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-7">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="clock" variant="outline" class="size-4" />
                </div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400">Auditoria de Liquidações Recentes</h2>
            </div>

            <div class="space-y-1">
                @forelse($history as $item)
                    <div class="flex justify-between items-center py-3.5 border-b border-zinc-50 dark:border-zinc-800/50 last:border-0 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 px-3 rounded-2xl group transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-xl group-hover:scale-105 transition-transform">
                                <flux:icon name="check-badge" variant="solid" class="text-zinc-400 size-4" />
                            </div>
                            <div>
                                <p class="text-xs font-black dark:text-zinc-200 uppercase tracking-tight">{{ $item->person_name }}</p>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase italic mt-0.5">{{ $item->updated_at->format('d/m/Y · H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-widest border
                                {{ $item->type === 'owe' ? 'text-orange-500 bg-orange-500/5 border-orange-200 dark:border-orange-800/40' : 'text-emerald-500 bg-emerald-500/5 border-emerald-200 dark:border-emerald-800/40' }}">
                                {{ $item->type === 'owe' ? 'Pago' : 'Recebido' }}
                            </span>
                            <span class="text-sm font-black dark:text-zinc-300 tracking-tighter tabular-nums">{{ number_format($item->amount, 2, ',', ' ') }} €</span>

                            {{-- Botão Reabrir (Anular Liquidação) --}}
                            <button wire:click="togglePaid({{ $item->id }})"
                                class="opacity-0 group-hover:opacity-100 transition-opacity w-6 h-6 flex items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:bg-zinc-200 text-[10px]"
                                title="Reabrir">
                                <flux:icon name="arrow-uturn-left" class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">Sem registos de liquidação.</p>
                @endforelse
            </div>
        </div>
    </div>
{{-- 5. MODAL: REGISTAR / EDITAR --}}
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
    x-on:open-debt-modal.window="show()"
    x-on:close-debt-modal.window="close()"
    x-on:keydown.escape.window="close()"
    style="display: none;"
    x-show="open"
>

    {{-- BACKDROP COM COR (SEM BLUR PESADO) --}}
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

        {{-- PAINEL COM TOM DINÂMICO (LARANJA / VERDE) --}}
        <div
            @click.stop
            x-transition.scale.duration.120ms
            class="relative w-full max-w-lg rounded-[2rem] overflow-hidden
                   backdrop-blur-sm border border-white/20 shadow-[0_6px_22px_-4px_rgba(0,0,0,0.45)]
                   {{ $type === 'owe'
                        ? 'bg-orange-500/15'
                        : 'bg-emerald-500/15' }}"
        >

            <form wire:submit.prevent="save" class="p-8 space-y-7">

                {{-- HEADER --}}
                <div class="space-y-3 text-center">

                    {{-- ÍCONE AO CENTRO + DINÂMICO --}}
                    <div class="inline-flex p-3 rounded-2xl mx-auto
                        {{ $type === 'owed'
                            ? 'bg-emerald-500/20 text-emerald-400'
                            : 'bg-orange-500/20 text-orange-400' }}">
                        <flux:icon name="hand-raised" class="size-6" />
                    </div>

                    <h2 class="text-3xl font-black uppercase italic tracking-tight text-white">
                        {{ $editingId ? 'Editar Registo' : 'Nova Operação' }}
                    </h2>

                    <p class="text-[10px] text-zinc-200 font-semibold uppercase tracking-[0.25em]">
                        Documente a transferência de capital
                    </p>
                </div>

                {{-- FORM BODY --}}
                <div class="space-y-6">

                    {{-- TOGGLE TIPO --}}
                    <div class="grid grid-cols-2 gap-2 p-1.5 bg-white/10 rounded-2xl border border-white/10">
                        <button type="button" wire:click="$set('type', 'owe')"
                            class="py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-100
                            {{ $type === 'owe'
                                ? 'bg-orange-500/25 text-orange-400'
                                : 'text-zinc-300 hover:text-zinc-100' }}">
                            EU DEVO
                        </button>

                        <button type="button" wire:click="$set('type', 'owed')"
                            class="py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-100
                            {{ $type === 'owed'
                                ? 'bg-emerald-500/25 text-emerald-400'
                                : 'text-zinc-300 hover:text-zinc-100' }}">
                            DEVEM-ME
                        </button>
                    </div>

                    {{-- ENTIDADE --}}
                    <div>
                        <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">Entidade / Pessoa</flux:label>
                        <flux:input wire:model="person_name" placeholder="Ex: João Silva..." class="rounded-2xl bg-white/10 border-white/10 text-white placeholder-zinc-400" />
                        @error('person_name') <p class="text-red-400 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- MONTANTE + VENCIMENTO --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">Montante (€)</flux:label>
                            <flux:input wire:model="amount" type="number" step="0.01"
                                class="rounded-2xl bg-white/10 border-white/10 text-white font-black placeholder-zinc-400" />
                        </div>

                        <div>
                            <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">Vencimento</flux:label>
                            <flux:input wire:model="due_at" type="date"
                                class="rounded-2xl bg-white/10 border-white/10 text-white placeholder-zinc-400" />
                        </div>
                    </div>

                    {{-- NOTAS --}}
                    <div>
                        <flux:label class="text-[9px] font-black uppercase tracking-[0.25em] mb-2 ml-1 text-zinc-200">Notas</flux:label>
                       <flux:textarea wire:model="description" rows="2"
    class="rounded-2xl bg-white/10 border-white/10 !text-white !placeholder-white/40" />

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

                    {{-- BOTÃO DINÂMICO (LARANJA / VERDE) --}}
                    <button type="submit"
                        class="flex-[2] h-12 rounded-2xl font-black uppercase tracking-widest
                               text-white transition-all duration-120 shadow-md
                               {{ $type === 'owe'
                                   ? 'bg-orange-500 hover:bg-orange-400 shadow-orange-500/40'
                                   : 'bg-emerald-500 hover:bg-emerald-400 shadow-emerald-500/40' }}">
                        {{ $editingId ? 'Guardar Alterações' : 'Validar Operação' }}
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>


    {{-- RODAPÉ --}}
    <footer class="pt-16 pb-4 text-center border-t border-zinc-100 dark:border-zinc-800 opacity-50">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Controlo de Tesouraria e Terceiros
        </p>
    </footer>

</div>

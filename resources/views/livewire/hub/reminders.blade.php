<div class="max-w-7xl mx-auto space-y-10 pb-20" x-data="{ modal: @entangle('openModal') }">

    {{-- ── ESTILOS DE ALTA FIDELIDADE ── --}}
    @once
    <style>
        .reminder-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(16, 185, 129, 0.15);
            border-radius: 2.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .reminder-card:hover { transform: translateY(-8px); box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.15); }

        .text-black-nlt { color: #0f172a !important; }
        .bg-emerald-soft { background: rgba(16, 185, 129, 0.05); }

        .category-badge { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; padding: 4px 12px; border-radius: 10px; }

        .priority-indicator { width: 6px; height: 40px; border-radius: 10px; }

        [x-cloak] { display: none !important; }
    </style>
    @endonce
{{-- ── 2. CABEÇALHO DE CONTROLO (TOTALMENTE RESPONSIVO) ── --}}
    <div class="flex flex-col xl:flex-row gap-6 items-center justify-between bg-white dark:bg-zinc-900/50 p-6 lg:p-8 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800 shadow-sm w-full">

        {{-- Grupo 1: Pesquisa e Filtros de Estado --}}
        <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:flex-1">
            <div class="relative w-full md:max-w-xs">
                <flux:icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-zinc-400" />
                <input wire:model.live.debounce.400ms="search" placeholder="Filtrar..."
                    class="w-full pl-11 pr-6 py-4 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border-none shadow-inner text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <nav class="flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-2xl w-full md:w-auto shrink-0">
                @foreach(['pending' => 'Pendentes', 'history' => 'Histórico', 'all' => 'Todos'] as $tab => $label)
                    <button wire:click="$set('activeTab', '{{ $tab }}')"
                        class="flex-1 md:flex-none px-4 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all
                        {{ $activeTab === $tab ? 'bg-white dark:bg-zinc-900 text-emerald-600 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Grupo 2: Prioridade e Botão de Acção --}}
        <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
            <select wire:model.live="filterPriority" class="w-full md:w-48 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-[10px] font-black uppercase tracking-widest outline-none cursor-pointer">
                <option value="all">Prioridades</option>
                <option value="high">🔴 Alta</option>
                <option value="medium">🟡 Média</option>
                <option value="low">🟢 Baixa</option>
            </select>

            <button @click="modal = true; $wire.openReminderModal()"
                class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-emerald-500/30 transition-all active:scale-95 whitespace-nowrap flex items-center justify-center gap-2">
                <flux:icon name="plus" class="size-4" />
                Novo Lembrete
            </button>
        </div>
    </div>
    {{-- ── 1. DASHBOARD DE ESTATÍSTICAS (KPIs) — LIQUID GLASS PREMIUM ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-6">

    @foreach ([
        ['Ativos', $this->stats['total_active'], 'emerald', 'clock'],
        ['Urgentes', $this->stats['high_priority'], 'red', 'fire'],
        ['Concluídos Hoje', $this->stats['completed_today'], 'blue', 'check-badge'],
        ['Em Atraso', $this->stats['overdue'], 'amber', 'exclamation-circle']
    ] as [$label, $value, $color, $icon])

        <div class="relative overflow-hidden p-8 rounded-[2.5rem]
                    bg-white/10 dark:bg-zinc-900/30
                    backdrop-blur-lg
                    border border-white/10 dark:border-white/5
                    shadow-[0_6px_28px_rgba(0,0,0,0.35)]
                    flex flex-col justify-between group transition-all duration-200 hover:scale-[1.02]">

            {{-- Glow do ícone --}}
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full
                        bg-{{ $color }}-500/20 blur-2xl opacity-40 group-hover:opacity-60 transition-all"></div>

            {{-- Ícone --}}
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-all">
                <flux:icon :name="$icon" class="size-24 text-{{ $color }}-500" />
            </div>

            {{-- Label --}}
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-300">
                {{ $label }}
            </p>

            {{-- Valor --}}
            <h3 class="text-4xl font-black mt-2 tracking-tighter text-{{ $color }}-400 drop-shadow-sm">
                {{ $value }}
            </h3>

        </div>

    @endforeach

</div>




    {{-- ── 3. LISTA DE CARTÕES ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($this->reminders as $reminder)
            <div wire:key="rem-{{ $reminder->id }}" class="reminder-card p-8 flex flex-col justify-between group relative overflow-hidden">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <button wire:click="toggleComplete({{ $reminder->id }})"
                            class="size-9 rounded-2xl border-2 flex items-center justify-center transition-all {{ $reminder->is_completed ? 'bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-500/40' : 'border-zinc-200 hover:border-emerald-500 bg-white' }}">
                            @if($reminder->is_completed) <flux:icon name="check" variant="mini" class="size-5" /> @endif
                        </button>
                        <span class="category-badge bg-emerald-100 text-emerald-700">{{ $reminder->category }}</span>
                    </div>

                    <div class="priority-indicator {{ 'priority-'.$reminder->priority }}"></div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-xl font-black text-black-nlt tracking-tight leading-tight {{ $reminder->is_completed ? 'line-through opacity-30' : '' }}">
                        {{ $reminder->title }}
                    </h4>
                    @if($reminder->notes)
                        <p class="text-sm text-zinc-500 font-medium line-clamp-2 italic">"{{ $reminder->notes }}"</p>
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t border-zinc-100 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Data Limite</span>
                        <span class="text-xs font-bold text-zinc-900 mt-1 flex items-center gap-2">
                            <flux:icon name="calendar" variant="micro" class="size-4 text-emerald-600" />
                            {{ $reminder->remind_at->format('d M, Y · H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1">
                        <button wire:click="openReminderModal({{ $reminder->id }})" class="p-2.5 rounded-xl hover:bg-emerald-50 text-zinc-400 hover:text-emerald-600 transition-colors">
                            <flux:icon name="pencil-square" class="size-5" />
                        </button>
                        <button wire:click="deleteReminder({{ $reminder->id }})" class="p-2.5 rounded-xl hover:bg-red-50 text-zinc-400 hover:text-red-500 transition-colors">
                            <flux:icon name="trash" class="size-5" />
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-40 text-center bg-white/50 rounded-[4rem] border-2 border-dashed border-zinc-200">
                <flux:icon name="archive-box" class="size-20 mx-auto mb-6 text-zinc-200" />
                <h3 class="text-xl font-black text-zinc-400 uppercase tracking-widest">Nada para Processar</h3>
                <p class="text-zinc-500 mt-2 font-medium italic">A tua agenda está totalmente limpa.</p>
            </div>
        @endforelse
    </div>













{{-- ── 4. MODAL ALPINE (ULTRA FLUIDO + CAMPOS ESCUROS + TEXTO PRETO) ── --}}
<div x-show="modal" x-cloak
     class="fixed inset-0 z-[300] flex items-center justify-center p-3 sm:p-6
            bg-zinc-950/50 transition-opacity duration-75">

    <div @click.away="modal = false"
         x-show="modal"

         {{-- ENTRADA SUPER LEVE --}}
         x-transition:enter="transition duration-80 ease-out"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"

         {{-- SAÍDA SUPER LEVE --}}
         x-transition:leave="transition duration-60 ease-in"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"

         class="relative w-full max-w-2xl rounded-[2.5rem] overflow-hidden
                bg-white/10 dark:bg-zinc-900/30
                backdrop-blur-lg
                border border-white/10 dark:border-white/5
                shadow-[0_6px_28px_rgba(0,0,0,0.45)]
                max-h-[90vh] flex flex-col">

        {{-- HEADER --}}
        <div class="shrink-0 px-8 py-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">
                    Configurar Lembrete
                </h2>
                <p class="text-[9px] text-emerald-300 font-black uppercase mt-2 tracking-widest">
                    Agendamento Inteligente Finance Connect
                </p>
            </div>

            <button @click="modal = false"
                    class="p-3 bg-white/10 rounded-2xl shadow-sm text-white hover:text-emerald-300 transition-all active:scale-95">
                <flux:icon name="x-mark" class="size-6" />
            </button>
        </div>

        {{-- FORM --}}
        <form wire:submit.prevent="saveReminder" class="flex flex-col min-h-0 flex-1">

            {{-- BODY --}}
            <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">

                {{-- TÍTULO --}}
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                        Título do Evento
                    </label>
                    <input type="text" wire:model="title"
                           placeholder="Ex: Liquidação de Dividendos"
                           class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                  rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white
                                  placeholder-zinc-500 outline-none transition-all focus:ring-2 focus:ring-emerald-500/40">
                </div>

                {{-- DATA E HORA --}}
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                        Data e Hora
                    </label>
                    <input type="datetime-local" wire:model="remind_at"
                           class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                  rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white
                                  outline-none transition-all focus:ring-2 focus:ring-emerald-500/40">
                </div>

                {{-- PRIORIDADE + FREQUÊNCIA --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- PRIORIDADE --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Prioridade
                        </label>
                        <select wire:model="priority"
                                class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                       rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white outline-none">
                            <option class="text-black" value="low">🟢 Baixa</option>
                            <option class="text-black" value="medium">🟡 Média</option>
                            <option class="text-black" value="high">🔴 Alta</option>
                        </select>
                    </div>

                    {{-- FREQUÊNCIA --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Frequência
                        </label>
                        <select wire:model="frequency"
                                class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                       rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white outline-none">
                            <option class="text-black" value="once">📌 Única</option>
                            <option class="text-black" value="daily">📅 Diária</option>
                            <option class="text-black" value="weekly">🔁 Semanal</option>
                            <option class="text-black" value="monthly">📆 Mensal</option>
                        </select>
                    </div>
                </div>

                {{-- CATEGORIA --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-300 px-1">
                        Categoria
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['finance' => 'Finanças', 'personal' => 'Pessoal', 'work' => 'Trabalho', 'health' => 'Saúde'] as $val => $l)
                            <button type="button"
                                    wire:click="$set('category', '{{ $val }}')"
                                    class="py-3 rounded-2xl border-2 text-[9px] font-black uppercase tracking-widest transition-all
                                    {{ $category === $val
                                        ? 'bg-emerald-600 text-white border-emerald-600'
                                        : 'bg-zinc-200 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-black dark:text-white hover:border-emerald-500' }}">
                                {{ $l }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- NOTAS --}}
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                        Notas (opcional)
                    </label>
                    <textarea wire:model="notes" rows="3"
                              placeholder="Detalhes estratégicos..."
                              class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                     rounded-2xl py-4 px-5 text-sm font-medium text-black dark:text-white
                                     outline-none placeholder-zinc-500"></textarea>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="shrink-0 px-8 py-5 flex flex-col sm:flex-row gap-3 border-t border-white/10 bg-white/5">

                <button type="button"
                        @click="modal = false"
                        class="w-full h-14 rounded-2xl text-white/70 hover:text-white
                               hover:bg-white/10 font-bold uppercase text-xs tracking-widest
                               transition-all active:scale-95">
                    Cancelar
                </button>

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="saveReminder"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500
                               text-white font-black uppercase text-xs tracking-widest shadow-xl
                               shadow-emerald-500/30 transition-all active:scale-95 disabled:opacity-60
                               flex items-center justify-center gap-3">
                    <span wire:loading.remove wire:target="saveReminder">
                        <flux:icon name="check-circle" class="size-5 inline-block mr-2" />
                        {{ $editingReminderId ? 'Guardar Modificações' : 'Finalizar Agendamento 🟢' }}
                    </span>
                    <span wire:loading wire:target="saveReminder">A guardar...</span>
                </button>

            </div>

        </form>
    </div>
</div>














</div>

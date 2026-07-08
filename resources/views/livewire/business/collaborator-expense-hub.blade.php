<div class="space-y-10 pb-20">
    {{-- 1. HEADER & AÇÃO PRINCIPAL --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-xl text-emerald-600">
                <flux:icon name="banknotes" class="w-10 h-10" />
            </div>
            <div>
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Notas de Gastos</h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Gestão de despesas e reembolsos operacionais</p>
            </div>
        </div>

        {{-- BOTÃO NOVO REGISTO: VERDE, MAIOR E COM SOMBRA --}}
        <flux:button
            wire:click="resetForm"
            x-on:click="$dispatch('modal-show', { name: 'expense-modal' })"
            variant="primary"
            icon="plus"
            class="rounded-2xl px-12 h-16 font-black uppercase tracking-[0.2em] text-xs shadow-2xl shadow-emerald-500/40 !bg-emerald-600 hover:!bg-emerald-500 transition-all active:scale-95 group border-none"
        >
            <span class="group-hover:scale-110 transition-transform">Novo Registo</span>
        </flux:button>
    </div>

    {{-- 2. DASHBOARD RÁPIDO (KPIs) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Aguardam Aprovação</p>
                <h3 class="text-4xl font-black text-amber-500 tracking-tighter">{{ number_format($stats['total_pending'], 2) }}€</h3>
            </div>
            <flux:icon name="clock" class="size-12 text-zinc-100 dark:text-zinc-800" />
        </div>

        <div class="glass-card bg-emerald-600 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-500/10 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Aprovado p/ Reembolso</p>
                <h3 class="text-4xl font-black text-white tracking-tighter">{{ number_format($stats['total_approved'], 2) }}€</h3>
            </div>
            <flux:icon name="check-circle" class="size-12 text-white/20" />
        </div>
    </div>

    {{-- 3. TABELA DE GASTOS COM DESIGN PREMIUM --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <flux:table>
            <flux:table.columns>
                {{-- STATUS CENTRADO --}}
                <flux:table.column class="text-center uppercase text-[9px] font-black tracking-[0.2em] text-zinc-400 w-32">
    Status
</flux:table.column>
                <flux:table.column class="text-center uppercase text-[9px] font-black tracking-[0.2em] text-zinc-400 w-24">Data</flux:table.column>
                <flux:table.column class="uppercase text-[9px] font-black tracking-[0.2em] text-zinc-400">O que foi adquirido</flux:table.column>
                <flux:table.column class="uppercase text-[9px] font-black tracking-[0.2em] text-zinc-400">Vínculo Operacional</flux:table.column>
                <flux:table.column class="text-center uppercase text-[9px] font-black tracking-[0.2em] text-zinc-400 w-20">Anexo</flux:table.column>
                <flux:table.column class="text-right uppercase text-[9px] font-black tracking-[0.2em] text-zinc-400 w-32">Valor</flux:table.column>
                <flux:table.column class="pr-8 text-center uppercase text-[9px] font-black tracking-[0.2em] text-zinc-400 w-24">Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($expenses as $expense)
                    <flux:table.row class="group transition-all hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">

                        {{-- STATUS CENTRADO --}}
                       <flux:table.cell class="text-center">
    <div class="flex justify-center items-center w-full">
        @php
            $s = trim(strtolower($expense->status));
            $statusStyle = match($s) {
                'aprovado' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                'rejeitado' => 'bg-red-500/10 text-red-600 border-red-500/20',
                default => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
            };
        @endphp
        <span class="inline-flex items-center justify-center px-3 py-1 rounded-md text-[8px] font-black uppercase tracking-widest border {{ $statusStyle }}">
            {{ $expense->status }}
        </span>
    </div>
</flux:table.cell>

                        {{-- DATA --}}
                        <flux:table.cell class="text-center">
                            <div class="flex flex-col items-center leading-none">
                                <span class="text-xs font-black dark:text-white">{{ $expense->spent_at->format('d M') }}</span>
                                <span class="text-[9px] text-zinc-400 font-bold uppercase mt-1">{{ $expense->spent_at->format('Y') }}</span>
                            </div>
                        </flux:table.cell>

                        {{-- DESCRITIVO & CATEGORIA --}}
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="text-sm font-black dark:text-white uppercase tracking-tight group-hover:text-brand-600 transition-colors line-clamp-1">
                                    {{ str_replace('[Colaborador] ', '', $expense->description) }}
                                </span>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <flux:icon name="{{ $expense->category?->icon ?? 'tag' }}" variant="micro" class="size-3 text-zinc-400" />
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase italic">{{ $expense->category?->name ?? 'Geral' }}</span>
                                </div>
                            </div>
                        </flux:table.cell>

                        {{-- VÍNCULO OPERACIONAL (MOSTRA NOMES REAIS) --}}
                        <flux:table.cell>
    <div class="flex flex-col gap-2">

        {{-- SE FOR PROJETO --}}
        @if($expense->project)
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400 text-[8px] font-black uppercase border border-brand-200 dark:border-brand-500/20 tracking-widest">
                    Projeto
                </span>
                <span class="text-[11px] font-bold dark:text-white uppercase tracking-tight truncate max-w-[150px]">
                    {{ $expense->project->name }}
                </span>
            </div>
        @endif

        {{-- SE FOR TAREFA --}}
        @if($expense->task)
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-900/10 text-amber-700 dark:text-amber-500 text-[8px] font-black uppercase border border-amber-200 dark:border-amber-500/20 tracking-widest">
                    Tarefa
                </span>
                <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-tight truncate max-w-[150px]">
                    {{ $expense->task->title }}
                </span>
            </div>
        @endif

        {{-- SE NÃO TIVER NADA --}}
        @if(!$expense->project && !$expense->task)
            <span class="text-[10px] text-zinc-300 dark:text-zinc-700 italic uppercase tracking-widest opacity-50">
                Sem Vínculo
            </span>
        @endif

    </div>
</flux:table.cell>

                        {{-- ANEXO --}}
                        <flux:table.cell class="text-center">
                            @if($expense->receipt_path)
                                <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank"
                                   class="inline-flex size-8 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-emerald-600 hover:text-white transition-all shadow-sm group/clip">
                                    <flux:icon name="paper-clip" variant="micro" class="size-4 group-hover/clip:rotate-12 transition-transform" />
                                </a>
                            @else
                                <div class="size-8 inline-flex items-center justify-center rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800 opacity-20 cursor-not-allowed">
                                    <flux:icon name="paper-clip" variant="micro" class="size-4" />
                                </div>
                            @endif
                        </flux:table.cell>

                        {{-- VALOR --}}
                        <flux:table.cell class="text-right">
                            <span class="text-lg font-black dark:text-white tabular-nums tracking-tighter">
                                {{ number_format($expense->amount, 2, ',', ' ') }}€
                            </span>
                        </flux:table.cell>

                        {{-- AÇÕES (MENU 3 PONTOS) --}}
                        <flux:table.cell class="pr-8 text-center !overflow-visible">
                            <div class="flex justify-center items-center">
                                @if(trim(strtolower($expense->status)) === 'pendente')
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" @click.away="open = false" type="button"
                                                class="size-8 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all">
                                            <flux:icon name="ellipsis-horizontal" variant="micro" class="size-4" />
                                        </button>

                                        <div x-show="open" x-cloak x-transition
                                             class="absolute right-0 mt-2 w-36 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl z-[100] py-1 overflow-hidden"
                                             style="display: none;">
                                            <button wire:click="edit({{ $expense->id }})" @click="open = false"
                                                    class="w-full text-left px-4 py-2.5 text-[10px] font-black uppercase tracking-widest hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                                Editar Registo
                                            </button>
                                            <div class="border-t border-zinc-100 dark:border-zinc-800 mx-2 my-1"></div>
                                            <button wire:click="delete({{ $expense->id }})" @click="open = false"
                                                    wire:confirm="Desejas eliminar esta nota de gasto permanentemente?"
                                                    class="w-full text-left px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="size-8 flex items-center justify-center" title="Bloqueado: Registo já processado">
                                        <flux:icon name="lock-closed" variant="micro" class="size-4 text-zinc-300 dark:text-zinc-700 opacity-50" />
                                    </div>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        {{-- PAGINAÇÃO --}}
        @if($expenses->hasPages())
            <div class="p-6 bg-zinc-50/30 dark:bg-zinc-900/30 border-t border-zinc-100 dark:border-zinc-800">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    {{-- 4. MODAL DE SUBMISSÃO / EDIÇÃO --}}
    <flux:modal name="expense-modal" class="md:w-[600px] !p-0 overflow-visible rounded-[2.5rem]">
        <div class="p-10 bg-white dark:bg-zinc-950 space-y-8">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-emerald-600 rounded-2xl text-white shadow-xl">
                    <flux:icon name="{{ $editingId ? 'pencil-square' : 'plus' }}" class="size-6" />
                </div>
                <div>
                    <h2 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter">
                        {{ $editingId ? 'Editar Registo' : 'Nova Nota de Gasto' }}
                    </h2>
                    <p class="text-xs text-zinc-400 font-medium italic">O preenchimento correto garante o reembolso rápido.</p>
                </div>
            </div>

            {{-- GRID DE CAMPOS --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Descrição (Ocupa as 2 colunas) --}}
    <div class="md:col-span-2 text-left">
        <flux:input wire:model="description" label="O que foi adquirido?" placeholder="Ex: Gasóleo Carrinha, Almoço Cliente..." class="rounded-xl font-bold" />
    </div>

    {{-- Valor e Data --}}
    <div class="text-left">
        <flux:input wire:model="amount" type="number" step="0.01" label="Valor Total (€)" icon="currency-euro" class="rounded-xl font-black text-xl" />
    </div>
    <div class="text-left">
        <flux:input wire:model="spent_at" type="date" label="Data do Gasto" class="rounded-xl font-bold" />
    </div>

    {{-- Categoria (Ocupa as 2 colunas para manter a hierarquia) --}}
    <div class="md:col-span-2 text-left">
        <flux:select wire:model="category_id" label="Categoria do Gasto" class="rounded-xl font-bold uppercase text-xs">
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </flux:select>
    </div>

    {{-- PROJETO E TAREFA LADO A LADO (Mesmo Tamanho) --}}
    <div class="text-left">
        <flux:select wire:model="project_id" label="Vincular a Projeto" class="rounded-xl font-bold uppercase text-xs">
            <option value="">Nenhum Projeto</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </flux:select>
    </div>

    <div class="text-left">
        <flux:select wire:model="task_id" label="Vincular a Tarefa" class="rounded-xl font-bold uppercase text-xs">
            <option value="">Nenhuma Tarefa</option>
            @foreach($tasks as $t)
                <option value="{{ $t->id }}">{{ $t->title }}</option>
            @endforeach
        </flux:select>
    </div>


                {{-- UPLOAD DE COMPROVATIVO --}}
                <div class="md:col-span-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest mb-2 block text-left">Comprovativo (Recibo / Fatura)</flux:label>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 transition-all hover:border-emerald-500/50">
                        <input type="file" wire:model="receipt" class="text-xs" />
                        @if($existingReceiptPath && !$receipt)
                            <p class="mt-2 text-[10px] font-bold text-emerald-600 uppercase">✓ Ficheiro Guardado no Sistema</p>
                        @endif
                    </div>
                    <div wire:loading wire:target="receipt" class="text-[9px] text-emerald-600 font-bold uppercase animate-pulse mt-1 text-left">A carregar ficheiro...</div>
                </div>
            </div>

            {{-- BOTÕES DE AÇÃO DO MODAL --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close class="flex-1">
                    <flux:button variant="ghost" class="w-full font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black h-14 rounded-2xl !bg-emerald-600 shadow-xl shadow-emerald-500/20 uppercase tracking-widest transition-all">
                    {{ $editingId ? 'Salvar Alterações' : 'Submeter Nota' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

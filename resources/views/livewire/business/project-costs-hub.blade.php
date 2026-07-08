<div class="space-y-10 pb-20">
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="p-5 bg-zinc-950 dark:bg-brand-600 rounded-[2rem] shadow-xl text-white">
                <flux:icon name="presentation-chart-line" class="w-10 h-10" />
            </div>
            <div>
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Análise de Custos</h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorização de rentabilidade por projeto e tarefa</p>
            </div>
        </div>
    </div>

    {{-- 1. GRID DE PROJETOS (RENTABILIDADE) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @foreach($projects as $project)
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between">
                <div class="mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-2 py-1 rounded bg-brand-500/10 text-brand-600 text-[8px] font-black uppercase tracking-widest">Projeto Ativo</span>
                        <p class="text-[10px] font-bold text-zinc-400">Budget: {{ number_format($project->budget, 2) }}€</p>
                    </div>
                    <h3 class="text-xl font-black dark:text-white uppercase tracking-tight">{{ $project->name }}</h3>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-end">
                        <span class="text-[9px] font-black text-zinc-400 uppercase">Consumo do Orçamento</span>
                        <span class="text-sm font-black dark:text-white">{{ number_format($project->total_costs, 2) }}€</span>
                    </div>

                    {{-- BARRA DE PROGRESSO (Custo vs Budget) --}}
                    @php $percent = $project->budget > 0 ? ($project->total_costs / $project->budget) * 100 : 0; @endphp
                    <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full {{ $percent > 90 ? 'bg-red-500' : 'bg-emerald-500' }} transition-all duration-1000" style="width: {{ min($percent, 100) }}%"></div>
                    </div>

                    @if($project->pending_costs > 0)
                        <p class="text-[9px] font-bold text-amber-500 uppercase">⚠ {{ number_format($project->pending_costs, 2) }}€ em aprovação</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- 2. TABELA DE APROVAÇÕES PENDENTES --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Despesas a Aguardar Revisão</h2>
            <span class="bg-amber-500 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase">{{ $pendingExpenses->count() }} Pendentes</span>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column class="pl-8 text-center uppercase text-[9px] font-black text-zinc-400 w-32">Status</flux:table.column>
                <flux:table.column class="uppercase text-[9px] font-black text-zinc-400 w-40">Colaborador</flux:table.column>
                <flux:table.column class="uppercase text-[9px] font-black text-zinc-400">Descritivo</flux:table.column>
                <flux:table.column class="uppercase text-[9px] font-black text-zinc-400">Vínculo</flux:table.column>
                <flux:table.column class="text-right uppercase text-[9px] font-black text-zinc-400 w-32">Valor</flux:table.column>
                <flux:table.column class="pr-8 text-center uppercase text-[9px] font-black text-zinc-400 w-40">Decisão</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($pendingExpenses as $exp)
                    <flux:table.row>
                        <flux:table.cell class="text-center">
                            <span class="px-2 py-1 rounded text-[8px] font-black uppercase bg-amber-500/10 text-amber-600 border border-amber-500/20">Pendente</span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:avatar initials="{{ $exp->user->initials() }}" class="size-8" />
                                <span class="text-xs font-bold dark:text-white">{{ $exp->user->name }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-sm font-black dark:text-white uppercase">{{ $exp->description }}</span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex flex-col gap-1">
                                @if($exp->project) <span class="text-[9px] font-black text-brand-600 uppercase">PRJ: {{ $exp->project->name }}</span> @endif
                                @if($exp->task) <span class="text-[9px] font-bold text-zinc-400 uppercase">TRF: {{ $exp->task->title }}</span> @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="text-right">
                            <span class="text-lg font-black dark:text-white">{{ number_format($exp->amount, 2) }}€</span>
                        </flux:table.cell>

                        <flux:table.cell class="pr-8">
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="approve({{ $exp->id }})" variant="primary" size="sm" class="!bg-emerald-600 rounded-lg text-[9px] font-black uppercase">Aprovar</flux:button>
                                <flux:button wire:click="reject({{ $exp->id }})" variant="ghost" size="sm" class="text-red-500 rounded-lg text-[9px] font-black uppercase">Rejeitar</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        @if($pendingExpenses->isEmpty())
            <div class="py-20 text-center text-zinc-400 uppercase text-[10px] font-black tracking-widest italic">
                Tudo em dia! Nenhuma despesa pendente de aprovação.
            </div>
        @endif
    </div>
      {{-- 3. HISTÓRICO DE CUSTOS (AUDITORIA) --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between px-4">
            <h2 class="text-xl font-black dark:text-white uppercase italic tracking-tighter">Histórico de Custos</h2>

            {{-- BARRA DE FILTROS --}}
            <div class="flex gap-3">
                <flux:select wire:model.live="filterProject" class="w-48 text-[10px] font-bold uppercase">
                    <option value="">Todos os Projetos</option>
                    @foreach($projects as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                </flux:select>

                <flux:select wire:model.live="filterStatus" class="w-40 text-[10px] font-bold uppercase">
                    <option value="">Estado (Todos)</option>
                    <option value="aprovado">Aprovado</option>
                    <option value="rejeitado">Rejeitado</option>
                </flux:select>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="pl-8 text-center uppercase text-[9px] font-black text-zinc-400 w-32">Status</flux:table.column>
                    <flux:table.column class="uppercase text-[9px] font-black text-zinc-400 w-24">Data</flux:table.column>
                    <flux:table.column class="uppercase text-[9px] font-black text-zinc-400">Gasto / Categoria</flux:table.column>
                    <flux:table.column class="uppercase text-[9px] font-black text-zinc-400">Unidade Operacional</flux:table.column>
                    <flux:table.column class="uppercase text-[9px] font-black text-zinc-400">Responsável</flux:table.column>
                    <flux:table.column class="text-right uppercase text-[9px] font-black text-zinc-400 w-32">Valor</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($history as $exp)
                        <flux:table.row class="opacity-80 hover:opacity-100 transition-opacity">
                            <flux:table.cell class="text-center">
                                @if($exp->status === 'aprovado')
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Aprovado</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-red-500/10 text-red-600 border border-red-500/20">Rejeitado</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell class="text-xs font-bold text-zinc-500">
                                {{ $exp->spent_at->format('d/m/Y') }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $exp->description }}</span>
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase italic">{{ $exp->category?->name }}</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-col gap-1">
                                    @if($exp->project) <span class="text-[9px] font-black text-brand-600 uppercase">📁 {{ $exp->project->name }}</span> @endif
                                    @if($exp->task) <span class="text-[9px] font-bold text-zinc-500 uppercase italic">↳ {{ $exp->task->title }}</span> @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <div class="size-5 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[8px] font-black">{{ $exp->user->initials() }}</div>
                                    <span class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400">{{ explode(' ', $exp->user->name)[0] }}</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                <span class="text-lg font-black dark:text-white tabular-nums">{{ number_format($exp->amount, 2, ',', ' ') }}€</span>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="p-6 bg-zinc-50/50 dark:bg-zinc-950/50">
                {{ $history->links() }}
            </div>
        </div>
    </div>
</div>


<div class="space-y-10 pb-20">
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="p-5 bg-zinc-950 dark:bg-brand-600 rounded-[2rem] shadow-xl text-white">
                <flux:icon name="presentation-chart-line" class="w-10 h-10" />
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Análise de Custos</h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorização de rentabilidade por projeto e tarefa</p>
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Aprovado --}}
        <div class="bg-white dark:bg-zinc-900 border border-emerald-200 dark:border-emerald-900/30 rounded-[2rem] shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Total Aprovado</span>
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <flux:icon name="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tighter">{{ number_format($totalApproved, 2, ',', ' ') }}€</h3>
            <p class="text-[9px] text-zinc-500 font-bold mt-2">Despesas processadas</p>
        </div>

        {{-- Total Pendente --}}
        <div class="bg-white dark:bg-zinc-900 border border-amber-200 dark:border-amber-900/30 rounded-[2rem] shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[9px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">Aguardando</span>
                <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon name="exclamation-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
            <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tighter">{{ number_format($totalPending, 2, ',', ' ') }}€</h3>
            <p class="text-[9px] text-zinc-500 font-bold mt-2">{{ $pendingExpenses->count() }} Despesas</p>
        </div>

        {{-- Total Rejeitado --}}
        <div class="bg-white dark:bg-zinc-900 border border-red-200 dark:border-red-900/30 rounded-[2rem] shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[9px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest">Rejeitado</span>
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <flux:icon name="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400" />
                </div>
            </div>
            <h3 class="text-3xl font-black text-red-600 dark:text-red-400 tracking-tighter">{{ number_format($totalRejected, 2, ',', ' ') }}€</h3>
            <p class="text-[9px] text-zinc-500 font-bold mt-2">Não processadas</p>
        </div>

        {{-- Taxa de Aprovação --}}
        <div class="bg-white dark:bg-zinc-900 border border-brand-200 dark:border-brand-900/30 rounded-[2rem] shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[9px] font-black text-brand-600 dark:text-brand-400 uppercase tracking-widest">Taxa</span>
                <div class="p-2 bg-brand-100 dark:bg-brand-900/20 rounded-lg">
                    <flux:icon name="chart-bar" class="w-5 h-5 text-brand-600 dark:text-brand-400" />
                </div>
            </div>
            <h3 class="text-3xl font-black text-brand-600 dark:text-brand-400 tracking-tighter">{{ $approvalRate }}%</h3>
            <p class="text-[9px] text-zinc-500 font-bold mt-2">De aprovação</p>
        </div>
    </div>

    {{-- 1. GRID DE PROJETOS (RENTABILIDADE) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:shadow-md hover:border-brand-200 dark:hover:border-brand-800/50 transition-all flex flex-col justify-between group">
                <div class="mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400 text-[8px] font-black uppercase tracking-widest">📊 Ativo</span>
                        <p class="text-[9px] font-bold text-zinc-500 dark:text-zinc-400">Orçamento: {{ number_format($project->budget, 2) }}€</p>
                    </div>
                    <h3 class="text-xl font-black dark:text-white uppercase tracking-tight group-hover:text-brand-600 transition-colors">{{ $project->name }}</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Consumo do Orçamento</span>
                            <span class="text-sm font-black dark:text-white">{{ number_format($project->total_costs, 2) }}€</span>
                        </div>

                        {{-- BARRA DE PROGRESSO (Custo vs Budget) --}}
                        @php
                            $percent = $project->budget > 0 ? ($project->total_costs / $project->budget) * 100 : 0;
                            $barColor = $percent > 90 ? 'bg-red-500' : ($percent > 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <div class="h-2.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700">
                            <div class="h-full {{ $barColor }} transition-all duration-1000 shadow-[0_0_8px_rgba(59,130,246,0.3)]" style="width: {{ min($percent, 100) }}%"></div>
                        </div>
                        <span class="text-[8px] text-zinc-500 font-bold mt-2 block">{{ number_format($percent, 0) }}% utilizado</span>
                    </div>

                    @if($project->pending_costs > 0)
                        <div class="px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900/50 rounded-lg">
                            <p class="text-[8px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-widest">⚠ Aguardando Aprovação</p>
                            <p class="text-sm font-black text-amber-600 dark:text-amber-400">{{ number_format($project->pending_costs, 2) }}€</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center px-4">
                <div class="flex justify-center mb-4">
                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-full">
                        <flux:icon name="briefcase" class="w-12 h-12 text-zinc-400 dark:text-zinc-600" />
                    </div>
                </div>
                <p class="text-lg font-black dark:text-white uppercase tracking-tight mb-2">Nenhum Projeto</p>
                <p class="text-sm text-zinc-500 mb-6">Crie um projeto para começar a monitorizar custos</p>
                <a href="{{ route('hub.business.projects') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-2 bg-brand-600 text-white text-xs font-black uppercase rounded-lg hover:bg-brand-700 transition-colors">
                    <flux:icon name="plus" class="w-4 h-4" />
                    Novo Projeto
                </a>
            </div>
        @endforelse
    </div>

    {{-- 2. TABELA DE APROVAÇÕES PENDENTES --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="text-lg font-black uppercase tracking-tight text-zinc-700 dark:text-white">Despesas a Aguardar Revisão</h2>
            <span class="bg-amber-500 text-white text-[10px] font-black px-4 py-2 rounded-full uppercase w-fit">{{ $pendingExpenses->count() }} Pendentes</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="w-1/6 px-6 py-4 text-center uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="w-1/5 px-6 py-4 uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Colaborador</th>
                        <th class="w-1/4 px-6 py-4 uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Descritivo</th>
                        <th class="w-1/6 px-6 py-4 uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Vínculo</th>
                        <th class="w-1/12 px-6 py-4 text-right uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Valor</th>
                        <th class="w-1/6 px-6 py-4 text-center uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Decisão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @foreach($pendingExpenses as $exp)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="w-1/6 px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 rounded-lg text-[8px] font-black uppercase bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 dark:border-amber-500/30 whitespace-nowrap">Pendente</span>
                            </td>

                            <td class="w-1/5 px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar initials="{{ $exp->user->initials() }}" class="size-8 flex-shrink-0" />
                                    <span class="text-xs font-bold dark:text-white truncate">{{ $exp->user->name }}</span>
                                </div>
                            </td>

                            <td class="w-1/4 px-6 py-4">
                                <span class="text-xs font-black dark:text-white uppercase truncate block">{{ $exp->description }}</span>
                            </td>

                            <td class="w-1/6 px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($exp->project) <span class="text-[8px] font-black text-brand-600 dark:text-brand-400 uppercase truncate">{{ $exp->project->name }}</span> @endif
                                    @if($exp->task) <span class="text-[8px] font-bold text-zinc-500 dark:text-zinc-400 uppercase truncate">{{ $exp->task->title }}</span> @endif
                                </div>
                            </td>

                            <td class="w-1/12 px-6 py-4 text-right">
                                <span class="text-sm font-black dark:text-white whitespace-nowrap">{{ number_format($exp->amount, 2, ',', ' ') }}€</span>
                            </td>

                            <td class="w-1/6 px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <flux:button wire:click="approve({{ $exp->id }})" variant="primary" size="sm" class="!bg-emerald-600 dark:!bg-emerald-700 rounded-lg text-[8px] font-black uppercase whitespace-nowrap">Aprovar</flux:button>
                                    <flux:button wire:click="reject({{ $exp->id }})" variant="ghost" size="sm" class="text-red-500 dark:text-red-400 rounded-lg text-[8px] font-black uppercase whitespace-nowrap">Rejeitar</flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($pendingExpenses->isEmpty())
            <div class="py-20 px-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="p-4 bg-emerald-100 dark:bg-emerald-900/20 rounded-full">
                        <flux:icon name="check-circle" class="w-12 h-12 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <p class="text-lg font-black dark:text-white uppercase tracking-tight mb-2">Tudo Em Dia!</p>
                <p class="text-sm text-zinc-500 italic mb-6">Nenhuma despesa pendente de aprovação. Excelente trabalho!</p>
                <a href="{{ route('expenses.create') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-2 bg-brand-600 text-white text-xs font-black uppercase rounded-lg hover:bg-brand-700 transition-colors">
                    <flux:icon name="plus" class="w-4 h-4" />
                    Nova Despesa
                </a>
            </div>
        @endif
    </div>
    {{-- 3. HISTÓRICO DE CUSTOS (AUDITORIA) --}}
    <div class="space-y-4">
        <div class="px-4">
            <h2 class="text-lg font-black dark:text-white uppercase tracking-tight mb-6">Histórico de Custos</h2>

            {{-- BARRA DE FILTROS --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full">
                <flux:input
                    wire:model.live.debounce.300ms="historySearch"
                    icon="magnifying-glass"
                    placeholder="Pesquisar por descrição..."
                    class="flex-1 !rounded-xl border-zinc-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 text-[10px]"
                />

                <flux:select wire:model.live="filterProject" class="sm:w-48 text-[10px] font-bold uppercase rounded-xl">
                    <option value="">Todos os Projetos</option>
                    @foreach($projects as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                </flux:select>

                <flux:select wire:model.live="filterStatus" class="sm:w-40 text-[10px] font-bold uppercase rounded-xl">
                    <option value="">Estado (Todos)</option>
                    <option value="aprovado">Aprovado</option>
                    <option value="rejeitado">Rejeitado</option>
                </flux:select>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="w-1/12 px-6 py-4 text-center uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Status</th>
                            <th class="w-1/12 px-6 py-4 uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Data</th>
                            <th class="w-1/4 px-6 py-4 uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Gasto / Categoria</th>
                            <th class="w-1/5 px-6 py-4 uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Unidade Operacional</th>
                            <th class="w-1/6 px-6 py-4 uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Responsável</th>
                            <th class="w-1/8 px-6 py-4 text-right uppercase text-[9px] font-black text-zinc-500 dark:text-zinc-400">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                        @forelse($history as $exp)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="w-1/12 px-6 py-4 text-center">
                                    @if($exp->status === 'aprovado')
                                        <span class="inline-block px-3 py-1 rounded-lg text-[8px] font-black uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 dark:border-emerald-500/30 whitespace-nowrap">✓ Aprovado</span>
                                    @else
                                        <span class="inline-block px-3 py-1 rounded-lg text-[8px] font-black uppercase bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 dark:border-red-500/30 whitespace-nowrap">✕ Rejeitado</span>
                                    @endif
                                </td>

                                <td class="w-1/12 px-6 py-4 text-xs font-bold text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $exp->spent_at->format('d/m/Y') }}
                                </td>

                                <td class="w-1/4 px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-black dark:text-white uppercase truncate">{{ $exp->description }}</span>
                                        <span class="text-[8px] font-bold text-zinc-500 dark:text-zinc-400 uppercase italic truncate">{{ $exp->category?->name }}</span>
                                    </div>
                                </td>

                                <td class="w-1/5 px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        @if($exp->project) <span class="text-[8px] font-black text-brand-600 dark:text-brand-400 uppercase truncate">{{ $exp->project->name }}</span> @endif
                                        @if($exp->task) <span class="text-[8px] font-bold text-zinc-500 dark:text-zinc-400 uppercase truncate">{{ $exp->task->title }}</span> @endif
                                    </div>
                                </td>

                                <td class="w-1/6 px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="size-6 rounded-full bg-brand-600 flex items-center justify-center text-[8px] font-black text-white flex-shrink-0">{{ $exp->user->initials() }}</div>
                                        <span class="text-[9px] font-bold text-zinc-600 dark:text-zinc-400 truncate">{{ explode(' ', $exp->user->name)[0] }}</span>
                                    </div>
                                </td>

                                <td class="w-1/8 px-6 py-4 text-right">
                                    <span class="text-sm font-black dark:text-white tabular-nums whitespace-nowrap">{{ number_format($exp->amount, 2, ',', ' ') }}€</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-full">
                                            <flux:icon name="inbox" class="w-10 h-10 text-zinc-400 dark:text-zinc-600" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-black dark:text-white uppercase tracking-tight">Sem Resultados</p>
                                            <p class="text-xs text-zinc-500 mt-1">Nenhuma despesa corresponde aos seus filtros</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($history->count() > 0)
            <div class="px-6 py-4 bg-zinc-50/50 dark:bg-zinc-950/50 border-t border-zinc-100 dark:border-zinc-800 overflow-x-auto">
                {{ $history->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

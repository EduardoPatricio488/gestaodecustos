<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE PROJETOS (ESTILO PREMIUM SaaS) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 text-brand-600">
                        <flux:icon name="briefcase" class="w-10 h-10" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Gestão de Projetos</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Operações Ativas</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Controlo de rentabilidade, orçamentos e prazos por unidade de trabalho</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="relative flex-1 min-w-[240px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar projeto..." class="!bg-transparent border-none shadow-none" />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:modal.trigger name="project-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Novo Projeto
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- 2. KPIs OPERACIONAIS (PIPELINE ANALYTICS COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Pipeline Total (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400">
                        <flux:icon name="presentation-chart-line" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest">Valor em Carteira</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Pipeline Total (Budgets)</p>
                <h3 class="text-4xl font-black text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($totalBudget, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
        </div>

        {{-- Projetos Ativos --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-brand-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-2xl text-zinc-500">
                    <flux:icon name="briefcase" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Estado de Execução</p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $activeCount }}
                </span>
                <span class="text-xs text-zinc-400 uppercase font-bold ml-2 tracking-widest italic">Trabalhos</span>
            </h3>
        </div>

        {{-- Rentabilidade Média --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-emerald-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="bolt" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Margem de Lucro Média</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ round($projects->avg('margin')) }}%
                </span>
            </h3>
            <div class="mt-4 h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]" style="width: {{ $projects->avg('margin') }}%"></div>
            </div>
        </div>
    </div>

    {{-- 3. GRELHA DE OPERAÇÕES (ESTILO SaaS DASHBOARD) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($projects as $project)
            @php
                $margin = min(max($project->margin, 0), 100);
                $isProfitable = $project->profit >= 0;
            @endphp

            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-brand-500/30 transition-all duration-300 group overflow-hidden relative">

                {{-- Cabeçalho do Card --}}
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <span class="inline-flex px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 font-black uppercase text-[8px] tracking-widest rounded-lg border border-zinc-200 dark:border-zinc-700 mb-3">
                            {{ $project->status }}
                        </span>
                        <h3 class="text-xl font-black dark:text-white uppercase tracking-tight leading-none group-hover:text-brand-600 transition-colors">
                            {{ $project->name }}
                        </h3>
                    </div>

                    <flux:dropdown>
                        <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" class="rounded-xl" />
                        <flux:menu class="min-w-[180px] p-2">
                            <flux:menu.item wire:click="edit({{ $project->id }})" icon="pencil-square" class="font-bold text-xs">Editar Projeto</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item wire:click="delete({{ $project->id }})" wire:confirm="Apagar projeto e dados financeiros associados?" variant="danger" icon="trash" class="font-bold text-xs">Eliminar Operação</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                {{-- Dashboard de Rentabilidade (Neon Style) --}}
                <div class="space-y-5">
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Eficiência de Margem</span>
                            <span class="text-xs font-bold {{ $isProfitable ? 'text-emerald-500' : 'text-red-500' }} mt-1">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                    {{ number_format($project->profit, 2, ',', ' ') }} € Lucro
                                </span>
                            </span>
                        </div>
                        <span class="text-2xl font-black {{ $isProfitable ? 'text-emerald-500' : 'text-red-500' }} tracking-tighter italic">
                            {{ round($project->margin) }}%
                        </span>
                    </div>

                    {{-- Barra de Progresso Neon --}}
                    <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-inner">
                        <div class="h-full {{ $isProfitable ? 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)]' : 'bg-red-500 shadow-[0_0_12px_rgba(239,68,68,0.5)]' }} transition-all duration-1000 ease-out"
                             style="width: {{ $margin }}%">
                        </div>
                    </div>

                    {{-- Detalhes de Fluxo (Gasto vs Ganho) --}}
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase mb-1">Custo Total</p>
                            <p class="text-sm font-black text-red-500">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                    -{{ number_format($project->costs, 2, ',', ' ') }} €
                                </span>
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase mb-1">Receita Gerada</p>
                            <p class="text-sm font-black text-emerald-600">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                    +{{ number_format($project->revenue, 2, ',', ' ') }} €
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Rodapé: Deadline --}}
                @if($project->deadline)
                    <div class="mt-8 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="calendar" class="size-3 text-zinc-400" />
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Entrega Final:</span>
                        </div>
                        <span class="text-[10px] font-black dark:text-zinc-300 uppercase {{ \Carbon\Carbon::parse($project->deadline)->isPast() ? 'text-red-500' : '' }}">
                            {{ \Carbon\Carbon::parse($project->deadline)->translatedFormat('d M, Y') }}
                        </span>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                        <flux:icon name="presentation-chart-bar" class="size-12 text-zinc-200 dark:text-zinc-700" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Pipeline Vazio</p>
                        <p class="text-zinc-400 text-xs italic font-medium">Cria o teu primeiro projeto para começar a monitorizar margens.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 4. MODAL: GESTÃO ESTRATÉGICA DE PROJETO (DESIGN SaaS PRO) --}}
    <flux:modal name="project-modal" position="center" class="md:w-[650px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-900 dark:bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="briefcase" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Configurações do Projeto' : 'Nova Unidade de Trabalho' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Define os parâmetros operacionais e financeiros do projeto.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- SECÇÃO: IDENTIDADE DO TRABALHO --}}
                <div class="space-y-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome do Projeto</flux:label>
                        <flux:input
                            wire:model="name"
                            placeholder="Ex: Refatoração de Sistema Q4"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Âmbito e Observações Técnicas</flux:label>
                        <flux:textarea
                            wire:model="description"
                            rows="2"
                            placeholder="Descreve os objetivos principais deste trabalho..."
                            class="rounded-2xl shadow-sm border-none !bg-zinc-50 dark:!bg-zinc-900 text-sm p-4"
                        />
                    </div>
                </div>

                {{-- SECÇÃO: CRONOGRAMA E BUDGET (PAINEL DE DESTAQUE) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2">
                        <flux:icon name="presentation-chart-line" class="size-3 text-brand-500" />
                        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">Planeamento de Recursos</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest text-center">Budget (€)</flux:label>
                            <flux:input
                                wire:model="budget"
                                type="number"
                                step="0.01"
                                class="font-black text-xl text-center text-brand-600 !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                                placeholder="0,00"
                            />
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest text-center">Data Início</flux:label>
                            <flux:input wire:model="start_date" type="date" class="font-bold !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm" />
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest text-center">Entrega Final</flux:label>
                            <flux:input wire:model="deadline" type="date" class="font-bold !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Fase de Execução</flux:label>
                        <flux:select wire:model="status" class="font-black uppercase text-xs !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                            <option value="planeamento">📝 Fase de Planeamento</option>
                            <option value="em_curso">⚡ Em Execução Ativa</option>
                            <option value="pausado">⏸️ Operação Suspensa</option>
                            <option value="concluido">✅ Trabalho Finalizado</option>
                        </flux:select>
                    </div>
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Projeto' : 'Iniciar Operação' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Gestão de Projetos
        </p>
    </footer>
</div>

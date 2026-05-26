<div class="space-y-10 pb-20">
    {{-- 1. HEADER DE OPERAÇÕES (ESTILO PREMIUM SaaS) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="command-line" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Timeline de Fluxo</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Workflow</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorização de estados e missões do grupo</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="relative flex-1 min-w-[220px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar missão..." class="!bg-transparent border-none shadow-none" />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:select wire:model.live="activeProjectId" class="w-full md:w-52 font-bold uppercase text-[10px] !bg-transparent border-none shadow-none" placeholder="Todos os Projetos">
                    <option value="">Todas as Unidades</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    {{-- 2. QUADRO KANBAN (LARGURA OPTIMIZADA - 320px) --}}
    <div class="relative -mx-4 sm:-mx-6 lg:-mx-8">
        <div class="flex gap-6 overflow-x-auto pb-12 pt-4 px-8 no-scrollbar items-start custom-horizontal-scroll">

            {{-- COLUNA 1: BACKLOG --}}
            <div class="flex-shrink-0 w-[320px] space-y-6">
                <div class="flex justify-between items-center px-6 py-4 bg-zinc-100/80 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200 dark:border-zinc-700 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="size-2 rounded-full bg-zinc-400"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">A Aguardar</span>
                    </div>
                    <flux:badge variant="neutral" size="sm" class="font-black bg-white dark:bg-zinc-800">{{ $pendingTasks->count() }}</flux:badge>
                </div>

                <div class="space-y-4">
                    @foreach($pendingTasks as $task)
                        <div class="glass-card p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm hover:border-brand-500 transition-all duration-300 group">
                            <div class="flex justify-between items-start mb-3">
                                <flux:badge :variant="$task->priority_color" size="sm" class="text-[7px] font-black uppercase px-2 border-none">{{ $task->priority }}</flux:badge>
                                <button wire:click="updateTaskStatus({{ $task->id }}, 'em_curso')" class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-xl text-zinc-400 hover:bg-brand-500 hover:text-white transition-all shadow-inner">
                                    <flux:icon name="play" variant="solid" class="size-3" />
                                </button>
                            </div>
                            <h4 class="text-sm font-black dark:text-white uppercase leading-snug mb-5 line-clamp-2">{{ $task->title }}</h4>
                            <div class="flex justify-between items-center pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                <span class="text-[8px] font-black text-zinc-400 uppercase tracking-tighter truncate max-w-[120px] italic">{{ $task->project->name }}</span>
                                <span class="text-[9px] font-bold {{ $task->isOverdue() ? 'text-red-500' : 'text-zinc-500' }}">
                                    {{ $task->due_date ? $task->due_date->format('d M') : 'S/ P' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA 2: EM EXECUÇÃO --}}
            <div class="flex-shrink-0 w-[320px] space-y-6">
                <div class="flex justify-between items-center px-6 py-4 bg-brand-500/10 rounded-2xl border border-brand-500/20 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="size-2 rounded-full bg-brand-500 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-600">Em Curso</span>
                    </div>
                    <flux:badge variant="success" size="sm" class="bg-brand-600 text-white font-black">{{ $inProgressTasks->count() }}</flux:badge>
                </div>

                <div class="space-y-4">
                    @foreach($inProgressTasks as $task)
                        <div class="glass-card p-6 bg-white dark:bg-zinc-900 border-l-4 border-l-brand-600 border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-xl relative group">
                            <h4 class="text-sm font-black dark:text-white uppercase leading-snug mb-8 line-clamp-2">{{ $task->title }}</h4>
                            <div class="flex justify-between items-end">
                                <div class="flex items-center gap-2">
                                    <div class="size-8 rounded-xl bg-brand-600 text-white flex items-center justify-center text-[10px] font-black shadow-lg">
                                        {{ substr($task->assignee->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-[9px] font-black dark:text-white uppercase tracking-tighter truncate max-w-[80px]">{{ explode(' ', $task->assignee->name ?? 'Pendente')[0] }}</span>
                                </div>
                                <button wire:click="updateTaskStatus({{ $task->id }}, 'concluida')" class="px-4 py-2 bg-zinc-950 dark:bg-brand-600 text-white text-[9px] font-black uppercase rounded-xl hover:scale-105 transition-all">
                                    Finalizar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA 3: CONCLUÍDO --}}
            <div class="flex-shrink-0 w-[320px] space-y-6">
                <div class="flex justify-between items-center px-6 py-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-emerald-500" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Finalizadas</span>
                    </div>
                </div>

                <div class="space-y-4 opacity-70 hover:opacity-100 transition-all">
                    @foreach($completedTasks as $task)
                        <div class="glass-card p-5 bg-zinc-50/50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-[2rem]">
                            <div class="flex items-center gap-3 mb-3">
                                 <flux:icon name="check" class="size-3 text-emerald-600" />
                                 <h4 class="text-xs font-bold text-zinc-500 line-through uppercase truncate">{{ $task->title }}</h4>
                            </div>
                            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-700/50 flex justify-between items-center text-[8px] font-black text-emerald-600 uppercase">
                                <span>Arquivada em {{ $task->completed_at ? $task->completed_at->format('d/m') : '---' }}</span>
                                <flux:icon name="archive-box" class="size-3 text-zinc-400" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ESPAÇADOR DE SEGURANÇA FINAL (Crucial para não cortar a última coluna) --}}
            <div class="flex-shrink-0 w-32 h-10"></div>
        </div>
    </div>

    {{-- 3. LEGENDA DE PRIORIDADES (ESTILO SaaS DASHBOARD) --}}
    <div class="flex flex-wrap justify-center gap-x-10 gap-y-4 pt-10 border-t border-zinc-100 dark:border-zinc-800 mt-10">
        <div class="flex items-center gap-3">
            <div class="size-2.5 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Missão Crítica</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="size-2.5 rounded-full bg-orange-500 shadow-[0_0_10px_rgba(249,115,22,0.5)]"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Prioridade Alta</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="size-2.5 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Fluxo Normal</span>
        </div>
        <div class="flex items-center gap-3 opacity-50">
            <div class="size-2.5 rounded-full bg-zinc-400"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Baixa Prioridade</span>
        </div>
    </div>

    {{-- 4. RODAPÉ DE OPERAÇÕES --}}
    <footer class="mt-20 pb-6 flex flex-col md:flex-row justify-between items-center gap-4 opacity-50">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ auth()->user()->currentWorkspace->name }} · Protocolo Kanban Enterprise
        </p>
        <div class="flex items-center gap-4">
            <span class="text-[9px] font-black text-brand-600 uppercase tracking-widest">Sincronização Ativa</span>
            <div class="h-3 w-px bg-zinc-300 dark:bg-zinc-700"></div>
            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Ops Engine v2.0</span>
        </div>
    </footer>

    {{-- 5. ESTILOS DE INTERFACE (SCROLLBARS & KANBAN FX) --}}
    <style>
        /* Scrollbar Horizontal Principal (Fina e Discreta) */
        .custom-horizontal-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .custom-horizontal-scroll::-webkit-scrollbar-track {
            background: transparent;
            margin: 0 40px; /* Dá espaço nas extremidades */
        }

        .custom-horizontal-scroll::-webkit-scrollbar-thumb {
            background: #e4e4e7; /* zinc-200 */
            border-radius: 20px;
        }

        .dark .custom-horizontal-scroll::-webkit-scrollbar-thumb {
            background: #27272a; /* zinc-800 */
        }

        .custom-horizontal-scroll::-webkit-scrollbar-thumb:hover {
            background: #3b82f6; /* brand-500 */
        }

        /* Animação de entrada suave para os cards */
        .glass-card {
            animation: cardAppear 0.4s ease-out forwards;
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Garantir que o scroll horizontal ignore o limite do container pai */
        .custom-horizontal-scroll {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        /* Desativar scrollbars indesejadas */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>

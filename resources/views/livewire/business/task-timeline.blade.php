<div x-data="{ showTaskModal: false, activeTask: null }" class="space-y-10 pb-20">

    {{-- 1. HEADER DE OPERAÇÕES --}}
    <div class="relative text-left">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="command-line" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black uppercase tracking-tighter italic leading-none dark:text-white text-zinc-900">
                            Timeline de Fluxo
                        </h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest text-zinc-500">
                            Workflow
                        </flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">
                        Monitorização de estados e missões do grupo
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm w-full lg:w-auto">
                <div class="relative flex-1 min-w-[220px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass"
                        placeholder="Procurar missão..." class="!bg-transparent border-none shadow-none" />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:select wire:model.live="activeProjectId"
                    class="w-full md:w-52 font-black uppercase text-[10px] !bg-transparent border-none shadow-none">
                    <option value="">Todas as Unidades</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    {{-- 2. QUADRO KANBAN --}}
    <div class="relative -mx-4 sm:-mx-6 lg:-mx-8">
        <div class="flex gap-6 overflow-x-auto pb-12 pt-4 px-8 no-scrollbar items-start custom-horizontal-scroll">

            {{-- COLUNA 1: A AGUARDAR --}}
            <div class="flex-shrink-0 w-[320px] space-y-6">
                <div class="flex justify-between items-center px-6 py-4 bg-zinc-100/80 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center gap-3 text-left">
                        <div class="size-2 rounded-full bg-zinc-400"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">A Aguardar</span>
                    </div>
                    <flux:badge variant="neutral" size="sm" class="font-black bg-white dark:bg-zinc-800 text-zinc-500 shadow-sm">
                        {{ $pendingTasks->count() }}
                    </flux:badge>
                </div>

                <div class="space-y-4">
                    @foreach($pendingTasks as $task)
                        <div wire:key="pending-task-{{ $task->id }}" x-data="{ optionsOpen: false }"
                             class="glass-card p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm relative group">

                            {{-- MENU 3 PONTOS MANUAL --}}
                            <div class="absolute top-4 right-4 z-30">
                                <button type="button" @click.stop="optionsOpen = !optionsOpen"
                                        class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 opacity-0 group-hover:opacity-100">
                                    <flux:icon name="ellipsis-horizontal" class="size-4" />
                                </button>

                                <div x-show="optionsOpen" x-cloak @click.outside="optionsOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute right-0 top-8 w-44 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-2xl z-50 overflow-hidden text-left">
                                    <div class="p-1.5 space-y-0.5">
                                        <button type="button" wire:click="openTask({{ $task->id }})" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300 hover:bg-brand-50 hover:text-brand-600 transition-all">
                                            <flux:icon name="eye" class="size-3.5" /> Ver Detalhes
                                        </button>
                                        <button type="button" wire:click="deleteTask({{ $task->id }})" wire:confirm="Eliminar missão?" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[10px] font-black uppercase text-red-500 hover:bg-red-50 transition-all">
                                            <flux:icon name="trash" class="size-3.5" /> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
<div class="flex justify-between items-start mb-4">
    <flux:badge :variant="$task->priority_color" size="sm"
        class="text-[7px] font-black uppercase px-2 border-none">
        {{ $task->priority }}
    </flux:badge>
</div>

{{-- BOTÃO PLAY CENTRALIZADO E VERDE --}}
<div class="flex justify-center mb-6">
    <button wire:click="updateTaskStatus({{ $task->id }}, 'em_curso')"
        class="size-14 flex items-center justify-center bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full hover:bg-emerald-600 hover:text-white transition-all shadow-lg group/play">
        <flux:icon name="play" variant="solid" class="size-6 group-hover/play:scale-110 transition-transform ml-1" />
    </button>
</div>

                            <h4 class="text-sm font-black dark:text-white uppercase leading-snug mb-5 line-clamp-2 text-left">
                                {{ $task->title }}
                            </h4>

                            <div class="flex justify-between items-center pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                <span class="text-[8px] font-black text-zinc-400 uppercase tracking-tighter truncate max-w-[120px] italic">
                                    {{ $task->project->name }}
                                </span>
                                <span class="text-[9px] font-bold {{ $task->isOverdue() ? 'text-red-500' : 'text-zinc-500' }}">
                                    {{ $task->due_date ? $task->due_date->format('d M') : 'S/ P' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA 2: EM CURSO --}}
            <div class="flex-shrink-0 w-[320px] space-y-6">
                <div class="flex justify-between items-center px-6 py-4 bg-brand-500/10 rounded-2xl border border-brand-500/20">
                    <div class="flex items-center gap-3 text-left">
                        <div class="size-2 rounded-full bg-brand-500 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-600">Em Curso</span>
                    </div>
                    <flux:badge variant="success" size="sm" class="bg-brand-600 text-white font-black shadow-sm">
                        {{ $inProgressTasks->count() }}
                    </flux:badge>
                </div>

                <div class="space-y-4">
                    @foreach($inProgressTasks as $task)
                        <div wire:key="progress-task-{{ $task->id }}" x-data="{ optionsOpen: false }"
                             class="glass-card p-6 bg-white dark:bg-zinc-900 border-l-4 border-l-brand-600 rounded-[2rem] shadow-xl relative group">

                            {{-- MENU 3 PONTOS MANUAL --}}
                            <div class="absolute top-4 right-4 z-30">
                                <button type="button" @click.stop="optionsOpen = !optionsOpen"
                                        class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 opacity-0 group-hover:opacity-100">
                                    <flux:icon name="ellipsis-horizontal" class="size-4" />
                                </button>
                                <div x-show="optionsOpen" x-cloak @click.outside="optionsOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute right-0 top-8 w-44 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-2xl z-50 text-left">
                                    <div class="p-1.5 space-y-0.5">
                                        <button type="button" wire:click="openTask({{ $task->id }})" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300 hover:bg-brand-50 hover:text-brand-600 transition-all">
                                            <flux:icon name="eye" class="size-3.5" /> Ver Detalhes
                                        </button>
                                        <button type="button" wire:click="updateTaskStatus({{ $task->id }}, 'pendente')" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 transition-all">
                                            <flux:icon name="arrow-uturn-left" class="size-3.5" /> Recuar Estado
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <h4 class="text-sm font-black dark:text-white uppercase leading-snug mb-8 line-clamp-2 text-left">
                                {{ $task->title }}
                            </h4>

                            <div class="flex justify-between items-end">
                                <div class="flex items-center gap-2">
                                    <div class="size-8 rounded-xl bg-brand-600 text-white flex items-center justify-center text-[10px] font-black shadow-lg">
                                        {{ substr($task->assignee->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-[9px] font-black dark:text-white uppercase tracking-tighter truncate max-w-[80px]">
                                        {{ explode(' ', $task->assignee->name ?? 'Pendente')[0] }}
                                    </span>
                                </div>

                                <button wire:click="updateTaskStatus({{ $task->id }}, 'concluida')"
                                    class="px-4 py-2 bg-zinc-950 dark:bg-brand-600 text-white text-[9px] font-black uppercase rounded-xl hover:scale-105 transition-all shadow-md">
                                    Finalizar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COLUNA 3: FINALIZADAS --}}
            <div class="flex-shrink-0 w-[320px] space-y-6">
                <div class="flex justify-between items-center px-6 py-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20">
                    <div class="flex items-center gap-3 text-left">
                        <flux:icon name="check-circle" variant="solid" class="size-4 text-emerald-500" />
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Finalizadas</span>
                    </div>
                </div>

                <div class="space-y-4 opacity-70 hover:opacity-100 transition-all">
                    @foreach($completedTasks as $task)
                        <div wire:key="done-task-{{ $task->id }}" x-data="{ optionsOpen: false }"
                             class="glass-card p-5 bg-zinc-50/50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] relative group">

                            {{-- MENU 3 PONTOS MANUAL --}}
                            <div class="absolute top-4 right-4 z-30">
                                <button type="button" @click.stop="optionsOpen = !optionsOpen"
                                        class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 opacity-0 group-hover:opacity-100">
                                    <flux:icon name="ellipsis-horizontal" class="size-4" />
                                </button>
                                <div x-show="optionsOpen" x-cloak @click.outside="optionsOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute right-0 top-8 w-44 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-2xl z-50 text-left">
                                    <div class="p-1.5 space-y-0.5">
                                        <button type="button" wire:click="openTask({{ $task->id }})" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 transition-all">
                                            <flux:icon name="eye" class="size-3.5" /> Ver Detalhes
                                        </button>
                                        <button type="button" wire:click="updateTaskStatus({{ $task->id }}, 'em_curso')" @click="optionsOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[10px] font-black uppercase text-brand-600 hover:bg-brand-50 transition-all">
                                            <flux:icon name="arrow-path" class="size-3.5" /> Reativar Missão
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 mb-3 text-left">
                                <flux:icon name="check" class="size-3 text-emerald-600" />
                                <h4 class="text-xs font-bold text-zinc-500 line-through uppercase truncate">
                                    {{ $task->title }}
                                </h4>
                            </div>

                            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-700/50 flex justify-between items-center text-[8px] font-black text-emerald-600 uppercase">
                                <span>Arquivada em {{ $task->completed_at ? $task->completed_at->format('d/m') : '---' }}</span>
                                <flux:icon name="archive-box" class="size-3 text-zinc-400" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex-shrink-0 w-32 h-10"></div>
        </div>
    </div>

    {{-- 3. LEGENDA --}}
    <div class="flex flex-wrap justify-center gap-x-10 gap-y-4 pt-10 border-t border-zinc-100 dark:border-zinc-800 mt-10">
        <div class="flex items-center gap-3 text-left">
            <div class="size-2.5 rounded-full bg-red-500"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Missão Crítica</span>
        </div>
        <div class="flex items-center gap-3 text-left">
            <div class="size-2.5 rounded-full bg-orange-500"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Prioridade Alta</span>
        </div>
        <div class="flex items-center gap-3 text-left">
            <div class="size-2.5 rounded-full bg-blue-500"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Fluxo Normal</span>
        </div>
        <div class="flex items-center gap-3 opacity-50 text-left">
            <div class="size-2.5 rounded-full bg-zinc-400"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Baixa Prioridade</span>
        </div>
    </div>

    {{-- 4. MODAL DE DETALHES --}}
    <flux:modal name="task-modal" position="center" class="md:w-[600px] !p-0 overflow-visible" wire:ignore.self>
        <div class="p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 border border-zinc-200 dark:border-zinc-800 text-left">

            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            @if($activeTask)
                <h2 class="text-2xl font-black uppercase tracking-tight dark:text-white leading-tight">
                    {{ $activeTask->title }}
                </h2>

                <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed italic">
                    "{{ $activeTask->description ?? 'Sem descrição disponível para esta missão.' }}"
                </p>

                <div class="grid grid-cols-2 gap-8 pt-8 border-t border-zinc-200 dark:border-zinc-800">
                    <div>
                        <p class="text-[9px] uppercase font-black text-zinc-400 mb-1 tracking-widest">Unidade de Negócio</p>
                        <p class="text-sm font-black dark:text-zinc-200 uppercase">{{ $activeTask->project->name }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase font-black text-zinc-400 mb-1 tracking-widest">Responsável</p>
                        <p class="text-sm font-black dark:text-zinc-200 uppercase">{{ $activeTask->assignee->name ?? 'Pendente' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase font-black text-zinc-400 mb-1 tracking-widest">Prioridade</p>
                        <p class="text-sm font-black uppercase {{ $activeTask->priority === 'critica' ? 'text-red-500' : 'text-brand-500' }}">
                            {{ $activeTask->priority }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase font-black text-zinc-400 mb-1 tracking-widest">Prazo Final</p>
                        <p class="text-sm font-black dark:text-zinc-200">
                            {{ $activeTask->due_date ? $activeTask->due_date->format('d M, Y') : 'Sem Prazo' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>

    {{-- 5. RODAPÉ --}}
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

    {{-- 6. ESTILOS --}}
    <style>
        .custom-horizontal-scroll::-webkit-scrollbar { height: 6px; }
        .custom-horizontal-scroll::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 20px; }
        .dark .custom-horizontal-scroll::-webkit-scrollbar-thumb { background: #27272a; }
        .custom-horizontal-scroll::-webkit-scrollbar-thumb:hover { background: #3b82f6; }
.custom-horizontal-scroll {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>

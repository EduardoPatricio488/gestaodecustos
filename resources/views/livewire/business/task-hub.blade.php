
<div x-data="{ privacyMode: false }" class="space-y-10 pb-20">
    {{-- 1. HEADER DE PRODUTIVIDADE (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 text-brand-600">
                        <flux:icon name="clipboard-document-check" class="w-10 h-10" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                            Gestão de Tarefas
                        </h1>
                        <flux:badge variant="neutral"
                            class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">
                            Fluxo Operacional
                        </flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">
                        Controlo de missões, cronometragem de equipa e métricas de eficiência
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="task-modal">
                    <flux:button variant="primary" icon="plus"
                        class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Nova Missão
                    </flux:button>
                </flux:modal.trigger>

                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

                <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left"
                    wire:navigate title="Voltar" class="rounded-xl" />
            </div>
        </div>
    </div>

    {{-- 2. KPIs DE PERFORMANCE --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Pendentes --}}
        <div
            class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-brand-500/30">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">
                Missões em Aberto
            </p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    {{ $pendingCount }}
                </span>
            </h3>
        </div>

        {{-- Atrasadas --}}
        <div
            class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all {{ $overdueCount > 0 ? 'border-red-200 dark:border-red-900/30 hover:border-red-500' : 'hover:border-brand-500/30' }}">
            <p
                class="text-[10px] font-black {{ $overdueCount > 0 ? 'text-red-500 animate-pulse' : 'text-zinc-400' }} uppercase tracking-[0.2em] mb-1">
                Fora de Prazo
            </p>
            <h3
                class="text-4xl font-black {{ $overdueCount > 0 ? 'text-red-500' : 'text-zinc-900 dark:text-white' }} tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    {{ $overdueCount }}
                </span>
            </h3>
        </div>

        {{-- Taxa de Execução --}}
        <div
            class="lg:col-span-2 relative overflow-hidden bg-zinc-950 p-7 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all">
            </div>

            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex justify-between items-start">
                    <div>
                        <p
                            class="text-[10px] font-black text-brand-400 uppercase tracking-[0.2em] mb-1">
                            Taxa de Execução Global
                        </p>
                        <h3 class="text-4xl font-black text-white tracking-tighter italic">
                            {{ round($completionRate) }}%
                        </h3>
                    </div>
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400 shadow-inner">
                        <flux:icon name="bolt" variant="outline" class="size-6" />
                    </div>
                </div>

                <div class="mt-4 h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-brand-500 shadow-[0_0_10px_#3b82f6] transition-all duration-1000"
                        style="width: {{ $completionRate }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. BARRA DE FILTRAGEM --}}
    <div
        class="glass-card p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl flex flex-col md:flex-row gap-4 shadow-sm items-center">
        <div class="relative flex-1 w-full">
            <flux:input wire:model.live="search" icon="magnifying-glass"
                placeholder="Procurar missão por título..."
                class="!bg-transparent border-none shadow-none" />
        </div>

        <div class="h-8 w-px bg-zinc-100 dark:bg-zinc-800 hidden md:block"></div>

        <flux:select wire:model.live="projectFilter"
            class="w-full md:w-72 font-bold uppercase text-[10px]">
            <option value="">Filtrar por Unidade de Trabalho</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="statusFilter"
            class="w-full md:w-52 font-bold uppercase text-[10px]">
            <option value="">Todos os Estados</option>
            <option value="pendente">Pendente</option>
            <option value="em_curso">Em Curso</option>
            <option value="concluida">Concluída</option>
        </flux:select>

        <flux:button variant="ghost" size="sm" icon="eye-slash"
            x-on:click="privacyMode = !privacyMode"
            class="rounded-xl text-[10px] font-black uppercase">
            Privacidade
        </flux:button>
    </div>

    {{-- 4. MISSÕES & TIME-TRACKING --}}
    <div class="space-y-4">
        @forelse($tasks as $task)
            @php $isRunning = $task->is_timer_running; @endphp

            <div
                class="glass-card bg-white dark:bg-zinc-900 border-2 rounded-[2.2rem] transition-all duration-500 overflow-hidden shadow-sm group
                {{ $isRunning ? 'border-orange-500 shadow-orange-500/10 ring-4 ring-orange-500/5' : 'border-zinc-100 dark:border-zinc-800 hover:border-brand-500/30' }}">

                <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center">

                    {{-- ESQUERDA: IDENTIDADE --}}
                    <div class="p-6 flex items-center gap-5 flex-1">
                        <button
                            wire:click="updateStatus({{ $task->id }}, '{{ $task->status === 'concluida' ? 'pendente' : 'concluida' }}')"
                            class="size-8 rounded-full border-2 transition-all flex items-center justify-center shrink-0
                            {{ $task->status === 'concluida' ? 'bg-emerald-500 border-emerald-500 shadow-lg shadow-emerald-500/20' : 'border-zinc-300 dark:border-zinc-700 hover:border-brand-500' }}">
                            @if($task->status === 'concluida')
                                <flux:icon name="check" variant="micro" class="size-5 text-white" />
                            @endif
                        </button>

                        <div class="space-y-1.5">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h4
                                    class="font-black dark:text-white uppercase text-sm tracking-tight {{ $task->status === 'concluida' ? 'line-through opacity-40 italic' : '' }}">
                                    {{ $task->title }}
                                </h4>

                                <span
                                    class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-[0.2em] rounded-md border border-zinc-200 dark:border-zinc-700">
                                    {{ $task->project->name }}
                                </span>

                                @if($isRunning)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-orange-500/10 text-orange-500 text-[8px] font-black uppercase tracking-widest rounded-md animate-pulse">
                                        <div class="size-1 rounded-full bg-orange-500"></div>
                                        Em Foco
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                @if($task->assignee)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="size-5 rounded-lg bg-brand-600 flex items-center justify-center text-[8px] font-black text-white uppercase shadow-sm">
                                            {{ substr($task->assignee->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="text-[10px] font-black text-zinc-500 uppercase tracking-tighter">
                                            {{ $task->assignee->name }}
                                        </span>
                                    </div>
                                @endif

                                <span class="text-zinc-200 dark:text-zinc-800 text-xs">|</span>

                                <span
                                    class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest italic">
                                    {{ $task->status_time }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- CENTRO: CRONÓMETRO --}}
                    <div
                        class="bg-zinc-50/50 dark:bg-zinc-950/20 md:border-x border-zinc-100 dark:border-zinc-800 p-6 flex items-center gap-6 min-w-[240px]">
                        <div class="flex flex-col text-right flex-1">
                            <span
                                class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">
                                Time Elapsed
                            </span>
                            <span :class="privacyMode ? 'blur-sm select-none' : ''"
                                class="text-xl font-black font-mono transition-all duration-500 {{ $isRunning ? 'text-orange-500' : 'text-zinc-600 dark:text-zinc-400' }}">
                                {{ $task->time_formatted }}
                            </span>
                        </div>

                        <button wire:click="toggleTimer({{ $task->id }})"
                            class="size-12 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110
                            {{ $isRunning ? 'bg-orange-500 text-white animate-bounce' : 'bg-white dark:bg-zinc-800 text-zinc-400 hover:text-brand-500 border border-zinc-200 dark:border-zinc-700' }}">
                            <flux:icon name="{{ $isRunning ? 'stop' : 'play' }}" variant="solid"
                                class="size-6" />
                        </button>
                    </div>

                    {{-- DIREITA: PRIORIDADE & AÇÕES --}}
                    <div
                        class="p-6 flex items-center gap-6 bg-white dark:bg-zinc-900 min-w-[220px] justify-between md:justify-end">
                         <flux:button
        x-on:click="$dispatch('open-expense-modal', { taskId: {{ $task->id }}, projectId: {{ $task->project_id }} })"
        variant="ghost"
        icon="currency-euro"
        size="sm"
        class="text-emerald-500 rounded-xl hover:bg-emerald-50"
        title="Registar Gasto nesta Tarefa"
    />

    <flux:badge :variant="$task->priority_color" size="sm" class="font-black text-[9px] uppercase px-3 shadow-sm border-none">
        {{ $task->priority }}
    </flux:badge>


                        <div
                            class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <flux:button wire:click="edit({{ $task->id }})" variant="ghost"
                                icon="pencil-square" size="sm" class="rounded-xl" />
                            <flux:button wire:click="delete({{ $task->id }})"
                                wire:confirm="Eliminar missão?" variant="ghost" icon="trash"
                                size="sm" class="text-red-500 rounded-xl" />
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="py-24 text-center glass-card rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                <div
                    class="size-16 bg-zinc-50 dark:bg-zinc-900 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <flux:icon name="sparkles" class="size-8 text-zinc-300" />
                </div>
                <p
                    class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">
                    Backlog Vazio
                </p>
                <p
                    class="text-zinc-400 text-xs italic mt-1 font-medium leading-relaxed">
                    A tua lista de missões está limpa.<br>
                    Aproveita o tempo livre ou planeia a próxima etapa.
                </p>
            </div>
        @endforelse
    </div>

    {{-- 5. MODAL: PLANEAMENTO DE MISSÃO --}}
    <flux:modal name="task-modal" position="center"
        class="md:w-[600px] !p-0 overflow-visible">
        <div
            class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark"
                        class="rounded-full" />
                </flux:modal.close>
            </div>

            <div class="flex items-center gap-4">
                <div
                    class="p-3 bg-zinc-900 dark:bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="clipboard-document-check" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl"
                        class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Editar Missão' : 'Nova Missão Operacional' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">
                        Define os objetivos e aloca recursos para a execução.
                    </p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Descrição Principal --}}
                <div class="space-y-2">
                    <flux:label
                        class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                        O que precisa de ser executado?
                    </flux:label>
                    <flux:input wire:model="title"
                        placeholder="Ex: Finalizar auditoria financeira Q4..."
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                </div>

                {{-- Painel de Atribuição --}}
                <div
                    class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="user-group" class="size-3 text-brand-500" />
                        <p
                            class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">
                            Recursos & Contexto
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label
                                class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                                Unidade de Trabalho (Projeto)
                            </flux:label>
                            <flux:select wire:model="project_id"
                                class="font-black uppercase text-[10px] !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                                <option value="">Vincular a Projeto...</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">📂 {{ $p->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                      {{-- Responsável pela Missão --}}
<div class="space-y-2">
    <flux:label
        class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
        Responsável pela Missão
    </flux:label>

    <flux:select wire:model="user_id"
        class="font-black uppercase text-[10px] !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
        <option value="">Pendente de Atribuição...</option>

        @foreach($team as $member)
            <option value="{{ $member->id }}">👤 {{ $member->name }}</option>
        @endforeach
    </flux:select>
</div>

                    </div>
                </div>

                {{-- Urgência e Deadline --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label
                            class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                            Nível de Urgência
                        </flux:label>
                        <flux:select wire:model="priority"
                            class="font-black uppercase text-xs !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner">
                            <option value="baixa">🟢 Baixa Prioridade</option>
                            <option value="media">🟡 Prioridade Normal</option>
                            <option value="alta">🟠 Alta Prioridade</option>
                            <option value="critica">🔴 MISSÃO CRÍTICA</option>
                        </flux:select>
                    </div>

                    <div class="space-y-2">
                        <flux:label
                            class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                            Data Limite de Entrega
                        </flux:label>
                        <flux:input wire:model="due_date" type="date"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>
                </div>

                {{-- Estimativa de Horas --}}
                <div class="space-y-2">
                    <flux:label
                        class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                        Estimativa de Horas
                    </flux:label>
                    <flux:input wire:model="estimated_hours" type="number" min="0"
                        step="0.5"
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        placeholder="Ex: 4.0" />
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost"
                        class="flex-1 font-black uppercase text-[10px] text-zinc-400">
                        Abortar
                    </flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary"
                    class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Missão' : 'Lançar Missão' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ --}}
    <footer
        class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p
            class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Gestão Operacional
        </p>
    </footer>
</div>


<div class="space-y-10 pb-20 text-left"> {{-- Única DIV Raiz --}}

    {{-- 1. HEADER OPERACIONAL --}}
    <div class="relative">

        {{-- BADGE DINÂMICO (CEO A VER VS COLABORADOR REAL) --}}
        <div class="flex justify-center mb-10">
            <div class="relative group cursor-default">
                @php $isImpersonating = session()->has('impersonator_id'); @endphp

                <!-- Glow dinâmico: Vermelho (CEO) vs Esmeralda (Colaborador) -->
                <div class="absolute -inset-1 bg-gradient-to-r {{ $isImpersonating ? 'from-red-500/30 to-orange-500/30' : 'from-emerald-500/30 to-teal-500/30' }} rounded-full blur opacity-25 group-hover:opacity-60 transition duration-1000"></div>

                <div class="relative flex items-center gap-3 px-5 py-2 rounded-full bg-white/90 dark:bg-zinc-900/90 border border-zinc-200/50 dark:border-zinc-800/50 shadow-2xl backdrop-blur-xl">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isImpersonating ? 'bg-red-400' : 'bg-emerald-400' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isImpersonating ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                    </span>

                    <p class="text-[10px] font-black uppercase tracking-[0.15em] flex items-center gap-2">
    @if(session()->has('viewing_as_collaborator_id'))
        @php $target = \App\Models\Employee::find(session('viewing_as_collaborator_id')); @endphp
        <span class="text-zinc-400 dark:text-zinc-500 italic">A visualizar a conta de:</span>
        <span class="text-red-600 dark:text-red-400 font-black">{{ $target->name ?? 'Colaborador' }}</span>
        <span class="text-zinc-300 dark:text-zinc-700">|</span>
        <span class="text-red-500 font-black italic">Acesso CEO</span>
    @else
        <span class="text-zinc-400">Terminal de Equipa:</span>
        <span class="text-zinc-800 dark:text-white">{{ auth()->user()->name }}</span>
        <span class="text-zinc-300 dark:text-zinc-700">|</span>
        <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent italic font-black uppercase">Staff Member</span>
    @endif
</p>
                    <flux:icon name="{{ $isImpersonating ? 'eye' : 'bolt' }}" variant="micro" class="size-4 {{ $isImpersonating ? 'text-red-500' : 'text-emerald-500' }}" />
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <img src="{{ $workspace->logo_url }}" class="size-20 rounded-[2rem] shadow-2xl border-4 border-white dark:border-zinc-800 object-cover bg-white group-hover:scale-105 transition-all duration-500">
                    <div class="absolute -bottom-1 -right-1 size-7 bg-emerald-500 border-4 border-zinc-50 dark:border-zinc-950 rounded-full shadow-lg"></div>
                </div>

                <div>
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                        {{ $workspace->name }}
                    </h1>
                    <div class="flex items-center gap-3 mt-3">
                        <flux:badge variant="neutral" size="sm" class="font-black text-[9px] tracking-[0.2em] border-none bg-zinc-100 dark:bg-zinc-800 text-zinc-500 uppercase">
                            Espaço de Trabalho Ativo
                        </flux:badge>
                        @if($ceo)
                            <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">
                                CEO: <span class="text-zinc-700 dark:text-zinc-200">{{ $ceo->name }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- AÇÕES RÁPIDAS: PONTO E TAREFAS --}}
            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all">

    {{-- BOTÃO REGISTAR PONTO --}}
    <button
        wire:click="registerPunch"
        class="flex items-center gap-2 px-5 py-2.5 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all
        {{ $isClockedIn ? 'text-red-500 hover:bg-red-50' : 'text-zinc-500 hover:text-emerald-600 hover:bg-zinc-50' }}"
    >
        <flux:icon name="clock" variant="micro" class="size-4" />
        <span>{{ $isClockedIn ? 'Terminar Turno' : 'Registar Ponto' }}</span>
    </button>

    <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

    {{-- BOTÃO NOVA TAREFA (Redireciona para a página de tarefas que já tens) --}}
    <flux:button
        href="{{ route('hub.business.tasks') }}"
        variant="primary"
        icon="plus"
        class="bg-emerald-600 hover:bg-emerald-700 border-none shadow-lg shadow-emerald-500/20 rounded-2xl font-black uppercase tracking-tighter px-6 text-white"
    >
        Nova Tarefa
    </flux:button>
</div>
        </div>
    </div>

    {{-- 2. GRID DE PERFORMANCE PESSOAL --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        {{-- Pendentes --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">A Minha Carga</p>
            <h3 class="text-4xl font-black text-zinc-800 dark:text-white tracking-tighter">
                {{ $stats['pending'] }} <span class="text-xs font-medium text-zinc-400 uppercase tracking-widest">Tarefas Ativas</span>
            </h3>
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <flux:icon name="briefcase" class="size-16" />
            </div>
        </div>

        {{-- Eficiência --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Impacto Diário</p>
            <h3 class="text-4xl font-black text-emerald-500 tracking-tighter">
                +{{ $stats['completed_today'] }} <span class="text-xs font-medium text-emerald-500/60 uppercase tracking-widest">Hoje</span>
            </h3>
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <flux:icon name="sparkles" class="size-16" />
            </div>
        </div>

        {{-- Alertas --}}
        <div class="glass-card bg-zinc-950 p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl relative overflow-hidden group">
            <p class="text-[10px] font-black text-red-400 uppercase tracking-[0.2em] mb-1">Atenção Crítica</p>
            <h3 class="text-4xl font-black {{ $stats['overdue'] > 0 ? 'text-red-500 shadow-red-500/20' : 'text-white' }} tracking-tighter italic">
                {{ $stats['overdue'] }} <span class="text-xs font-medium uppercase tracking-widest">Atrasadas</span>
            </h3>
            <flux:icon name="exclamation-triangle" class="absolute -right-4 -bottom-4 size-20 text-white/5" />
        </div>
    </div>

    {{-- 3. FOCO NO WORKFLOW --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        {{-- LISTA DE TAREFAS (A CHECKLIST PRINCIPAL) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-500/10 rounded-lg text-brand-600">
                        <flux:icon name="list-bullet" variant="outline" class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 leading-none">Workflow</h3>
                        <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter mt-1">A Minha Checklist</p>
                    </div>
                </div>
                <flux:button href="{{ route('hub.business.tasks') }}" variant="ghost" size="xs" class="text-[9px] font-black uppercase tracking-widest">Ver Todas</flux:button>
            </div>

            <div class="space-y-4">
                @forelse($myTasks as $task)
                    <div class="flex items-center justify-between p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 group hover:border-brand-500/50 transition-all shadow-sm">
                        <div class="flex items-center gap-6">
                            {{-- Checkbox Dinâmico --}}
                            <div class="relative flex items-center justify-center">
                                <flux:checkbox wire:click="completeTask({{ $task->id }})" class="size-6 cursor-pointer border-2" />
                            </div>

                            <div>
                                <p class="text-base font-black dark:text-white uppercase tracking-tight group-hover:text-brand-600 transition-colors leading-none">
                                    {{ $task->name }}
                                </p>
                                <div class="flex items-center gap-3 mt-2 text-[10px] font-bold uppercase tracking-widest">
                                    <span class="{{ $task->due_date->isPast() ? 'text-red-500' : 'text-zinc-400' }}">
                                        Prazo: {{ $task->due_date->format('d M') }}
                                    </span>
                                    <span class="text-zinc-300 dark:text-zinc-700">|</span>
                                    <span class="text-brand-500">{{ $task->project->name ?? 'Geral' }}</span>
                                </div>
                            </div>
                        </div>

                        <flux:badge size="sm" variant="neutral" class="text-[8px] font-black uppercase tracking-widest px-3 py-1 border-none bg-zinc-50 dark:bg-zinc-800">
                            {{ $task->status ?? 'Pendente' }}
                        </flux:badge>
                    </div>
                @empty
                    <div class="py-20 text-center border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[3rem]">
                        <div class="inline-flex p-4 bg-zinc-50 dark:bg-zinc-900 rounded-full mb-4">
                            <flux:icon name="check-circle" class="size-10 text-zinc-300" />
                        </div>
                        <p class="text-zinc-400 font-black uppercase text-xs tracking-[0.2em]">Todas as tarefas concluídas. Bom trabalho!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- BARRA LATERAL: PROJETOS & COMUNICAÇÃO --}}
        <div class="space-y-8">
            {{-- MEUS PROJETOS --}}
            <div class="glass-card p-8 bg-zinc-50/50 dark:bg-zinc-900/40 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-3 mb-8">
                    <flux:icon name="briefcase" variant="outline" class="size-4 text-zinc-400" />
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 leading-none">Os Meus Projetos</h3>
                </div>

                <div class="space-y-8">
                    @foreach($myProjects as $project)
                        <div class="space-y-3">
                            <div class="flex justify-between items-end">
                                <span class="text-[11px] font-black uppercase dark:text-zinc-100 tracking-tight leading-none">{{ $project->name }}</span>
                                <span class="text-[10px] font-bold text-brand-600 leading-none">{{ $project->progress ?? 10 }}%</span>
                            </div>
                            <div class="h-2 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-brand-500 shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: {{ $project->progress ?? 10 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- QUICK MESSENGER --}}
            <div class="glass-card p-8 bg-brand-600 rounded-[2.5rem] text-white shadow-2xl shadow-brand-600/30 relative overflow-hidden group">
                <div class="relative z-10">
                    <flux:icon name="chat-bubble-left-right" class="size-10 mb-6 text-white/80" />
                    <h4 class="text-2xl font-black uppercase italic leading-tight tracking-tighter">Equipa Online</h4>
                    <p class="text-[11px] font-bold uppercase opacity-80 mt-2 tracking-widest">Tens alguma dúvida sobre o trabalho?</p>

                    <flux:button href="{{ route('hub.business.messenger') }}" class="w-full mt-10 bg-white text-brand-600 font-black uppercase text-[10px] tracking-widest h-14 rounded-2xl shadow-xl group-hover:scale-[1.02] transition-transform">
                        Abrir Messenger Interno
                    </flux:button>
                </div>
                <!-- Detalhe decorativo -->
                <div class="absolute -right-4 -top-4 size-32 bg-white/10 rounded-full blur-3xl transition-transform duration-1000 group-hover:scale-150"></div>
            </div>
        </div>
    </div>
    {{-- MODAL DE BLOQUEIO DE RESCISÃO --}}
    @if($isTerminated)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-xl animate-in fade-in duration-500">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-lg rounded-[3rem] shadow-2xl p-12 text-center border border-red-500/20">
                <div class="relative inline-flex mb-8">
                    <div class="absolute inset-0 bg-red-500/20 blur-2xl rounded-full"></div>
                    <div class="relative size-24 rounded-[2rem] bg-red-600 flex items-center justify-center text-white shadow-xl">
                        <flux:icon name="exclamation-triangle" class="size-12" />
                    </div>
                </div>

                <h2 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter leading-tight text-center">Vínculo Encerrado</h2>

                <div class="mt-6 p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800 text-left">
                    <p class="text-sm text-zinc-500 leading-relaxed font-medium">
                        A tua ligação com a organização <span class="text-zinc-900 dark:text-white font-black uppercase">{{ $workspace->name }}</span> foi formalmente encerrada.
                    </p>
                    <p class="text-xs text-zinc-400 mt-4 italic">
                        Ao sair, perderás o acesso a este terminal. Poderás criar o teu próprio negócio ou entrar noutra equipa no ecrã seguinte.
                    </p>
                </div>

                {{-- Botão com wire:click correto --}}
                <flux:button
                    wire:click="acknowledgeTermination"
                    variant="primary"
                    class="w-full h-16 mt-10 bg-red-600 hover:bg-red-700 border-none rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-red-500/40"
                >
                    Sair do Terminal
                </flux:button>
            </div>
        </div>
    @endif
</div>

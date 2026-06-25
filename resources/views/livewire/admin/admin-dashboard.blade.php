<div class="space-y-8 text-left pb-20">
    {{-- 1. HEADER E AÇÕES RÁPIDAS --}}
    <x-page-header title="Consola de Comando" description="Visão analítica e controlo global do ecossistema Finance Pro.">
        <x-slot:actions>

            {{-- LÓGICA DO BOTÃO DE INSTRUÇÕES CONFORME O NÍVEL --}}
            @php
                $user = auth()->user();
                $tutorialEvent = match($user->role) {
                    'admin'     => 'open-admin-tutorial',
                    'moderator' => 'open-moderator-tutorial',
                    'analyst'   => 'open-analyst-tutorial',
                    default     => null
                };
            @endphp

            @if($tutorialEvent)
                <flux:button
                    x-on:click="$dispatch('{{ $tutorialEvent }}')"
                    variant="ghost"
                    icon="question-mark-circle"
                    class="font-black uppercase text-[10px] tracking-widest border border-zinc-200 dark:border-zinc-800"
                >
                    Instruções
                </flux:button>
            @endif

            {{-- Prioridade 1: Gerir Contas (Visível para Admin e Moderador) --}}
            @if($user->isAdmin() || $user->isModerator())
                <flux:button href="{{ route('admin.users') }}" variant="primary" icon="users" wire:navigate class="shadow-lg shadow-brand-500/20 px-6 font-black uppercase text-[10px] tracking-widest">Gerir Utilizadores</flux:button>
            @endif

            {{-- Prioridade 2: Enviar Notificação (Visível para Admin e Moderador) --}}
            @if($user->isAdmin() || $user->isModerator())
                <flux:button href="{{ route('admin.communication') }}" variant="filled" icon="megaphone" wire:navigate class="font-black uppercase text-[10px] tracking-widest">Enviar Notificação</flux:button>
            @endif

            {{-- Prioridade 3: Logs de Auditoria (Apenas Admin Geral) --}}
            @if($user->isAdmin())
                <flux:button href="{{ route('admin.logs') }}" variant="ghost" icon="shield-check" wire:navigate class="font-black uppercase text-[10px] tracking-widest">Logs de Auditoria</flux:button>
            @endif

        </x-slot:actions>
    </x-page-header>

{{-- 2. KPIs GLOBAIS (Grelha de 5 colunas) --}}
<div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-5">
    {{-- Utilizadores --}}
    <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
        <div class="flex justify-between items-start mb-2">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Utilizadores</p>
            <div class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-xl"><flux:icon name="users" class="size-4 text-zinc-500" /></div>
        </div>
        <p class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter italic">{{ number_format($totalUsers) }}</p>
        <div class="mt-3 flex items-center gap-2 text-[10px] font-black uppercase">
            <span class="text-emerald-500">{{ $activeUsersToday }} ativos hoje</span>
            <span class="text-zinc-300">|</span>
            <span class="text-brand-500">+{{ $growthRate }}% cresc.</span>
        </div>
    </div>

    {{-- Financeiro --}}
    <div class="stat-card bg-zinc-900 dark:bg-black p-6 rounded-[2rem] shadow-2xl border border-white/5 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 size-32 bg-brand-500/10 blur-3xl rounded-full"></div>
        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">Volume Global</p>
        <p class="text-4xl font-black text-white tracking-tighter italic">{{ number_format($totalCashflow, 0, ',', ' ') }}€</p>
        <p class="mt-3 text-[9px] text-zinc-500 font-bold uppercase tracking-widest italic">Soma de todos os lançamentos</p>
    </div>

    {{-- IA Engagement --}}
    <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <div class="flex justify-between items-start mb-2">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Diálogos IA</p>
            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-xl"><flux:icon name="cpu-chip" class="size-4 text-blue-500" /></div>
        </div>
        <p class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter italic">{{ number_format($aiMessagesToday) }}</p>
        <p class="mt-3 text-[10px] font-black text-blue-500 uppercase tracking-widest">{{ $aiErrorRate }}% taxa de latência</p>
    </div>

    {{-- Retenção (Acesso a Stats) --}}
    <a href="{{ route('admin.stats') }}" wire:navigate class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-brand-500 transition-all group">
        <div class="flex justify-between items-start mb-2">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em]">Retenção</p>
            <flux:icon name="arrow-up-right" class="size-4 text-zinc-300 group-hover:text-brand-500" />
        </div>
        <p class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter italic">{{ $onboardingRate }}%</p>
        <p class="mt-3 text-[10px] text-brand-500 font-black uppercase italic group-hover:translate-x-1 transition-transform">Ver analítica detalhada →</p>
    </a>

    {{-- Workspace Status --}}
    <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-2">Workspaces</p>
        <p class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter italic">{{ $totalWorkspaces }}</p>
        <div class="mt-3 flex items-center gap-2">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Sincronização Ativa</span>
        </div>
    </div>
</div>

{{-- 3. GRELHA DE LISTAGENS E INSIGHTS --}}
<div class="grid gap-8 lg:grid-cols-12">

    {{-- COLUNA ESQUERDA (8 COLUNAS) --}}
    <div class="lg:col-span-8 space-y-8">

        {{-- ÚLTIMOS UTILIZADORES (ESTILO RAIO-X) --}}
        <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden text-left transition-all hover:shadow-xl">
            <div class="p-6 border-b dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-950/20 px-8">
                <h2 class="text-xs font-black uppercase tracking-widest text-zinc-500">Fluxo de Novos Registos</h2>
                <flux:button variant="ghost" size="xs" href="{{ route('admin.users') }}" wire:navigate class="text-[9px] font-black uppercase">Ver todos os utilizadores</flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400">
                        <tr>
                            <th class="px-8 py-4">Utilizador</th>
                            <th class="px-6 py-4 text-center">Nível / XP</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-8 py-4 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($latestUsers as $u)
                            <tr class="hover:bg-brand-500/[0.02] transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-black text-zinc-500 border border-zinc-200 dark:border-zinc-700 shadow-inner group-hover:border-brand-500/50 transition-colors">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-zinc-900 dark:text-white leading-none group-hover:text-brand-600 transition-colors">{{ $u->name }}</span>
                                            <span class="text-[10px] text-zinc-400 mt-1 italic">{{ $u->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="inline-flex flex-col items-center px-3 py-1.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700">
                                        <span class="text-[10px] font-black text-brand-600 dark:text-brand-400 uppercase leading-none">Lvl {{ $u->level ?? 1 }}</span>
                                        <span class="text-[8px] font-bold text-zinc-400 mt-1 uppercase">{{ number_format($u->xp ?? 0) }} XP</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <flux:badge size="sm" color="{{ $u->isActive() ? 'emerald' : 'red' }}" inset="top bottom" class="uppercase font-black text-[9px] px-3">
                                        {{ $u->isActive() ? 'Ativo' : 'Inativo' }}
                                    </flux:badge>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <flux:button href="{{ route('admin.impersonate', $u->id) }}" size="xs" variant="ghost" icon="user-circle" class="opacity-0 group-hover:opacity-100 transition-opacity" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 3. LOGS DE ATIVIDADE RECENTES --}}
        @if(auth()->user()->isAdmin())
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left relative overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xs font-black uppercase tracking-widest text-zinc-400 flex items-center gap-2">
                    <span class="size-2 bg-emerald-500 rounded-full"></span> Auditoria de Atividade Recente
                </h2>
                <flux:button variant="ghost" size="xs" href="{{ route('admin.logs') }}" wire:navigate class="text-[9px] font-black uppercase">Ver Auditoria Full</flux:button>
            </div>

            <div class="space-y-3">
                @forelse($securityLogs as $log)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-950/40 rounded-2xl border border-zinc-100 dark:border-zinc-800 hover:border-brand-500/30 transition-all group">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-mono text-zinc-400 font-bold group-hover:text-brand-500">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</span>
                            <div class="h-4 w-px bg-zinc-200 dark:bg-zinc-800"></div>
                            <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">{{ $log->user_name }}</span>
                            <span class="text-xs text-zinc-500 lowercase">{{ $log->action }}</span>
                        </div>
                        <flux:icon name="bolt" variant="micro" class="size-3 text-zinc-300 opacity-0 group-hover:opacity-100 transition-opacity" />
                    </div>
                @empty
                    <p class="text-xs text-zinc-500 italic p-4 text-center">Nenhum evento registado na última hora.</p>
                @endforelse
            </div>
        </div>
        @endif
    </div>

    {{-- COLUNA DIREITA (4 COLUNAS) --}}
    <div class="lg:col-span-4 space-y-8">

        {{-- ADOÇÃO DE FUNÇÕES --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
            <h2 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-8 border-b dark:border-zinc-800 pb-4">Adoção de Funcionalidades</h2>

            <div class="space-y-8">
                @foreach($usageStats as $feature => $percent)
                <div class="group">
                    <div class="flex justify-between text-[10px] font-black uppercase mb-2 tracking-widest">
                        <span class="text-zinc-500 group-hover:text-brand-600 transition-colors">{{ $feature }}</span>
                        <span class="text-zinc-900 dark:text-white">{{ $percent }}%</span>
                    </div>
                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-2 rounded-full overflow-hidden border dark:border-zinc-700">
                        <div class="bg-brand-500 h-full transition-all duration-1000 shadow-[0_0_10px_rgba(var(--color-brand-500),0.3)]" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <flux:button href="{{ route('admin.stats') }}" class="w-full mt-10" variant="ghost" size="sm" wire:navigate class="font-black uppercase text-[9px] tracking-widest">Relatório Analítico Completo</flux:button>
        </div>

        {{-- PERFORMANCE DE TAREFAS --}}
        <div class="glass-card p-8 bg-zinc-50 dark:bg-zinc-800/40 rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-700 text-center relative overflow-hidden group">
            <div class="absolute inset-0 bg-brand-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <h3 class="font-black text-zinc-900 dark:text-white uppercase tracking-widest text-[11px] italic mb-6">Taxa de Conclusão</h3>

            <div class="relative size-32 mx-auto mb-6">
                <svg class="size-full" viewBox="0 0 36 36">
                    <path class="text-zinc-200 dark:text-zinc-700" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path class="text-emerald-500" stroke-width="3" stroke-dasharray="{{ $completionRate }}, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black italic tracking-tighter text-zinc-900 dark:text-white leading-none">{{ $completionRate }}%</span>
                    <span class="text-[8px] font-black text-zinc-500 uppercase mt-1">Metas OK</span>
                </div>
            </div>

            <p class="text-[10px] text-zinc-500 font-bold uppercase">{{ $totalReminders }} lembretes gerados este mês</p>
            <flux:button variant="ghost" size="xs" class="mt-6 w-full text-[9px] font-black uppercase border border-zinc-200 dark:border-zinc-700" href="{{ route('admin.productivity') }}" wire:navigate>Dossiê de Produtividade</flux:button>
        </div>
    </div>
</div>
</div>

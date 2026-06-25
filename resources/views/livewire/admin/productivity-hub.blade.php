<div class="space-y-8 text-left">
    <x-page-header title="🚀 Centro de Produtividade" description="Monitorização de metas, tarefas e fluxo de trabalho dos utilizadores.">
    </x-page-header>

    {{-- 1. KPIs DE PERFORMANCE --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Card Tarefas --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div>
                    <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Taxa de Tarefas</p>
                    <p class="text-4xl font-black mt-1 text-blue-600">{{ $stats['task_rate'] }}%</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-2xl">
                    <flux:icon name="check-circle" class="size-6" />
                </div>
            </div>
            <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-2.5 rounded-full overflow-hidden border dark:border-zinc-700">
                <div class="bg-blue-500 h-full transition-all duration-1000 shadow-[0_0_15px_rgba(59,130,246,0.5)]" style="width: {{ $stats['task_rate'] }}%"></div>
            </div>
            <p class="text-[10px] text-zinc-500 mt-4 font-bold uppercase tracking-tighter">{{ $stats['total_reminders'] }} Lembretes registados</p>
        </div>

        {{-- Card Objetivos --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Sucesso em Metas</p>
                    <p class="text-4xl font-black mt-1 text-emerald-600">{{ $stats['goal_rate'] }}%</p>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl">
                    <flux:icon name="trophy" class="size-6" />
                </div>
            </div>
            <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-2.5 rounded-full overflow-hidden border dark:border-zinc-700">
                <div class="bg-emerald-500 h-full transition-all duration-1000 shadow-[0_0_15px_rgba(16,185,129,0.5)]" style="width: {{ $stats['goal_rate'] }}%"></div>
            </div>
            <p class="text-[10px] text-zinc-500 mt-4 font-bold uppercase tracking-tighter">{{ $stats['total_goals'] }} Objetivos ativos</p>
        </div>

        {{-- Card Atividade Hoje (Estilo Consola) --}}
        <div class="stat-card bg-zinc-900 dark:bg-black text-white p-8 rounded-[2.5rem] shadow-2xl border border-white/5 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-6 -top-6 size-32 bg-brand-500/10 blur-3xl rounded-full"></div>
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.2em] relative z-10">Atividade Global Hoje</p>
            <div class="relative z-10">
                <p class="text-6xl font-black text-brand-400 tracking-tighter italic">{{ $stats['activity_today'] }}</p>
                <p class="text-[9px] text-zinc-400 font-bold uppercase mt-2 italic">Ações processadas em 24h</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- 2. RANKING DE UTILIZADORES (COL 7) --}}
        <div class="lg:col-span-7 glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden text-left">
            <div class="p-6 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Utilizadores Mais Ativos (Power Users)</h3>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach($topUsers as $index => $u)
                    <div class="px-8 py-5 flex items-center justify-between hover:bg-zinc-50/50 dark:hover:bg-brand-500/5 transition-all group">
                        <div class="flex items-center gap-5">
                            <span class="text-xs font-black text-zinc-400 italic">#0{{ $index + 1 }}</span>
                            <div class="size-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-black text-zinc-600 dark:text-zinc-400 group-hover:bg-brand-500 group-hover:text-white transition-all">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-black text-zinc-900 dark:text-white">{{ $u->name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-black bg-zinc-100 dark:bg-zinc-800 px-4 py-1.5 rounded-xl text-zinc-600 dark:text-zinc-400 uppercase tracking-tighter border border-zinc-200 dark:border-zinc-700">
                                {{ $u->actions_count }} Interações
                            </span>
                            <flux:icon name="chevron-right" class="size-4 text-zinc-300 opacity-0 group-hover:opacity-100 transition-all" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. TIMELINE DE CONQUISTAS (COL 5) --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="p-8 bg-brand-500 text-white rounded-[2.5rem] shadow-xl shadow-brand-500/20 relative overflow-hidden">
                <flux:icon name="presentation-chart-line" class="absolute -right-4 -bottom-4 size-24 opacity-20 -rotate-12" />
                <h4 class="text-lg font-black uppercase italic tracking-tighter">Insights de Retenção</h4>
                <p class="text-xs text-white/80 mt-3 leading-relaxed font-medium">
                    Utilizadores que completam o primeiro <strong>Objetivo Financeiro</strong> têm 80% mais probabilidade de permanecerem ativos no site após 30 dias.
                </p>
                <flux:button variant="ghost" class="mt-6 text-white border-white/20 hover:bg-white/10 text-[9px] font-black uppercase">Ver Relatório Full</flux:button>
            </div>

            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">
                <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-6 flex items-center gap-2">
                    <span class="size-2 bg-emerald-500 rounded-full"></span> Conclusões Recentes
                </h3>
                <div class="space-y-4">
                    @forelse($recentAchievements as $ach)
                        <div class="flex items-start gap-4 text-left">
                            <div class="size-2 mt-1.5 bg-emerald-500 rounded-full shrink-0"></div>
                            <div>
                                <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 leading-tight">
                                    <span class="text-brand-600">{{ $ach->user_name }}</span> {{ $ach->action }}
                                </p>
                                <p class="text-[9px] text-zinc-400 uppercase font-bold mt-1">{{ \Carbon\Carbon::parse($ach->created_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-400 italic">Sem conquistas recentes.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

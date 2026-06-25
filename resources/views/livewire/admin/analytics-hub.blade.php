<div class="space-y-12">
    {{-- SECÇÃO 1: ESTATÍSTICAS GERAIS --}}
    <div class="space-y-8">
        <x-page-header title="Estatísticas de Utilização" description="Visão global do comportamento e performance do site.">
            <x-slot:actions>
                <flux:select wire:model.live="period" class="w-40">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30">Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                </flux:select>
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-6 lg:grid-cols-3 text-left">
            {{-- CARD: ADOÇÃO --}}
            <div class="lg:col-span-2 glass-card p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-8">Adoção de Funcionalidades</h3>
                <div class="grid gap-8 sm:grid-cols-2">
                    @foreach($featureUsage as $feature => $percent)
                        <div class="space-y-3">
                            <div class="flex justify-between items-end">
                                <span class="text-sm font-bold capitalize text-zinc-700 dark:text-zinc-300">
                                    @switch($feature)
                                        @case('reminders') Lembretes @break
                                        @case('chatbot') Chatbot IA @break
                                        @case('goals') Objetivos @break
                                        @case('investments') Investimentos @break
                                        @default {{ ucfirst($feature) }}
                                    @endswitch
                                </span>
                                <span class="text-2xl font-black text-zinc-900 dark:text-white">{{ $percent }}%</span>
                            </div>
                            <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-3 rounded-full overflow-hidden text-[0px]">
                                <div class="bg-brand-500 h-full rounded-full transition-all duration-1000" style="width: {{ $percent }}%">.</div>
                            </div>
                            <p class="text-[10px] text-zinc-500 uppercase font-bold">Utilizadores ativos nesta função</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- CARD: DISPOSITIVOS --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-8">Acessos por Dispositivo</h3>
                <div class="space-y-6">
                    @foreach($devices as $device)
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center">
                                <flux:icon :name="$device['icon']" class="w-5 h-5 text-zinc-500" />
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between text-xs font-bold mb-1 text-left">
                                    <span class="dark:text-white">{{ $device['name'] }}</span>
                                    <span class="text-zinc-500">{{ $device['value'] }}%</span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full">
                                    <div class="bg-zinc-400 h-full rounded-full" style="width: {{ $device['value'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- FUNIL DE ONBOARDING --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-8">Funil de Onboarding (Conversão)</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                @php
                    $steps = [
                        ['label' => 'Registados', 'value' => $onboarding['registered'], 'color' => 'bg-zinc-100 dark:bg-zinc-800'],
                        ['label' => 'Perfil Configurado', 'value' => $onboarding['setup_profile'], 'color' => 'bg-brand-50 dark:bg-brand-900/10'],
                        ['label' => 'Primeira Despesa', 'value' => $onboarding['created_first_expense'], 'color' => 'bg-brand-100 dark:bg-brand-900/20'],
                        ['label' => 'Tutorial Concluído', 'value' => $onboarding['completed_tutorial'], 'color' => 'bg-brand-500 text-white'],
                    ];
                @endphp
                @foreach($steps as $index => $step)
                    <div class="p-6 rounded-2xl {{ $step['color'] }} relative flex flex-col items-center text-center">
                        <span class="text-[10px] font-black uppercase opacity-60 mb-2">{{ $step['label'] }}</span>
                        <span class="text-3xl font-black">{{ number_format($step['value']) }}</span>
                        @if($index > 0 && $steps[0]['value'] > 0)
                            <span class="text-[9px] font-bold mt-2 opacity-70 uppercase">{{ round(($step['value'] / $steps[0]['value']) * 100) }}% do total</span>
                        @elseif($index === 0)
                            <span class="text-[9px] font-bold mt-2 opacity-70 uppercase">Base 100%</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>








{{-- SECÇÃO 2: ESTATÍSTICAS INDIVIDUAIS --}}
    <div class="space-y-8 pb-20">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 px-2">
            <div class="text-left">
                <div class="flex items-center gap-3 text-left">
                    <h2 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter leading-none text-left">Raio-X de Utilizadores</h2>
                    <flux:badge size="sm" variant="pill" color="zinc" class="font-mono">{{ number_format($individualStats->total()) }} Contas</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 mt-2 font-medium text-left">Análise profunda de comportamento e conversão. Clica numa linha para detalhes totais.</p>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <flux:input wire:model.live.debounce.300ms="searchUser" icon="magnifying-glass" placeholder="Procurar por nome ou email..." class="w-full md:w-80 shadow-sm" />
            </div>
        </div>

        <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden transition-all text-left">
            <div class="overflow-x-auto text-left">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/50 border-b border-zinc-100 dark:border-zinc-800">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-black uppercase text-zinc-400 tracking-[0.15em] text-left">Utilizador</th>
                            <th class="px-4 py-5 text-center text-[10px] font-black uppercase text-zinc-400 tracking-[0.15em]">Nível</th>
                            <th class="px-4 py-5 text-center text-[10px] font-black uppercase text-zinc-400 tracking-[0.15em]">Financeiro</th>
                            <th class="px-4 py-5 text-center text-[10px] font-black uppercase text-zinc-400 tracking-[0.15em]">IA</th>
                            <th class="px-4 py-5 text-center text-[10px] font-black uppercase text-zinc-400 tracking-[0.15em]">Tarefas</th>
                            <th class="px-8 py-5 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-left">
                        @foreach($individualStats as $u)
                            <tr wire:click="showUserModal({{ $u->id }})" class="group cursor-pointer hover:bg-brand-500/[0.03] transition-all">
                                <td class="px-8 py-5 text-left">
                                    <div class="flex items-center gap-4 text-left">
                                        <div class="size-11 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-black text-zinc-500 border border-zinc-200 dark:border-zinc-700 shadow-inner group-hover:border-brand-500/50 transition-colors">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col text-left">
                                            <span class="text-sm font-black text-zinc-900 dark:text-white leading-none group-hover:text-brand-600 transition-colors">{{ $u->name }}</span>
                                            <span class="text-[11px] text-zinc-400 mt-1 font-medium italic">Registado {{ $u->created_at ? $u->created_at->diffForHumans() : '---' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-[10px] font-black text-brand-600 dark:text-brand-400 uppercase leading-none">LVL {{ $u->level ?? 1 }}</span>
                                        <span class="text-[9px] font-bold text-zinc-400 mt-1 uppercase">{{ number_format($u->xp ?? 0) }} XP</span>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="text-[10px] font-bold text-emerald-500 flex items-center gap-1">
                                            <flux:icon name="arrow-trending-up" variant="micro" class="size-3" /> {{ $u->receitas }}
                                        </span>
                                        <span class="text-[10px] font-bold text-red-500 flex items-center gap-1">
                                            <flux:icon name="arrow-trending-down" variant="micro" class="size-3" /> {{ $u->despesas }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <flux:badge size="sm" color="{{ $u->chatbot_count > 0 ? 'blue' : 'zinc' }}" class="font-black">
                                        {{ $u->chatbot_count }} msgs
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-tighter">{{ $u->reminders_count }} criados</span>
                                        <div class="w-10 h-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ min(($u->reminders_count ?? 0) * 10, 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <flux:icon name="chevron-right" class="size-4 text-zinc-300 group-hover:text-brand-500 group-hover:translate-x-1 transition-all" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t dark:border-zinc-800 bg-zinc-50/50 flex justify-between items-center">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Página {{ $individualStats->currentPage() }} de {{ $individualStats->lastPage() }}</p>
                {{ $individualStats->links() }}
            </div>
        </div>
    </div>


{{-- MODAL DE DADOS PROFUNDOS (DESIGN PREMIUM & CENTRADO) --}}
    <flux:modal name="user-deep-details" variant="center" class="md:w-[850px] !p-0 overflow-hidden text-left border-none shadow-2xl">
        @if($detailedUser)
            <div class="bg-zinc-50 dark:bg-black text-left flex flex-col h-full max-h-[90vh]">

                {{-- 1. HEADER / BANNER --}}
                <div class="bg-zinc-900 px-10 py-12 relative overflow-hidden shrink-0">
                    <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
                        <flux:icon name="identification" class="size-48 text-white" />
                    </div>

                    <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
                        {{-- Avatar --}}
                        <div class="relative group">
                            <div class="absolute inset-0 bg-brand-500 blur-2xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                            <div class="size-28 rounded-[2.5rem] bg-zinc-800 border-2 border-white/10 p-1.5 shadow-2xl relative">
                                <div class="size-full rounded-[2.2rem] bg-brand-500 flex items-center justify-center text-white text-5xl font-black italic">
                                    {{ substr($detailedUser->name, 0, 1) }}
                                </div>
                            </div>
                        </div>

                        {{-- Nome e Tags --}}
                        <div class="text-center md:text-left flex-1">
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-3">
                                <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter leading-none">{{ $detailedUser->name }}</h2>
                                <flux:badge size="sm" class="{{ $detailedUser->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }} border-none font-black uppercase text-[9px] tracking-widest">
                                    {{ $detailedUser->is_active ? 'Conta Ativa' : 'Banida' }}
                                </flux:badge>
                            </div>
                            <p class="text-zinc-400 font-bold text-lg mb-4">{{ $detailedUser->email }}</p>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                                <flux:badge size="sm" class="bg-white/5 text-zinc-400 border-white/10 font-black uppercase text-[9px] px-3">Cargo: {{ $detailedUser->role }}</flux:badge>
                                <flux:badge size="sm" class="bg-white/5 text-zinc-400 border-white/10 font-black uppercase text-[9px] px-3">Membro desde: {{ $detailedUser->created_at->format('d/m/Y') }}</flux:badge>
                                <flux:badge size="sm" class="bg-indigo-500/20 text-indigo-400 border-none font-black uppercase text-[9px] px-3">ID: #{{ $detailedUser->id }}</flux:badge>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. ÁREA DE CONTEÚDO (SCROLLABLE) --}}
                <div class="p-10 space-y-10 overflow-y-auto custom-scrollbar bg-white dark:bg-zinc-900">

                    {{-- Grelha de KPIs Rápidos --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-5 rounded-3xl border border-zinc-100 dark:border-zinc-800 text-left">
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Nível Atual</p>
                            <p class="text-2xl font-black text-brand-600 mt-1">Lvl {{ $detailedUser->level }}</p>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 h-1.5 rounded-full mt-3 overflow-hidden">
                                <div class="bg-brand-500 h-full transition-all duration-1000" style="width: {{ $userFullStats['xp_progress'] }}%"></div>
                            </div>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-5 rounded-3xl border border-zinc-100 dark:border-zinc-800 text-left">
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">XP Total</p>
                            <p class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ number_format($detailedUser->xp) }}</p>
                            <p class="text-[9px] text-zinc-500 mt-1 uppercase font-bold">{{ $detailedUser->points }} pontos disponíveis</p>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-5 rounded-3xl border border-zinc-100 dark:border-zinc-800 text-left">
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Engagement IA</p>
                            <p class="text-2xl font-black text-blue-500 mt-1">{{ $userFullStats['ai_messages_count'] }} <span class="text-xs">msgs</span></p>
                            <p class="text-[9px] text-zinc-500 mt-1 uppercase font-bold">{{ number_format($userFullStats['ai_tokens_estimate']) }} tokens consumidos</p>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-5 rounded-3xl border border-zinc-100 dark:border-zinc-800 text-left">
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Assiduidade</p>
                            <p class="text-2xl font-black text-orange-500 mt-1">{{ $detailedUser->streak }} dias 🔥</p>
                            <p class="text-[9px] text-zinc-500 mt-1 uppercase font-bold">Último Login: {{ $detailedUser->last_login_at?->diffForHumans() ?? 'Nunca' }}</p>
                        </div>
                    </div>

                    {{-- Secção Financeira & Produtividade --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Coluna Finanças --}}
                        <div class="space-y-6 bg-zinc-50 dark:bg-zinc-800/20 p-8 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800">
                            <h3 class="text-xs font-black uppercase text-zinc-400 tracking-widest flex items-center gap-2">
                                <flux:icon name="banknotes" class="size-4" /> Finanças Reais
                            </h3>
                            <div class="space-y-4 text-left">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-zinc-500 uppercase text-[10px]">Volume Recebido:</span>
                                    <span class="font-black text-emerald-600">+ {{ number_format($userFullStats['total_earned'], 2) }} €</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-zinc-500 uppercase text-[10px]">Volume Gasto:</span>
                                    <span class="font-black text-red-600">- {{ number_format($userFullStats['total_spent'], 2) }} €</span>
                                </div>
                                <div class="flex justify-between items-center text-sm border-t dark:border-zinc-700 pt-3">
                                    <span class="font-black uppercase text-[10px] dark:text-white">Balanço Líquido:</span>
                                    <span class="font-black {{ ($userFullStats['total_earned'] - $userFullStats['total_spent']) >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                        {{ number_format($userFullStats['total_earned'] - $userFullStats['total_spent'], 2) }} €
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-sm italic">
                                    <span class="text-[10px] text-zinc-400 font-bold uppercase">Média por Despesa:</span>
                                    <span class="text-xs font-bold text-zinc-500">{{ number_format($userFullStats['avg_expense'], 2) }} €</span>
                                </div>
                            </div>
                        </div>

                        {{-- Coluna Produtividade --}}
                        <div class="space-y-6 bg-zinc-50 dark:bg-zinc-800/20 p-8 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800">
                            <h3 class="text-xs font-black uppercase text-zinc-400 tracking-widest flex items-center gap-2">
                                <flux:icon name="bolt" class="size-4" /> Performance de Uso
                            </h3>
                            <div class="space-y-5">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black text-zinc-500 uppercase">Lembretes concluídos</span>
                                    <flux:badge size="sm" color="emerald" inset="top bottom" class="font-black">{{ $userFullStats['reminders_done'] }} / {{ $userFullStats['reminders_done'] + $userFullStats['reminders_pending'] }}</flux:badge>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black text-zinc-500 uppercase">Metas alcançadas</span>
                                    <flux:badge size="sm" color="purple" inset="top bottom" class="font-black">{{ $userFullStats['goals_reached'] }} / {{ $userFullStats['total_goals'] }}</flux:badge>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black text-zinc-500 uppercase">Grupos / Workspaces</span>
                                    <span class="font-black text-zinc-900 dark:text-white">{{ $userFullStats['workspaces_count'] }} Ativos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Últimas Ações (Histórico) --}}
                    @if(count($userFullStats['last_logs'] ?? []) > 0)
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em]">Rastreio de Atividade Recente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($userFullStats['last_logs'] as $log)
                                <div class="flex flex-col p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-100 dark:border-zinc-800 hover:border-brand-500/30 transition-all">
                                    <span class="text-[11px] font-bold text-zinc-900 dark:text-white uppercase leading-tight">{{ $log->action }}</span>
                                    <span class="text-[9px] text-zinc-400 font-mono mt-2 italic">{{ \Carbon\Carbon::parse($log->created_at)->format('d M, H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- 3. FOOTER / AÇÕES --}}
                <div class="p-8 bg-white dark:bg-zinc-900 border-t dark:border-zinc-800 shrink-0">
                    <div class="flex flex-col md:flex-row gap-4">
                        {{-- Botão de Impersonate (Assumir conta) --}}
                        <flux:button
                            href="{{ route('admin.impersonate', $detailedUser->id) }}"
                            variant="primary"
                            class="flex-1 h-14 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-xl shadow-brand-500/20"
                        >
                            Assumir Controlo Total da Conta
                        </flux:button>

                        {{-- Botão Fechar --}}
                        <flux:modal.close>
                            <flux:button variant="ghost" class="px-10 h-14 rounded-2xl font-black uppercase text-[10px] tracking-widest">Fechar dossiê</flux:button>
                        </flux:modal.close>
                    </div>
                    <p class="text-center text-[9px] text-zinc-400 uppercase font-black mt-4 tracking-tighter italic">Todas as ações realizadas através do acesso direto serão registadas nos logs de auditoria.</p>
                </div>
            </div>
        @endif
    </flux:modal>

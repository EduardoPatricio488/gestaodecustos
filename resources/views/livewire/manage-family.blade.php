<div class="space-y-10 pb-24">
    {{-- 1. HEADER ENTERPRISE --}}
    <div class="relative">
        {{-- Glow decorativo --}}
        <div class="absolute -top-10 left-0 size-72 bg-brand-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="user-group" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Comando do Grupo</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Workspace Ativo</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Gestão de privilégios e auditoria do espaço <span class="text-brand-600 font-bold uppercase tracking-tighter">{{ $workspaceName }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="rounded-xl font-bold text-zinc-500">Voltar</flux:button>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                @if($iAmAdmin)
                    <flux:button wire:click="updateWorkspaceName" variant="primary" icon="check" class="bg-brand-600 border-none shadow-lg shadow-brand-500/20 rounded-2xl font-black uppercase tracking-tighter px-6 text-white">
                        Guardar Alterações
                    </flux:button>
                @endif
            </div>
        </header>
    </div>

    {{-- 2. CONFIGURAÇÕES & CONVITE (GRID SUPERIOR) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Identidade do Workspace --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-brand-500/10 rounded-lg text-brand-600">
                        <flux:icon name="pencil-square" variant="outline" class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Branding do Espaço</h3>
                        <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Identidade</p>
                    </div>
                </div>

                <flux:input wire:model="workspaceName" placeholder="Ex: Família Louro" :disabled="!$iAmAdmin" class="h-14 !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl font-bold shadow-inner" />
                <p class="text-[10px] text-zinc-400 mt-4 italic">Apenas o <span class="font-black text-brand-600">Administrador Master</span> pode renomear este grupo.</p>
            </div>
        </div>

        {{-- Código de Convite Estilo "Safe Vault" --}}
        <div class="lg:col-span-7">
            <div class="glass-card p-8 bg-zinc-950 text-white rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
                <div class="absolute -right-20 -top-20 size-64 bg-brand-500/10 blur-[100px] rounded-full group-hover:bg-brand-500/20 transition-all"></div>

                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 h-full">
                    <div class="space-y-2 text-center md:text-left">
                        <div class="flex items-center gap-2 justify-center md:justify-start">
                            <flux:icon name="key" class="size-4 text-brand-400" />
                            <h3 class="text-xs font-black uppercase tracking-[0.3em] text-zinc-500">Chave de Acesso</h3>
                        </div>
                        <p class="text-2xl font-black italic uppercase tracking-tighter">Convidar Membros</p>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest max-w-[200px]">Partilhe este token para expandir o grupo.</p>
                    </div>

                    <div class="flex items-center gap-4 bg-white/5 p-4 rounded-3xl border border-white/5 backdrop-blur-md">
                        <span class="text-4xl font-mono font-black text-brand-500 tracking-[0.3em] uppercase pl-4">{{ $inviteCode }}</span>
                        <flux:button size="sm" variant="ghost" icon="document-duplicate" class="text-zinc-500 hover:text-white transition-colors" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. GESTÃO DE ACESSOS (TABELA DE MEMBROS) --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="shield-check" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Utilizadores com Privilégios</h2>
            </div>
            <flux:badge variant="neutral" class="font-black text-[9px] uppercase bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1">
                {{ count($members) }} Membros Ativos
            </flux:badge>
        </div>

        <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/50 dark:bg-zinc-950/20 text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] border-b dark:border-zinc-800">
                            <th class="p-6">Utilizador</th>
                            <th class="p-6 text-center">Nível de Acesso</th>
                            <th class="p-6 text-right">Controlo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($members as $member)
                            <tr class="group hover:bg-zinc-50/50 dark:hover:bg-brand-500/5 transition-all">
                                {{-- Identificação --}}
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="relative shrink-0">
                                            <flux:avatar initials="{{ substr($member->name, 0, 2) }}" class="size-12 shadow-sm border-2 border-white dark:border-zinc-800" />
                                            @if($member->id === auth()->id())
                                                <div class="absolute -bottom-1 -right-1 size-4 bg-brand-500 rounded-full border-2 border-white dark:border-zinc-900"></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                                {{ $member->name }}
                                                @if($member->id === auth()->id())
                                                    <span class="text-[8px] bg-brand-500 text-white px-2 py-0.5 rounded-full font-black tracking-widest italic uppercase">Tu</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-zinc-500 font-medium italic">{{ $member->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Funções / Roles --}}
                                <td class="p-6">
                                    <div class="flex justify-center">
                                        @if($iAmAdmin && $member->id !== auth()->id())
                                            <div class="w-48">
                                                <flux:select
                                                    wire:change="updateRole({{ $member->id }}, $event.target.value)"
                                                    class="font-black uppercase text-[10px] tracking-tighter !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-xl h-10 shadow-inner"
                                                >
                                                    <option value="admin" {{ $member->pivot->role === 'admin' ? 'selected' : '' }}>👑 Administrador</option>
                                                    <option value="editor" {{ $member->pivot->role === 'editor' ? 'selected' : '' }}>✍️ Editor Master</option>
                                                    <option value="viewer" {{ $member->pivot->role === 'viewer' ? 'selected' : '' }}>👁️ Analista (Read)</option>
                                                </flux:select>
                                            </div>
                                        @else
                                            @php
                                                $roleStyle = match($member->pivot->role) {
                                                    'admin'  => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                                    'editor' => 'bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-500/20',
                                                    default  => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.2em] border {{ $roleStyle }}">
                                                {{ $member->pivot->role === 'admin' ? '👑 Admin master' : ($member->pivot->role === 'editor' ? '✍️ Editor master' : '👁️ Analista') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Acções --}}
                                <td class="p-6 text-right">
                                    @if($iAmAdmin && $member->id !== auth()->id())
                                        <flux:button
                                            wire:click="removeMember({{ $member->id }})"
                                            wire:confirm="Expulsar este membro definitivamente do workspace?"
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            color="red"
                                            class="opacity-0 group-hover:opacity-100 transition-all rounded-xl hover:bg-red-50 dark:hover:bg-red-950/20"
                                        />
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   {{-- 4. MONITORIZAÇÃO DE PERFORMANCE (PREMIER FINANCIAL LEDGER) --}}
    <div class="space-y-6 pt-12">

        {{-- CABEÇALHO DA SECÇÃO --}}
        <div class="flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-brand-600 rounded-lg shadow-lg shadow-brand-500/20 text-white">
                    <flux:icon name="presentation-chart-line" variant="mini" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Auditoria de Rendimento <span class="text-zinc-300 dark:text-zinc-600 mx-2">|</span> <span class="text-brand-600 italic">{{ now()->translatedFormat('F Y') }}</span></h2>
            </div>
        </div>

        <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">

            {{-- HEADER DA TABELA (DESKTOP) --}}
            <div class="hidden lg:grid grid-cols-12 gap-4 px-10 py-5 bg-zinc-50/50 dark:bg-zinc-950/30 border-b border-zinc-100 dark:border-zinc-800">
                <div class="col-span-4 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Membro do Grupo</div>
                <div class="col-span-3 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 text-right">Rendimento Bruto</div>
                <div class="col-span-3 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 text-right">Total Despesas</div>
                <div class="col-span-2 text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 text-right">Net Balance</div>
            </div>

            {{-- LINHAS DE DADOS --}}
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach($memberStats as $user)
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 px-6 lg:px-10 py-6 items-center hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group">

                        {{-- 1. IDENTIDADE --}}
                        <div class="col-span-4 flex items-center gap-5">
                            <div class="relative shrink-0">
                                <div class="size-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center font-black text-xs shadow-xl border border-zinc-800 group-hover:scale-110 transition-transform">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div class="absolute -top-2 -left-2 size-6 bg-brand-600 text-white rounded-lg flex items-center justify-center text-[8px] font-black border-2 border-white dark:border-zinc-900 shadow-md">
                                    {{ $user->level }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-black dark:text-white uppercase tracking-tight truncate">{{ $user->name }}</h3>
                                <p class="text-[8px] font-black text-brand-600 uppercase tracking-widest mt-0.5">{{ number_format($user->xp) }} XP ACUMULADO</p>
                            </div>
                        </div>

                        {{-- 2. RENDIMENTO BRUTO (ALINHADO À DIREITA) --}}
                        <div class="col-span-3 flex lg:block justify-between items-center">
                            <span class="lg:hidden text-[9px] font-black uppercase text-zinc-400">Rendimento:</span>
                            <p class="text-lg font-black text-emerald-600 tracking-tighter text-right">
                                +{{ number_format($user->total_incomes, 2, ',', ' ') }}€
                            </p>
                        </div>

                        {{-- 3. TOTAL DESPESAS (ALINHADO À DIREITA) --}}
                        <div class="col-span-3 flex lg:block justify-between items-center">
                            <span class="lg:hidden text-[9px] font-black uppercase text-zinc-400">Despesas:</span>
                            <p class="text-lg font-black text-red-500 tracking-tighter text-right">
                                -{{ number_format($user->total_expenses, 2, ',', ' ') }}€
                            </p>
                        </div>

                        {{-- 4. NET BALANCE (DETALHE ESTÉTICO FINAL) --}}
                        <div class="col-span-2 flex lg:block justify-between items-center pl-0 lg:pl-6">
                            <span class="lg:hidden text-[9px] font-black uppercase text-zinc-400">Balanço:</span>
                            <div class="lg:bg-zinc-50 lg:dark:bg-zinc-800/50 lg:p-3 rounded-2xl lg:border lg:border-zinc-100 lg:dark:border-zinc-800 text-right group-hover:bg-zinc-900 dark:group-hover:bg-white transition-colors duration-500">
                                <p class="text-lg font-black {{ $user->net_balance >= 0 ? 'text-emerald-500 group-hover:text-white dark:group-hover:text-zinc-900' : 'text-red-500' }} tracking-tighter leading-none italic">
                                    {{ $user->net_balance >= 0 ? '+' : '' }}{{ number_format($user->net_balance, 2, ',', ' ') }}€
                                </p>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- 5. RANKING & AUDITORIA DE ATIVIDADE (LEADERBOARD) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-6">

        {{-- COLUNA: TOP CONTRIBUINTES (ESTILO RANKING PREMIUM) --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-2">
                <div class="p-2 bg-amber-500 rounded-lg shadow-lg shadow-amber-500/20 text-white">
                    <flux:icon name="trophy" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Top Contribuintes</h2>
            </div>

            <div class="space-y-4">
                @foreach($topRecorders as $index => $user)
                    @php
                        $rankStyle = match($index) {
                            0 => 'border-amber-500/50 bg-amber-500/5 shadow-amber-500/10',
                            1 => 'border-zinc-400/50 bg-zinc-400/5',
                            2 => 'border-orange-600/50 bg-orange-600/5',
                            default => 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900'
                        };
                        $medalColor = match($index) {
                            0 => 'text-amber-500',
                            1 => 'text-zinc-400',
                            2 => 'text-orange-600',
                            default => 'text-zinc-500'
                        };
                        $userActivities = $recentActivities->where('user_id', $user->id);
                    @endphp

                    <div x-data="{ showActions: false }" class="glass-card {{ $rankStyle }} border rounded-[2rem] overflow-hidden transition-all duration-300 group">

                        {{-- Cabeçalho do Rank --}}
                        <div @click="showActions = !showActions" class="p-5 flex items-center justify-between cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="text-xl font-black {{ $medalColor }} italic italic w-8 text-center">{{ $index + 1 }}º</span>
                                <div>
                                    <p class="text-sm font-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                                        {{ explode(' ', $user->name)[0] }}
                                        @if($user->id === auth()->id()) <span class="text-[8px] font-black text-brand-600 tracking-widest">(TU)</span> @endif
                                    </p>
                                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-tighter">{{ $user->expenses_count }} registos efetuados</p>
                                </div>
                            </div>
                            <flux:icon name="chevron-down" class="size-4 text-zinc-400 transition-transform duration-300" ::class="showActions ? 'rotate-180' : ''" />
                        </div>

                        {{-- Detalhes Expansíveis --}}
                        <div x-show="showActions" x-collapse x-cloak>
                            <div class="px-5 pb-5 border-t border-zinc-100 dark:border-zinc-800 pt-4 bg-zinc-50/50 dark:bg-zinc-950/50">
                                <div class="max-h-64 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                                    @forelse($userActivities as $log)
                                        <div class="p-3 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-[8px] font-black uppercase text-brand-600 tracking-widest italic">{{ $log->created_at->diffForHumans() }}</span>
                                                <flux:icon name="bolt" class="size-3 text-zinc-300" />
                                            </div>
                                            <p class="text-[11px] font-medium text-zinc-600 dark:text-zinc-400 leading-tight">{{ $log->description }}</p>
                                        </div>
                                    @empty
                                        <p class="text-center py-4 text-[10px] font-black uppercase text-zinc-400 italic">Sem atividade recente</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- COLUNA: LINHA DO TEMPO GLOBAL (2 COLUNAS) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-600 rounded-lg shadow-lg shadow-brand-500/20 text-white">
                        <flux:icon name="clock" variant="outline" class="size-4" />
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Log de Operações</h2>
                </div>
            </div>

            <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm">
                <div class="max-h-[700px] overflow-y-auto p-8 space-y-8 custom-scrollbar">
                    @forelse($recentActivities as $log)
                        @php
                            $props = is_array($log->properties) ? $log->properties : json_decode($log->properties, true);
                            $fieldLabels = [
                                'amount' => 'Valor', 'spent_at' => 'Data', 'subcategory' => 'Subcategoria',
                                'entidade' => 'Empresa/Serviço', 'km' => 'Quilometragem', 'local' => 'Posto/Local', 'pessoas' => 'Nº Pessoas'
                            ];
                        @endphp

                        <div x-data="{ open: false }" class="relative pl-8 border-l-2 border-zinc-100 dark:border-zinc-800 last:border-l-transparent group">
                            <div class="absolute -left-[9px] top-0 size-4 rounded-full border-4 border-white dark:border-zinc-900 bg-brand-500 group-hover:scale-125 transition-all shadow-sm"></div>

                            <div class="flex flex-col gap-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <flux:avatar initials="{{ substr($log->user->name, 0, 2) }}" class="size-8 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700" />
                                        <div class="space-y-0.5">
                                            <p class="text-xs font-black dark:text-white uppercase tracking-tight">{{ $log->user->name }}</p>
                                            <p class="text-[11px] text-zinc-500 font-medium leading-relaxed">{{ $log->description }}</p>
                                        </div>
                                    </div>
                                    <button @click="open = !open" class="shrink-0 text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1.5 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-100 dark:border-zinc-700 hover:text-brand-500 transition-colors">
                                        <span x-text="open ? 'Recolher' : 'Detalhes'"></span>
                                    </button>
                                </div>

                                {{-- Meta-Informação Expansível --}}
                                <div x-show="open" x-collapse x-cloak class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        @if(isset($props) && is_array($props))
                                            @foreach($props as $key => $val)
                                                @if($key === 'metadata' && is_array($val))
                                                    @foreach($val as $mKey => $mVal)
                                                        @if($mVal)
                                                            <div>
                                                                <span class="text-[8px] text-zinc-400 uppercase font-black block mb-0.5">{{ $fieldLabels[$mKey] ?? $mKey }}</span>
                                                                <span class="text-[10px] font-bold dark:text-zinc-200 uppercase truncate block">{{ $mVal }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @elseif(isset($fieldLabels[$key]) && $val)
                                                    <div>
                                                        <span class="text-[8px] text-zinc-400 uppercase font-black block mb-0.5">{{ $fieldLabels[$key] }}</span>
                                                        <span class="text-[10px] font-black text-brand-600 uppercase block">
                                                            {{ $key === 'amount' ? number_format($val, 2, ',', ' ') . ' €' : $val }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <p class="text-[9px] text-zinc-400 font-bold uppercase italic">{{ $log->created_at->format('d M, H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 italic text-zinc-400 uppercase text-[10px] tracking-widest">Auditoria vazia.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ESTILOS TÉCNICOS --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
    </style>
</div> {{-- FECHO DA DIV RAIZ PRINCIPAL --}}

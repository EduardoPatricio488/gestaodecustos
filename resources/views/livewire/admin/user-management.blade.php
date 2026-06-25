<div class="space-y-6 text-left">
    {{-- HEADER E FILTROS --}}
    <x-page-header title="Gestão de Utilizadores" description="Administre permissões, estados e visualize métricas individuais.">
        <x-slot:actions>
            <div class="flex flex-wrap gap-2">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Nome ou email..." class="w-64 shadow-sm" />

                <flux:select wire:model.live="filterRole" class="w-44">
                    <option value="all">Todos os Níveis</option>
                    <option value="admin">Admin</option>
                    <option value="moderator">Moderador</option>
                    <option value="analyst">Analista</option>
                    <option value="user">Utilizador</option>
                </flux:select>

                <flux:select wire:model.live="filterStatus" class="w-32">
                    <option value="all">Todos</option>
                    <option value="active">Ativos</option>
                    <option value="inactive">Banidos</option>
                </flux:select>
                 {{-- NOVO: FILTRO PARA ORDENAR POR --}}
                <flux:select wire:model.live="orderBy" class="w-50" icon="bars-arrow-down" title="Ordenar por">
                    <option value="created_at|desc">Mais recentes primeiro</option>
                    <option value="created_at|asc">Mais antigos primeiro</option>
                    <option value="name|asc">Nome (A a Z)</option>
                    <option value="name|desc">Nome (Z a A)</option>
                    <option value="last_login_at|desc">Último acesso</option>
                    <option value="xp|desc">Maior Nível / XP</option>
                </flux:select>
            </div>
        </x-slot:actions>
    </x-page-header>
{{-- TABELA DE GESTÃO COMPLETA --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-visible text-left">
        <div class="overflow-visible min-h-[500px]">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 dark:bg-zinc-950/50 border-b border-zinc-100 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-500 tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Perfil do Utilizador</th> {{-- Nome, Username, Email, Avatar --}}
                        <th class="px-6 py-5 text-center">Nível</th>
                        <th class="px-6 py-5 text-center">Registo</th> {{-- Data de Criação --}}
                        <th class="px-6 py-5 text-center">Último Login</th>
                        <th class="px-6 py-5 text-center">Estado</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black uppercase text-zinc-500 tracking-widest">Plano</th>
                        <th class="px-8 py-5 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($users as $user)
                        <tr wire:key="row-{{ $user->id }}" wire:click="showUserDetails({{ $user->id }})" class="group cursor-pointer hover:bg-brand-500/[0.03] transition-all">

                            {{-- COLUNA: PERFIL (Nome, Username, Email, Emoji/Avatar) --}}
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    {{-- Avatar/Emoji --}}
                                    <div class="size-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-black text-xl border border-zinc-200 dark:border-zinc-700 shadow-inner group-hover:border-brand-500/50 transition-colors">
                                        {{ $user->emoji ?? substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col text-left">
                                        <p class="text-sm font-black text-zinc-900 dark:text-white leading-none group-hover:text-brand-600 transition-colors">
                                            {{ $user->name }}
                                        </p>
                                        <p class="text-[10px] font-bold text-brand-500 mt-1">
                                            @<span>{{ $user->username ?? strtolower(explode(' ', $user->name)[0]) }}</span>
                                        </p>
                                        <p class="text-[11px] text-zinc-400 mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- COLUNA: NÍVEL --}}
                            <td class="px-6 py-5 text-center">
                                <button wire:click.stop="openRoleModal({{ $user->id }})" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-brand-500 transition-all">
                                    <span class="text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-400 group-hover:text-brand-500">{{ $user->role ?? 'User' }}</span>
                                    <flux:icon name="pencil-square" variant="micro" class="size-3 text-zinc-400" />
                                </button>
                            </td>

                            {{-- COLUNA: DATA DE CRIAÇÃO --}}
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </span>
                                    <span class="text-[9px] text-zinc-400 uppercase font-black tracking-tighter">
                                        {{ $user->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </td>

                            {{-- COLUNA: ÚLTIMO LOGIN --}}
                            <td class="px-6 py-5 text-center">
                                <span class="text-[11px] font-medium text-zinc-500 italic">
                                    {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Nunca' }}
                                </span>
                            </td>

                            {{-- COLUNA: ESTADO --}}
                            <td class="px-6 py-5 text-center">
                                <flux:badge size="sm" color="{{ $user->is_active ? 'emerald' : 'red' }}" inset="top bottom" class="uppercase font-black text-[9px] px-3">
                                    {{ $user->is_active ? 'Ativo' : 'Bloqueado' }}
                                </flux:badge>
                            </td>
{{-- COLUNA: PLANO --}}
<td class="px-6 py-5 text-center">
    @php
        $plan = strtolower($user->current_plan ?? 'free');
        $planColor = match($plan) {
            'premium' => 'emerald',
            'business' => 'violet',
            default => 'zinc'
        };
    @endphp
    <flux:badge size="sm" color="{{ $planColor }}" class="uppercase font-black text-[9px] px-3">
        {{ $plan === 'free' ? 'Normal' : $plan }}
    </flux:badge>
</td>
                            {{-- AÇÕES --}}
                            <td class="px-8 py-5 text-right overflow-visible">
                                <div wire:click.stop>
                                    <flux:dropdown align="end" offset="4">
                                        <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" />
                                        <flux:menu class="min-w-[200px]">
                                            <flux:menu.item icon="eye" wire:click="showUserDetails({{ $user->id }})">Perfil Detalhado</flux:menu.item>
                                            <flux:menu.item icon="user-circle" href="{{ route('admin.impersonate', $user->id) }}">Suporte (Entrar)</flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="key" wire:click="openRoleModal({{ $user->id }})">Alterar Permissões</flux:menu.item>
                                            <flux:menu.item icon="arrow-left" wire:click="forceLogout({{ $user->id }})">Expulsar das Sessões</flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="no-symbol" variant="danger" wire:click="toggleActive({{ $user->id }})">
                                                {{ $user->is_active ? 'Bloquear Conta' : 'Desbloquear' }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30">
            {{ $users->links() }}
        </div>
    </div>








{{-- MODAL DE DETALHES COMPLETO (DOSSIÊ DO UTILIZADOR) --}}
    @if($selectedUser)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeDetails">
            <div class="relative bg-white dark:bg-zinc-900 w-full max-w-3xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 animate-in fade-in zoom-in duration-200" wire:click.stop>

                {{-- 1. CABEÇALHO DE IDENTIDADE --}}
                <div class="p-10 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20 text-left">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-6">
                            {{-- Avatar/Emoji --}}
                            <div class="size-24 rounded-[2rem] bg-brand-500 flex items-center justify-center text-white text-5xl shadow-xl shadow-brand-500/20">
                                {{ $selectedUser->emoji ?? substr($selectedUser->name, 0, 1) }}
                            </div>
                            <div class="text-left">
                                <h2 class="text-3xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter leading-none">{{ $selectedUser->name }}</h2>
                                <p class="text-brand-500 font-bold mt-1 text-lg">@<span>{{ $selectedUser->username ?? 'utilizador' }}</span></p>
                                <p class="text-zinc-500 mt-1 font-medium">{{ $selectedUser->email }}</p>
@php
    $uPlan = strtolower($userStats['plan'] ?? 'free');
    $uColor = match($uPlan) {
        'premium' => 'emerald',
        'business' => 'violet',
        default => 'zinc'
    };
@endphp
<flux:badge size="sm" color="{{ $uColor }}" class="uppercase font-black text-[9px] tracking-widest">
    Plano: {{ $uPlan === 'free' ? 'NORMAL' : strtoupper($uPlan) }}
</flux:badge>
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <flux:badge size="sm" color="zinc" class="uppercase font-black text-[9px] tracking-widest">{{ $selectedUser->role }}</flux:badge>
                                    <flux:badge size="sm" color="{{ $selectedUser->is_active ? 'emerald' : 'red' }}" class="uppercase font-black text-[9px]">
                                        {{ $selectedUser->is_active ? 'Conta Ativa' : 'Conta Bloqueada' }}
                                    </flux:badge>
                                    @if($selectedUser->email_verified_at)
                                        <flux:badge size="sm" color="blue" class="uppercase font-black text-[9px]">Verificado</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <flux:button variant="ghost" icon="x-mark" wire:click="closeDetails" class="rounded-full" />
                    </div>
                </div>

                <div class="p-10 text-left space-y-8 overflow-y-auto max-h-[60vh] custom-scrollbar">

                    {{-- 2. INFOS DE CONTA E SEGURANÇA --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-zinc-50 dark:bg-zinc-950/30 p-6 rounded-[2rem] border border-zinc-100 dark:border-zinc-800">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-400 font-black uppercase">Data de Criação</span>
                                <span class="font-bold dark:text-white">{{ $selectedUser->created_at->format('d/m/Y \à\s H:i') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-400 font-black uppercase">Último Login</span>
                                <span class="font-bold dark:text-white">{{ $selectedUser->last_login_at ? \Carbon\Carbon::parse($selectedUser->last_login_at)->format('d/m/Y H:i') : 'Nunca' }}</span>
                            </div>
                        </div>
                        <div class="space-y-3 border-l dark:border-zinc-800 pl-6">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-400 font-black uppercase">Endereço IP</span>
                                <span class="font-mono font-bold text-brand-600">{{ $selectedUser->last_ip ?? '---' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-400 font-black uppercase">Localização</span>
                                <span class="font-bold dark:text-white">Portugal, Lisboa</span> {{-- Podes tornar isto dinâmico se tiveres GeoIP --}}
                            </div>
                        </div>
                    </div>

                    {{-- 3. KPIS FINANCEIROS --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-3xl border border-zinc-100 dark:border-zinc-700">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Total Gasto</p>
                            <p class="text-lg font-black text-red-500 mt-1">{{ number_format($userStats['expenses_sum'], 2) }}€</p>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-3xl border border-zinc-100 dark:border-zinc-700">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Total Ganho</p>
                            <p class="text-lg font-black text-emerald-500 mt-1">{{ number_format($userStats['incomes_sum'], 2) }}€</p>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-3xl border border-zinc-100 dark:border-zinc-700">
                            <p class="text-[9px] font-black uppercase text-zinc-400">IA Msgs</p>
                            <p class="text-xl font-black dark:text-white mt-1">{{ $userStats['ai_messages'] }}</p>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 rounded-3xl border border-zinc-100 dark:border-zinc-700">
                            <p class="text-[9px] font-black uppercase text-zinc-400">Tarefas</p>
                            <p class="text-xl font-black dark:text-white mt-1">{{ $userStats['reminders'] }}</p>
                        </div>
                    </div>

                    {{-- 4. HÁBITOS DE NAVEGAÇÃO --}}
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em] px-2">Principais Acessos ao Site</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @forelse($userStats['page_views'] as $view)
                                <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/30 rounded-2xl border border-zinc-100 dark:border-zinc-800 transition-hover hover:border-brand-500/50">
                                    <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase truncate pr-4">{{ str_replace('Acedeu a ', '', $view->action) }}</span>
                                    <span class="px-2 py-0.5 bg-brand-500/10 text-brand-600 text-[10px] font-black rounded-lg border border-brand-500/20">{{ $view->total }}x</span>
                                </div>
                            @empty
                                <p class="text-[10px] text-zinc-400 italic px-2">Sem histórico de navegação.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- 5. PAINEL DE AÇÕES PROFISSIONAIS --}}
                    <div class="pt-8 border-t dark:border-zinc-800">
                        <h3 class="text-[10px] font-black uppercase text-red-500 tracking-[0.2em] px-2 mb-4 text-center">Painel de Controlo & Segurança</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            {{-- Reset Password --}}
                            <flux:button wire:click="resetPassword({{ $selectedUser->id }})" wire:confirm="Enviar password padrão 'password123'?" variant="ghost" size="sm" icon="key" class="rounded-xl text-[9px] font-black uppercase">Reset Pass</flux:button>

                            {{-- Verificar Email Manualmente --}}
                            @if(!$selectedUser->email_verified_at)
                                <flux:button wire:click="verifyEmailManually({{ $selectedUser->id }})" variant="ghost" size="sm" icon="check-badge" class="rounded-xl text-[9px] font-black uppercase text-emerald-600">Validar Email</flux:button>
                            @endif

                            {{-- Forçar Logout --}}
                            <flux:button wire:click="forceLogout({{ $selectedUser->id }})" variant="ghost" size="sm" icon="arrow-left" class="rounded-xl text-[9px] font-black uppercase">Kick Session</flux:button>

                            {{-- Bloquear/Desbloquear --}}
                            <flux:button wire:click="toggleActive({{ $selectedUser->id }})" variant="ghost" size="sm" icon="no-symbol" class="rounded-xl text-[9px] font-black uppercase {{ $selectedUser->is_active ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ $selectedUser->is_active ? 'Bloquear' : 'Ativar' }}
                            </flux:button>

                            {{-- Apagar Conta --}}
                            <flux:button wire:click="deleteUser({{ $selectedUser->id }})" wire:confirm="ELIMINAR CONTA DEFINITIVAMENTE?" variant="ghost" size="sm" icon="trash" class="rounded-xl text-[9px] font-black uppercase text-red-600">Apagar Conta</flux:button>
                        </div>
                    </div>
                </div>

                {{-- 6. RODAPÉ DE ACESSO --}}
                <div class="p-8 border-t dark:border-zinc-800 bg-white dark:bg-zinc-900">
                    <flux:button class="w-full h-16 rounded-2xl font-black uppercase text-xs tracking-widest shadow-2xl shadow-brand-500/20" variant="primary" href="{{ route('admin.impersonate', $selectedUser->id) }}">
                        <flux:icon name="user-circle" class="mr-2" /> Assumir Identidade da Conta
                    </flux:button>
                    <p class="text-center text-[9px] text-zinc-400 uppercase font-black mt-4 tracking-tighter italic">Todas as ações feitas neste painel são registadas nos logs de auditoria.</p>
                </div>
            </div>
        </div>
    @endif








    <flux:modal name="change-role-modal" variant="center" class="md:w-[450px] space-y-6">
    <div class="text-left">
        <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-4">Segurança: Alterar Nível</h2>

        @if($userToEditRole)
            <form wire:submit.prevent="updateRole" class="space-y-6">
                {{-- SELECT DO CARGO --}}
                <flux:select wire:model="newRole" label="Novo Cargo para {{ $userToEditRole->name }}">
                    <option value="user">Utilizador (Acesso Normal)</option>
                    <option value="analyst">Analista (Monitor de Dados)</option>
                    <option value="moderator">Moderador (Suporte e Gestão)</option>
                    <option value="admin">Administrador (Controlo Total)</option>
                </flux:select>

                {{-- PASSWORD --}}
                <flux:input
                    type="password"
                    wire:model="adminPassword"
                    label="Confirma a TUA password de Admin"
                    placeholder="Password requerida"
                />
                {{-- Mostrar erro se a pass falhar --}}
                @error('adminPassword') <p class="text-xs text-red-500 font-bold uppercase mt-1">{{ $message }}</p> @enderror

                <div class="flex gap-3 pt-4">
                    <flux:modal.close><flux:button variant="ghost" class="flex-1">Cancelar</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-1 font-black bg-brand-600">Confirmar Mudança</flux:button>
                </div>
            </form>
        @endif
    </div>
</flux:modal>
</div>

<div class="space-y-8">
    {{-- HEADER DA PÁGINA --}}
    <x-page-header title="Consola de Administração" description="Visão global e estatísticas do servidor">
        <x-slot:actions>
            <flux:button href="{{ route('admin.users') }}" variant="primary" icon="users" wire:navigate>Gerir Utilizadores</flux:button>
        </x-slot:actions>
    </x-page-header>

    {{-- STATS GLOBAIS (ESTILO STAT-CARD) --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Utilizadores Totais</p>
            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('admin.support')" wire:navigate>Tickets Suporte</flux:sidebar.item>
            <p class="mt-2 text-3xl font-black text-zinc-900 dark:text-white">{{ $totalUsers }}</p>
            <div class="mt-2 text-[10px] font-bold uppercase">
                <span class="text-emerald-500">{{ $activeUsers }} ativos</span> /
                <span class="text-red-500">{{ $bannedUsers }} banidos</span>
            </div>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Grupos Ativos</p>
            <p class="mt-2 text-3xl font-black text-zinc-900 dark:text-white">{{ $totalWorkspaces }}</p>
            <p class="mt-2 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Famílias & Empresas</p>
        </div>

        <div class="stat-card bg-brand-600 p-6 rounded-2xl border-none shadow-xl shadow-brand-500/20">
            <p class="text-xs font-bold text-white/70 uppercase tracking-widest">Volume de Dinheiro</p>
            <p class="mt-2 text-3xl font-black text-white">{{ number_format($totalCashflow, 2, ',', ' ') }} €</p>
            <p class="mt-2 text-[10px] font-bold text-white/50 uppercase tracking-widest">Total movimentado</p>
        </div>

        <div class="stat-card bg-zinc-900 p-6 rounded-2xl border-none shadow-xl">
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Estado do Servidor</p>
            <p class="mt-2 text-xl font-bold text-white">Laravel {{ $serverStatus['laravel_version'] }}</p>
            <p class="text-[10px] text-zinc-500 uppercase font-black">PHP {{ $serverStatus['php_version'] }} ({{ $serverStatus['database'] }})</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- ÚLTIMOS REGISTOS (ESTILO GLASS-CARD) --}}
        <div class="glass-card lg:col-span-2 p-6 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-6">Últimos Registos no Site</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-zinc-100 dark:border-zinc-800">
                        <tr>
                            <th class="pb-3 text-[10px] font-black uppercase text-zinc-500 px-2">Utilizador</th>
                            <th class="pb-3 text-[10px] font-black uppercase text-zinc-500 px-2">Email</th>
                            <th class="pb-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($latestUsers as $u)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                                <td class="py-4 px-2 text-sm font-bold text-zinc-900 dark:text-white">{{ $u->name }}</td>
                                <td class="py-4 px-2 text-sm text-zinc-500">{{ $u->email }}</td>
                                <td class="py-4 px-2 text-right">
                                    <flux:button href="{{ route('admin.impersonate', $u->id) }}" size="xs" variant="ghost" icon="user-circle" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                <flux:button href="{{ route('admin.users') }}" class="w-full" variant="ghost" size="sm" wire:navigate>Ver todos os utilizadores</flux:button>
            </div>
        </div>

        {{-- STATUS RÁPIDO --}}
        <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-4">
                <flux:icon name="check-circle" class="w-8 h-8 text-emerald-500" />
            </div>
            <h3 class="font-black text-zinc-900 dark:text-white uppercase tracking-widest text-sm">Sistema Online</h3>
            <p class="text-xs text-zinc-500 mt-2 max-w-[200px]">Todas as funcionalidades de partilha e workspaces estão ativas e sem erros reportados.</p>
        </div>
    </div>
</div>

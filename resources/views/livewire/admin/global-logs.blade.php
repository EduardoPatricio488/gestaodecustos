<div class="space-y-8">
    {{-- HEADER --}}
    <x-page-header title="Segurança & Logs" description="Histórico global de todas as ações realizadas na plataforma.">
        <x-slot:actions>
            <div class="flex gap-2">
                <flux:button wire:click="clearOldLogs" wire:confirm="Eliminar logs com mais de 30 dias?" variant="ghost" icon="trash" size="sm">Limpar Antigos</flux:button>
                <flux:button href="{{ route('admin.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate />
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- STATS DE MONITORIZAÇÃO --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stat-card bg-zinc-900 text-white p-6 rounded-[2rem] shadow-xl border border-zinc-800">
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.2em]">Total de Eventos</p>
            <p class="text-3xl font-black mt-1">{{ number_format($stats['total_actions'], 0, ',', ' ') }}</p>
            <p class="text-[9px] text-zinc-500 mt-2 italic uppercase">Registados desde o início</p>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em]">Utilizadores Ativos</p>
            <p class="text-3xl font-black text-brand-600 mt-1">{{ $stats['unique_users_active'] }}</p>
            <p class="text-[9px] text-zinc-500 mt-2 uppercase font-bold">Nas últimas 24 horas</p>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-red-500 tracking-[0.2em]">Último Erro do Sistema</p>
            <p class="text-[11px] font-mono text-zinc-600 dark:text-zinc-400 mt-2 leading-tight truncate">
                {{ $stats['last_error'] }}
            </p>
        </div>
    </div>

    {{-- TABELA DE EVENTOS GLOBAIS --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="p-6 border-b dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-950/30">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Fluxo de Atividade em Tempo Real</h3>
            <flux:select wire:model.live="filterAction" class="w-40 text-[10px] font-bold uppercase">
                <option value="">Todas as ações</option>
                <option value="created">Criações ➕</option>
                <option value="updated">Edições 📝</option>
                <option value="deleted">Eliminações 🗑️</option>
            </flux:select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500 tracking-tighter">Data/Hora</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500">Utilizador</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500">Ação</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500">Descrição</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                            <td class="px-6 py-4 text-xs font-bold text-zinc-400 whitespace-nowrap">
                                {{ $log->created_at->format('d/m H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[8px] font-black text-zinc-500 border dark:border-zinc-700">
                                        {{ substr($log->user->name ?? '??', 0, 2) }}
                                    </div>
                                    <span class="text-xs font-bold dark:text-white">{{ $log->user->name ?? 'Sistema' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase
                                    {{ $log->action === 'created' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $log->action === 'updated' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $log->action === 'deleted' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $log->description }}</p>
                                <p class="text-[9px] font-black text-brand-600 uppercase opacity-50 mt-0.5">{{ $log->model_type }} #{{ $log->model_id }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center text-zinc-400 italic font-medium">
                                Nenhum log registado com este filtro.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t dark:border-zinc-800 bg-zinc-50/30">
            {{ $logs->links() }}
        </div>
    </div>
</div>

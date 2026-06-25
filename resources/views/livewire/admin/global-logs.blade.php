<div class="space-y-8 text-left">
    {{-- HEADER --}}
    <x-page-header title="🛡️ Auditoria de Sistema" description="Monitorização em tempo real de todas as interações e eventos de segurança.">
        <x-slot:actions>
            <div class="flex gap-2">
                <flux:button wire:click="clearOldLogs" wire:confirm="Limpar logs com +30 dias?" variant="ghost" icon="trash" size="sm" class="text-red-500">Limpar Histórico</flux:button>
                <flux:button href="{{ route('admin.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate />
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- FILTROS AVANÇADOS --}}
    <div class="flex flex-wrap gap-4 items-center bg-white dark:bg-zinc-900 p-4 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Pesquisar utilizador ou descrição..." class="flex-1 min-w-[300px]" />

        <flux:select wire:model.live="filterType" class="w-48">
            <option value="">Todos os Tipos</option>
            <option value="seguranca">🔐 Segurança</option>
            <option value="financeiro">💰 Financeiro</option>
            <option value="ia">🤖 Inteligência IA</option>
            <option value="auth">🔑 Autenticação</option>
        </flux:select>

        <flux:select wire:model.live="filterAction" class="w-48">
            <option value="">Todas as Ações</option>
            <option value="created">➕ Criação</option>
            <option value="updated">📝 Edição</option>
            <option value="deleted">🗑️ Eliminação</option>
            <option value="login">🔓 Login</option>
        </flux:select>
    </div>

    {{-- STATS GLOBAIS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-zinc-900 text-white p-6 rounded-[2rem] shadow-xl border border-zinc-800">
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">Registos Totais</p>
            <p class="text-3xl font-black mt-1">{{ number_format($stats['total_actions']) }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-brand-600 tracking-widest">Ativos (24h)</p>
            <p class="text-3xl font-black mt-1">{{ $stats['unique_users_24h'] }} <span class="text-sm text-zinc-400">users</span></p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-red-500 tracking-widest">Alertas (7 dias)</p>
            <p class="text-3xl font-black mt-1">{{ $stats['security_alerts'] }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Último Erro Log</p>
            <p class="text-[10px] font-mono text-red-500 mt-2 leading-tight line-clamp-2 italic">
                {{ $stats['last_error'] }}
            </p>
        </div>
    </div>

    {{-- TABELA DE EVENTOS --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 dark:bg-zinc-950/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                        <th class="px-8 py-5">Timestamp</th>
                        <th class="px-6 py-5">Utilizador</th>
                        <th class="px-6 py-5">Evento / Ação</th>
                        <th class="px-6 py-5">Descrição do Log</th>
                        <th class="px-8 py-5 text-right">Info</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($logs as $log)
                        <tr wire:click="showLogDetails({{ $log->id }})" class="group cursor-pointer hover:bg-zinc-50/80 dark:hover:bg-brand-500/[0.02] transition-all">
                            <td class="px-8 py-5 text-xs font-mono text-zinc-400">
                                {{ $log->created_at->format('d/m H:i:s') }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[9px] font-black text-zinc-500 border dark:border-zinc-700">
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold dark:text-white">{{ $log->user->name ?? 'Sistema' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase border
                                    {{ $log->action === 'created' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : '' }}
                                    {{ $log->action === 'updated' ? 'bg-amber-50 text-amber-600 border-amber-200' : '' }}
                                    {{ $log->action === 'deleted' ? 'bg-red-50 text-red-600 border-red-200' : '' }}
                                    {{ !in_array($log->action, ['created','updated','deleted']) ? 'bg-zinc-100 text-zinc-600 border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700' : '' }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-xs font-medium text-zinc-600 dark:text-zinc-300 leading-snug">{{ $log->description }}</p>
                                @if($log->model_type)
                                    <p class="text-[9px] font-black text-brand-500 uppercase mt-1 opacity-60">REF: {{ str_replace('App\\Models\\', '', $log->model_type) }} #{{ $log->model_id }}</p>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right text-zinc-300 group-hover:text-brand-500 transition-colors">
                                <flux:icon name="information-circle" class="size-5" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t dark:border-zinc-800 bg-zinc-50/20">
            {{ $logs->links() }}
        </div>
    </div>

    {{-- MODAL DE DETALHES DO LOG (JSON/METADATA) --}}
    <flux:modal name="log-details-modal" variant="center" class="md:w-[600px] space-y-6 text-left">
        @if($selectedLog)
            <div class="space-y-6">
                <div class="flex items-center gap-4 border-b dark:border-zinc-800 pb-4">
                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-2xl">
                        <flux:icon name="shield-check" class="size-6 text-brand-600" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black uppercase italic tracking-tighter dark:text-white">Detalhes do Evento</h2>
                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Log ID: #{{ $selectedLog->id }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-[10px] font-black text-zinc-400 uppercase">Utilizador</p>
                        <p class="font-bold dark:text-white">{{ $selectedLog->user->name ?? 'Sistema' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-zinc-400 uppercase">Data Completa</p>
                        <p class="font-bold dark:text-white">{{ $selectedLog->created_at->format('d/m/Y - H:i:s') }}</p>
                    </div>
                </div>

                <div class="p-5 bg-zinc-950 rounded-2xl border border-zinc-800 overflow-hidden">
                    <p class="text-[10px] font-black text-zinc-500 uppercase mb-3 tracking-widest">Metadata / Dados Brutos</p>
                    <pre class="text-[11px] font-mono text-emerald-500 overflow-x-auto leading-relaxed">
@if($selectedLog->metadata)
{{ json_encode($selectedLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
@else
"Nenhum dado extra registado para este evento."
@endif
                    </pre>
                </div>

                <div class="flex gap-3">
                    <flux:modal.close class="flex-1"><flux:button variant="ghost" class="w-full font-black uppercase text-xs">Fechar Dossiê</flux:button></flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</div>

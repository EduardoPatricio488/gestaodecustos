<div class="space-y-8 text-left">
    <x-page-header title="🔔 Monitor de Lembretes" description="Análise global de produtividade e tarefas criadas pelos utilizadores.">
        <x-slot:actions>
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Pesquisar tarefas..." class="w-80" />
        </x-slot:actions>
    </x-page-header>

    {{-- KPIS DE PRODUTIVIDADE --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stat-card bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Total Criado</p>
            <p class="text-4xl font-black text-zinc-900 dark:text-white mt-2">{{ number_format($stats['total']) }}</p>
            <p class="text-[10px] text-emerald-500 font-bold mt-2">+{{ $stats['today'] }} hoje</p>
        </div>

        <div class="stat-card bg-zinc-900 text-white p-8 rounded-[2rem] border border-zinc-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">Taxa de Conclusão</p>
                <p class="text-4xl font-black text-brand-400 mt-2">{{ $stats['rate'] }}%</p>
                <div class="w-full bg-zinc-800 h-1.5 rounded-full mt-4">
                    <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ $stats['rate'] }}%"></div>
                </div>
            </div>
            <flux:icon name="check-circle" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Lembretes Concluídos</p>
            <p class="text-4xl font-black text-emerald-600 mt-2">{{ number_format($stats['completed']) }}</p>
            <p class="text-[10px] text-zinc-400 font-bold mt-2 uppercase italic">Tarefas finalizadas</p>
        </div>
    </div>

    {{-- TABELA DE ATIVIDADE --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-100 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-500 tracking-widest">
                <tr>
                    <th class="px-8 py-4">Utilizador</th>
                    <th class="px-8 py-4">Lembrete / Tarefa</th>
                    <th class="px-8 py-4">Data Limite</th>
                    <th class="px-8 py-4 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($reminders as $r)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                        <td class="px-8 py-4">
                            <span class="text-xs font-bold dark:text-white">{{ $r->user_name }}</span>
                        </td>
                        <td class="px-8 py-4">
                            <p class="text-sm font-medium dark:text-zinc-300 leading-tight">{{ $r->title }}</p>
                        </td>
                        <td class="px-8 py-4">
                            <span class="text-xs text-zinc-500 font-mono">
                                {{ $r->due_date ? \Carbon\Carbon::parse($r->due_date)->format('d/m/Y') : 'Sem data' }}
                            </span>
                        </td>
                        <td class="px-8 py-4 text-center">
                            @if($r->completed)
                                <flux:badge size="sm" color="emerald" variant="outline" class="uppercase font-black text-[9px]">Concluído</flux:badge>
                            @else
                                <flux:badge size="sm" color="amber" variant="outline" class="uppercase font-black text-[9px]">Pendente</flux:badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-20 text-center text-zinc-500 italic">Nenhum lembrete registado.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t dark:border-zinc-800">
            {{ $reminders->links() }}
        </div>
    </div>
</div>

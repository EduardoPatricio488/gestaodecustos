<div class="space-y-8">
    {{-- HEADER --}}
    <x-page-header title="Transparência do Grupo" description="Análise detalhada de performance e atividade: {{ $workspaceName }}">
        <x-slot:actions>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate>Voltar</flux:button>
        </x-slot:actions>
    </x-page-header>

    {{-- 1. BALANÇO FINANCEIRO POR MEMBRO --}}
    <div class="space-y-4">
        <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2">Performance Individual (Mês Atual)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($memberStats as $user)
                <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-black text-xs shadow-lg uppercase">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div>
                            <p class="text-lg font-black dark:text-white leading-none">{{ $user->name }}</p>
                            <p class="text-[10px] text-zinc-500 uppercase font-bold mt-1">Lvl {{ $user->level }} · {{ $user->xp }} XP</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase">Ganhou</p>
                            <p class="text-md font-bold text-emerald-600">+{{ number_format($user->total_incomes, 2) }}€</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-zinc-400 uppercase">Gastou</p>
                            <p class="text-md font-bold text-red-500">-{{ number_format($user->total_expenses, 2) }}€</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase text-zinc-500 tracking-tighter">Balanço Líquido</span>
                        <span class="text-sm font-black {{ $user->net_balance >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ number_format($user->net_balance, 2) }} €
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- 2. RANKING DE ATIVIDADE --}}
        <div class="space-y-4 lg:col-span-1">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2">Top Contribuintes (Registos)</h2>
            <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                @foreach($topRecorders as $index => $user)
                    <div class="flex items-center justify-between p-3 border-b border-zinc-50 dark:border-zinc-800 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-zinc-400 w-4">{{ $index + 1 }}º</span>
                            <p class="text-sm font-bold dark:text-white">{{ explode(' ', $user->name)[0] }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 text-[10px] font-black">{{ $user->expenses_count }} gastos</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 3. LOG DE ATIVIDADE (O QUE FIZERAM NA CONTA) --}}
        <div class="space-y-4 lg:col-span-2">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400 px-2 text-zinc-400">Histórico Recente da Conta</h2>
            <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-100 dark:border-zinc-800">
                        <tr>
                            <th class="p-4 text-[10px] font-black uppercase text-zinc-500">Membro</th>
                            <th class="p-4 text-[10px] font-black uppercase text-zinc-500">Ação Realizada</th>
                            <th class="p-4 text-right text-[10px] font-black uppercase text-zinc-500">Quando</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($recentActivities as $log)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[8px] font-black uppercase text-zinc-500 border dark:border-zinc-700">
                                            {{ substr($log->user->name, 0, 2) }}
                                        </div>
                                        <span class="text-xs font-bold dark:text-zinc-200">{{ explode(' ', $log->user->name)[0] }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-zinc-600 dark:text-zinc-400">
                                            {{ $log->description }}
                                        </span>
                                        <span class="text-[9px] font-black uppercase text-brand-600 opacity-70">{{ $log->model_type }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-right">
                                    <span class="text-[10px] text-zinc-400 font-medium italic">{{ $log->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

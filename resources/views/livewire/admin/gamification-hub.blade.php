<div class="space-y-8 text-left">
    <x-page-header title="⭐ Sistema de Gamificação" description="Gere o engagement, atribua medalhas e monitorize o ranking global de utilizadores.">
    </x-page-header>

    {{-- STATS GLOBAIS DE ENGAGEMENT --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-left">
        <div class="stat-card bg-zinc-900 text-white p-6 rounded-[2rem] border border-zinc-800 shadow-xl">
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">Esforço Total (XP)</p>
            <p class="text-3xl font-black mt-1 text-brand-400">{{ number_format($stats['total_xp']) }}</p>
        </div>
        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Moedas em Circulação</p>
            <p class="text-3xl font-black mt-1">{{ number_format($stats['total_points']) }}</p>
        </div>
        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nível Médio</p>
            <p class="text-3xl font-black mt-1">Lvl {{ $stats['avg_level'] }}</p>
        </div>
        <div class="stat-card bg-brand-500 text-white p-6 rounded-[2rem] shadow-lg shadow-brand-500/20 text-left">
            <p class="text-[10px] font-black uppercase text-white/70 tracking-widest">Líder do Ranking</p>
            <p class="text-lg font-black mt-1 truncate italic tracking-tighter">{{ $stats['top_player']->name ?? '---' }}</p>
            <p class="text-[9px] font-bold uppercase mt-1">Nível {{ $stats['top_player']->level ?? 0 }} • {{ number_format($stats['top_player']->xp ?? 0) }} XP</p>
        </div>
    </div>

    {{-- RANKING DE UTILIZADORES --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden text-left">
        <div class="p-6 border-b dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-950/20">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Ranking de Utilizadores</h3>
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Pesquisar por nome..." size="sm" class="w-72" icon="magnifying-glass" />
        </div>

        <div class="overflow-x-auto text-left">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 dark:bg-zinc-950/50 border-b border-zinc-100 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-500">
                    <tr>
                        <th class="px-8 py-4">Posição</th>
                        <th class="px-6 py-4">Utilizador</th>
                        <th class="px-6 py-4 text-center">Nível / Progresso</th>
                        <th class="px-6 py-4 text-center">Pontos / XP</th>
                        <th class="px-6 py-4 text-center">Streak</th>
                        <th class="px-8 py-4 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($ranking as $index => $u)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-brand-500/[0.02] transition-colors">
                            <td class="px-8 py-4">
                                <div class="size-8 rounded-xl {{ $loop->first ? 'bg-amber-100 text-amber-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }} flex items-center justify-center text-xs font-black italic">
                                    #{{ ($ranking->currentPage() - 1) * $ranking->perPage() + $loop->iteration }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4 text-left">
                                    <div class="size-10 rounded-2xl bg-brand-500 flex items-center justify-center text-white text-xs font-black">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-zinc-900 dark:text-white leading-none">{{ $u->name }}</span>
                                        <span class="text-[10px] text-zinc-400 mt-1 italic">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-[10px] font-black text-brand-600 uppercase">Lvl {{ $u->level }}</span>
                                    <div class="w-24 bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden border dark:border-zinc-700">
                                        <div class="bg-brand-500 h-full" style="width: {{ ($u->xp % 1000) / 10 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-zinc-900 dark:text-white leading-none">{{ number_format($u->points) }} <small class="text-[8px] uppercase">Moedas</small></span>
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase mt-1">{{ number_format($u->xp) }} XP Total</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-500/10 text-orange-600 border border-orange-500/20 shadow-sm">
                                    <flux:icon name="fire" class="size-3.5" />
                                    <span class="text-xs font-black">{{ $u->streak }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <flux:modal.trigger name="edit-gamification">
                                    <flux:button wire:click="$set('selectedUserId', {{ $u->id }})" size="xs" variant="ghost" icon="pencil-square" class="rounded-lg" />
                                </flux:modal.trigger>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t dark:border-zinc-800 bg-zinc-50/30">
            {{ $ranking->links() }}
        </div>
    </div>

    {{-- MODAL DE AJUSTE (CENTRADO) --}}
    <flux:modal name="edit-gamification" variant="center" class="md:w-[450px] space-y-8 text-left">
        <div class="text-left">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 bg-brand-500 text-white rounded-2xl shadow-lg">
                    <flux:icon name="star" class="size-6" />
                </div>
                <div>
                    <flux:heading size="lg" class="font-black uppercase italic tracking-tighter">Gerir Recompensas</flux:heading>
                    <p class="text-xs text-zinc-500 font-medium">Atribua benefícios manuais ao utilizador selecionado.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Secção 1: Moedas --}}
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b pb-2">Atribuir Moedas / Pontos</h4>
                    <div class="flex gap-3 items-end">
                        <div class="flex-1">
                            <flux:input wire:model="pointsToAdd" type="number" label="Quantidade de Moedas" placeholder="Ex: 50" icon="circle-stack" />
                        </div>
                        <flux:button wire:click="awardPoints" variant="primary" class="h-11 font-black uppercase text-[10px] tracking-widest">Atribuir</flux:button>
                    </div>
                    <p class="text-[9px] text-zinc-400 italic">Cada moeda equivale a 10 pontos de experiência (XP).</p>
                </div>

                {{-- Secção 2: Medalhas --}}
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b pb-2">Atribuir Medalha Especial</h4>
                    <div class="flex gap-3 items-end">
                        <div class="flex-1">
                            <flux:select wire:model="badgeToAssign" label="Selecionar Medalha">
                                <option value="">Escolher uma medalha...</option>
                                @foreach($availableBadges as $badge)
                                    <option value="{{ $badge['id'] }}">{{ $badge['icon'] }} {{ $badge['name'] }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <flux:button wire:click="assignBadge" variant="filled" class="h-11 font-black uppercase text-[10px] tracking-widest">Enviar</flux:button>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t dark:border-zinc-800">
                <flux:modal.close><flux:button variant="ghost" class="w-full font-black uppercase text-xs tracking-widest">Fechar Janela</flux:button></flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>

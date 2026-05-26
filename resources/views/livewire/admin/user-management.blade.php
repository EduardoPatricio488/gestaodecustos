<div class="space-y-6">
    {{-- HEADER DA PÁGINA --}}
    <x-page-header title="Gestão de Utilizadores" description="Monitorize, bana ou aceda às contas dos utilizadores para suporte.">
        <x-slot:actions>
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Procurar por nome ou email..." class="max-w-sm" />
        </x-slot:actions>
    </x-page-header>

    {{-- TABELA DE UTILIZADORES (ESTILO GLASS-CARD) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500 tracking-widest">Utilizador</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500 tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500 tracking-widest">Registado em</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase text-zinc-500 tracking-widest px-8">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                            {{-- Informação do User --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center text-white font-black text-xs uppercase shadow-sm">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-[11px] text-zinc-500 font-medium">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Badge de Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        Banido
                                    </span>
                                @endif
                            </td>

                            {{-- Data --}}
                            <td class="px-6 py-4">
                                <p class="text-xs text-zinc-500 font-medium italic">{{ $user->created_at->format('d/m/Y') }}</p>
                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-4 text-right px-8">
                                <div class="flex justify-end gap-2">
                                    {{-- Botão Suporte (Entrar na conta) --}}
                                    <flux:button href="{{ route('admin.impersonate', $user->id) }}" size="xs" variant="ghost" icon="user-circle" title="Suporte: Entrar na conta" />

                                    {{-- Botão Banir/Ativar --}}
                                    @if($user->id !== auth()->id())
                                        <flux:button wire:click="toggleActive({{ $user->id }})" size="xs" variant="ghost" icon="no-symbol" color="{{ $user->is_active ? 'red' : 'emerald' }}" title="{{ $user->is_active ? 'Banir' : 'Reativar' }}" />

                                        {{-- Eliminar --}}
                                        <flux:button wire:click="deleteUser({{ $user->id }})" wire:confirm="Tens a certeza? Esta ação é IRREVERSÍVEL." size="xs" variant="ghost" icon="trash" color="red" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-zinc-500 italic text-sm">
                                Nenhum utilizador encontrado com esses critérios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINAÇÃO --}}
        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-800/10">
            {{ $users->links() }}
        </div>
    </div>
</div>

<div class="space-y-10 pb-24">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="absolute inset-0 bg-violet-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="users" class="w-10 h-10 text-violet-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Dividir Despesas</h1>
                    <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Split Hub</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Divide despesas com o grupo e acompanha quem pagou</p>
            </div>
        </div>

        <flux:button wire:click="openModal" variant="primary" icon="plus"
            class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-violet-500/20 !bg-violet-600 hover:!bg-violet-500 border-none">
            Nova Divisão
        </flux:button>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-950 text-white p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-8 -top-8 size-32 bg-red-500/10 blur-3xl rounded-full"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-red-400 mb-2">Devo a Outros</p>
            <h3 class="text-5xl font-black tracking-tighter italic">{{ number_format($totalIOwe, 2, ',', '.') }}<small class="text-xl ml-1">€</small></h3>
            <p class="text-[10px] text-zinc-500 mt-2 uppercase font-bold">{{ $iOwe->count() }} divisões pendentes</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600 mb-2">Outros Devem-me</p>
            <h3 class="text-5xl font-black tracking-tighter italic text-emerald-500">{{ number_format($totalTheyOwe, 2, ',', '.') }}<small class="text-xl ml-1">€</small></h3>
            <p class="text-[10px] text-zinc-400 mt-2 uppercase font-bold">{{ $theyOwe->count() }} divisões por receber</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-2">Saldo Líquido</p>
            @php $balance = $totalTheyOwe - $totalIOwe; @endphp
            <h3 class="text-5xl font-black tracking-tighter italic {{ $balance >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                {{ $balance >= 0 ? '+' : '' }}{{ number_format($balance, 2, ',', '.') }}<small class="text-xl ml-1">€</small>
            </h3>
            <p class="text-[10px] text-zinc-400 mt-2 uppercase font-bold">{{ $settled->count() }} divisões liquidadas</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['mine' => 'Tudo', 'owe' => 'Devo ('.$iOwe->count().')', 'owed' => 'A Receber ('.$theyOwe->count().')', 'settled' => 'Liquidadas'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all
                {{ $activeTab === $tab ? 'bg-violet-600 text-white shadow-lg shadow-violet-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- LISTA --}}
    @php
        $displayed = match($activeTab) {
            'owe'     => $iOwe,
            'owed'    => $theyOwe,
            'settled' => $settled,
            default   => $allSplits,
        };
    @endphp

    <div class="space-y-4">
        @forelse($displayed as $split)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm
                {{ $split->isFullySettled() ? 'opacity-70' : '' }}">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-2xl bg-violet-500/10 flex items-center justify-center text-violet-600 shrink-0">
                            <flux:icon name="receipt-percent" class="size-6" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <p class="font-black dark:text-white uppercase tracking-tight">{{ $split->title }}</p>
                                @if($split->isFullySettled())
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase">✓ Liquidado</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-zinc-400 font-bold uppercase mt-1">
                                {{ $split->spent_at->format('d/m/Y') }} · Criado por {{ $split->creator->name }}
                                @if($split->category) · {{ $split->category->name }} @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 shrink-0">
                        <div class="text-right">
                            <p class="text-2xl font-black dark:text-white">{{ number_format($split->total_amount, 2, ',', '.') }}€</p>
                            <p class="text-[9px] text-zinc-400 uppercase font-bold">Total · {{ $split->participants->count() }} pessoas</p>
                        </div>

                        @if($split->creator_user_id === auth()->id())
                            <button wire:click="deleteSplit({{ $split->id }})" wire:confirm="Eliminar esta divisão?"
                                class="p-2 rounded-xl text-zinc-300 dark:text-zinc-700 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                                <flux:icon name="trash" class="size-4" />
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Participantes --}}
                <div class="mt-5 pt-5 border-t border-zinc-100 dark:border-zinc-800 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($split->participants as $participant)
                        <div class="flex items-center justify-between p-3 rounded-2xl {{ $participant->paid ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800' : 'bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700' }}">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-xl {{ $participant->paid ? 'bg-emerald-500' : 'bg-zinc-200 dark:bg-zinc-700' }} flex items-center justify-center text-white text-[10px] font-black">
                                    {{ substr($participant->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black dark:text-white leading-none">{{ explode(' ', $participant->user->name)[0] }}</p>
                                    <p class="text-[9px] text-zinc-400 font-bold">{{ number_format($participant->amount, 2, ',', '.') }}€</p>
                                </div>
                            </div>

                            @if($split->creator_user_id === auth()->id() || $participant->user_id === auth()->id())
                                <button wire:click="togglePaid({{ $participant->id }})"
                                    class="px-3 py-1 rounded-xl text-[9px] font-black uppercase transition-all
                                    {{ $participant->paid
                                        ? 'bg-emerald-500/10 text-emerald-600 hover:bg-red-50 hover:text-red-500'
                                        : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-emerald-100 hover:text-emerald-700' }}">
                                    {{ $participant->paid ? '✓ Pago' : 'Pagar' }}
                                </button>
                            @else
                                <span class="px-3 py-1 rounded-xl text-[9px] font-black uppercase
                                    {{ $participant->paid ? 'bg-emerald-500/10 text-emerald-600' : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-400' }}">
                                    {{ $participant->paid ? '✓ Pago' : 'Pendente' }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($split->notes)
                    <p class="mt-3 text-xs text-zinc-400 italic">{{ $split->notes }}</p>
                @endif
            </div>
        @empty
            <div class="py-24 text-center bg-white dark:bg-zinc-900 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                <flux:icon name="users" class="size-12 mx-auto mb-4 text-zinc-200 dark:text-zinc-700" />
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Nenhuma divisão encontrada</p>
                <button wire:click="openModal" class="mt-6 px-6 py-3 bg-violet-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-violet-500 transition-all">
                    Criar Primeira Divisão
                </button>
            </div>
        @endforelse
    </div>

    {{-- MODAL --}}
    @if($showModal)
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-zinc-950/70 backdrop-blur-xl"
            x-data x-on:keydown.escape.window="$wire.closeModal()">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-2xl rounded-[2.5rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden max-h-[90vh] overflow-y-auto">

                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h2 class="text-xl font-black dark:text-white uppercase tracking-tighter italic">Nova Divisão de Despesa</h2>
                    <button wire:click="closeModal" class="p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 transition-all">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <div class="p-8 space-y-6">

                    {{-- Título e valor --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Descrição</label>
                            <input wire:model.live="title" type="text" placeholder="Ex: Jantar de aniversário"
                                class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-violet-500/30" />
                            @error('title') <p class="text-xs text-red-500 px-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Total (€)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-sm">€</span>
                                <input wire:model.live="totalAmount" type="number" min="0" step="0.01"
                                    class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl pl-9 pr-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-violet-500/30" />
                            </div>
                            @error('totalAmount') <p class="text-xs text-red-500 px-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Data e tipo --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Data</label>
                            <input wire:model="spentAt" type="date"
                                class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-violet-500/30" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Divisão</label>
                            <select wire:model.live="splitType"
                                class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-violet-500/30">
                                <option value="equal">Partes Iguais</option>
                                <option value="custom">Valores Personalizados</option>
                            </select>
                        </div>
                    </div>

                    {{-- Participantes --}}
                    <div class="space-y-3">
                        <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Quem Participa</label>
                        @error('selectedUsers') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                        <div class="grid grid-cols-1 gap-2">
                            @foreach($members as $member)
                                <label class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-2xl cursor-pointer hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-all border-2
                                    {{ in_array($member->id, $selectedUsers) ? 'border-violet-500/50 bg-violet-50 dark:bg-violet-900/20' : 'border-transparent' }}">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" wire:model.live="selectedUsers" value="{{ $member->id }}"
                                            {{ $member->id === auth()->id() ? 'disabled checked' : '' }}
                                            class="rounded text-violet-600">
                                        <div class="size-8 rounded-xl bg-violet-500/10 flex items-center justify-center text-violet-600 font-black text-[10px]">
                                            {{ substr($member->name, 0, 2) }}
                                        </div>
                                        <span class="text-sm font-bold dark:text-white">
                                            {{ $member->name }}
                                            @if($member->id === auth()->id()) <span class="text-[9px] text-zinc-400">(Tu)</span> @endif
                                        </span>
                                    </div>

                                    @if($splitType === 'custom' && in_array($member->id, $selectedUsers))
                                        <div class="relative w-28">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs font-bold">€</span>
                                            <input wire:model.live="customAmounts.{{ $member->id }}" type="number" min="0" step="0.01"
                                                class="w-full h-9 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl pl-7 pr-3 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-violet-500/20" />
                                        </div>
                                    @elseif(in_array($member->id, $selectedUsers))
                                        <span class="text-sm font-black text-violet-600">
                                            {{ $totalAmount > 0 && count($selectedUsers) > 0 ? number_format($totalAmount / count($selectedUsers), 2, ',', '.') : '0,00' }}€
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Notas (opcional)</label>
                        <textarea wire:model="notes" rows="2" placeholder="Detalhe da despesa..."
                            class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 text-sm font-medium dark:text-white outline-none focus:ring-2 focus:ring-violet-500/30 resize-none"></textarea>
                    </div>
                </div>

                <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 flex gap-3 justify-end">
                    <flux:button wire:click="closeModal" variant="ghost" class="rounded-2xl font-black uppercase text-xs tracking-widest">
                        Cancelar
                    </flux:button>
                    <flux:button wire:click="save" variant="primary"
                        class="rounded-2xl px-8 font-black uppercase text-xs tracking-widest !bg-violet-600 hover:!bg-violet-500 border-none shadow-lg shadow-violet-500/20">
                        Guardar Divisão
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div>

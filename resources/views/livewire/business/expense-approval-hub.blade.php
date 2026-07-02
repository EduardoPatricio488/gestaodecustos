<div class="space-y-8 pb-24">
    <div class="flex items-center gap-5">
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
            <flux:icon name="clipboard-document-check" class="w-8 h-8 text-amber-600" />
        </div>
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Aprovação de Despesas</h1>
            <p class="text-xs text-zinc-400 mt-1">Workflow colaborador → gestor → contabilidade</p>
        </div>
    </div>

    @if(!$isManager)
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 max-w-xl space-y-4">
            <h3 class="font-black dark:text-white">Submeter Despesa</h3>
            <flux:input wire:model="title" label="Título" placeholder="Ex: Material de escritório" />
            <flux:input wire:model="amount" type="number" label="Valor (€)" step="0.01" />
            <flux:select wire:model="category_id" label="Categoria">
                <option value="">— Selecionar —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </flux:select>
            <flux:textarea wire:model="description" label="Descrição" rows="2" />
            <flux:input wire:model="spent_at" type="date" label="Data" />
            <flux:button wire:click="submit" variant="primary" class="rounded-2xl font-black">Submeter para Aprovação</flux:button>
        </div>
    @endif

    @if($isManager && $pending->isNotEmpty())
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest text-amber-500 mb-4">Pendentes ({{ $pending->count() }})</h3>
            <div class="space-y-3">
                @foreach($pending as $item)
                    <div class="bg-white dark:bg-zinc-900 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 flex flex-col md:flex-row justify-between gap-4">
                        <div>
                            <p class="font-black dark:text-white">{{ $item->title }}</p>
                            <p class="text-sm text-zinc-400">{{ $item->user->name }} · {{ $item->spent_at->format('d/m/Y') }}</p>
                            @if($item->description)<p class="text-xs text-zinc-500 mt-1">{{ $item->description }}</p>@endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-black text-amber-600 tabular-nums">{{ number_format($item->amount, 2, ',', '.') }}€</span>
                            <flux:button wire:click="approve({{ $item->id }})" variant="primary" size="sm" class="rounded-xl">Aprovar</flux:button>
                            <flux:button wire:click="reject({{ $item->id }})" variant="danger" size="sm" class="rounded-xl">Rejeitar</flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($history->isNotEmpty())
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Histórico</h3>
            <div class="space-y-2">
                @foreach($history as $item)
                    <div class="flex justify-between items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                        <div>
                            <p class="font-bold text-sm dark:text-white">{{ $item->title }}</p>
                            <p class="text-[10px] text-zinc-400">{{ $item->user->name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-black uppercase px-2 py-1 rounded-full
                                {{ $item->status === 'approved' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                {{ $item->status }}
                            </span>
                            <p class="text-sm font-black tabular-nums mt-1 dark:text-white">{{ number_format($item->amount, 2, ',', '.') }}€</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

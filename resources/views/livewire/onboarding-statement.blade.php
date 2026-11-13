<div class="space-y-5">
    @if($confirmedMovements->isNotEmpty())
        <section class="rounded-3xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900/60 dark:bg-emerald-950/20">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-black text-zinc-900 dark:text-white">Movimentos já confirmados</h2>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $confirmedMovements->count() }} movimentos importados para este espaço financeiro</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">Confirmados</span>
            </div>
            <div class="max-h-64 space-y-2 overflow-y-auto pr-1">
                @foreach($confirmedMovements as $movement)
                    <div wire:key="onboarding-confirmed-movement-{{ $loop->index }}" class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-white p-3 dark:border-emerald-900/40 dark:bg-zinc-900">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-zinc-900 dark:text-white">{{ $movement['description'] ?: 'Movimento bancário' }}</p>
                            <p class="mt-1 text-[10px] text-zinc-500 dark:text-zinc-400">{{ $movement['date'] ? \Carbon\Carbon::parse($movement['date'])->format('d/m/Y') : 'Data não disponível' }} · {{ $movement['type'] }} · {{ $movement['category'] }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-black {{ in_array($movement['type'], ['Receita', 'Transferência recebida'], true) ? 'text-emerald-600' : 'text-red-600' }}">{{ in_array($movement['type'], ['Receita', 'Transferência recebida'], true) ? '+' : '−' }}{{ number_format($movement['amount'], 2, ',', '.') }} €</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if(! $showImportForm)
        <button type="button" wire:click="openImportForm" class="inline-flex h-12 items-center gap-2 rounded-2xl bg-blue-600 px-5 text-sm font-bold text-white hover:bg-blue-700">
            <flux:icon name="arrow-up-tray" class="size-4" /> Importar extrato
        </button>
    @else
        @include('livewire.partials.statement-import-form', $data)
    @endif
</div>

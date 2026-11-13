<div class="space-y-8 pb-24">
    <div class="flex items-center gap-5 px-1">
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
            <flux:icon name="arrow-down-tray" class="w-8 h-8 text-blue-600" />
        </div>
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Importar Movimentos</h1>
            <p class="text-xs text-zinc-400 mt-1">Importa despesas e transferências da tua conta bancária, com revisão antes de guardar.</p>
        </div>
    </div>

    @include('livewire.partials.statement-import-form')

    @if($imports->isNotEmpty())
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Histórico de importações</h3>
            <div class="space-y-2">
                @foreach($imports as $import)
                    <div class="flex justify-between items-center gap-3 p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-sm dark:text-white">{{ $import->filename }}</p>
                            <p class="text-[10px] text-zinc-400">{{ $import->created_at->format('d/m/Y H:i') }} · {{ $import->transactions_imported }} movimentos importados</p>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <flux:button type="button" size="xs" variant="ghost" wire:click="viewImportMovements({{ $import->id }})">Ver movimentos</flux:button>
                                <flux:button type="button" size="xs" variant="danger" wire:click="deleteImport({{ $import->id }})" wire:confirm="Esta ação vai eliminar esta importação e todos os movimentos associados: despesas, transferências bancárias e registos de dívidas. Queres continuar?">Apagar importação</flux:button>
                            </div>
                        </div>
                        <span class="shrink-0 text-[9px] font-black uppercase px-2 py-1 rounded-full {{ $import->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                            {{ $import->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($viewingImportId)
        <div class="bg-white dark:bg-zinc-900 border border-blue-200 dark:border-blue-900 rounded-3xl p-5 sm:p-7 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-black dark:text-white">Movimentos desta importação</h3>
                    <p class="text-xs text-zinc-400">{{ count($viewingImportMovements) }} registos associados</p>
                </div>
                <flux:button type="button" size="sm" variant="ghost" wire:click="closeImportMovements">Fechar</flux:button>
            </div>
            @forelse($viewingImportMovements as $movement)
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold dark:text-white">{{ $movement['description'] }}</p>
                        <p class="text-[11px] text-zinc-400">{{ \Carbon\Carbon::parse($movement['date'])->format('d/m/Y') }} · {{ $movement['type'] }} · {{ $movement['category'] }}</p>
                    </div>
                    <span class="text-sm font-black {{ $movement['type'] === 'Receita' || str_contains($movement['type'], 'recebida') ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $movement['type'] === 'Receita' || str_contains($movement['type'], 'recebida') ? '+' : '−' }}{{ number_format($movement['amount'], 2, ',', '.') }} €
                    </span>
                </div>
            @empty
                <p class="text-sm text-zinc-400">Esta importação não tem movimentos financeiros associados.</p>
            @endforelse
        </div>
    @endif
</div>

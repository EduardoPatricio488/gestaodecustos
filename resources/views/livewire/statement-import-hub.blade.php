<div class="space-y-8 pb-24">
    <div class="flex items-center gap-5 px-1">
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
            <flux:icon name="arrow-up-tray" class="w-8 h-8 text-blue-600" />
        </div>
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Importar Extrato</h1>
            <p class="text-xs text-zinc-400 mt-1">CSV de CGD, Millennium, Revolut, N26 — categorização automática</p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 max-w-2xl">
        <form wire:submit="importStatement" class="space-y-6">
            <div class="border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-2xl p-8 text-center">
                <flux:icon name="document-arrow-up" class="size-12 text-zinc-300 mx-auto mb-4" />
                <input type="file" wire:model="statementFile" accept=".csv,.txt" class="text-sm" />
                <p class="text-[10px] text-zinc-400 mt-3">Formatos: CSV (máx. 5MB)</p>
            </div>
            <div wire:loading wire:target="statementFile,importStatement" class="text-sm text-blue-500 font-bold">
                A processar extrato...
            </div>
            <flux:button type="submit" variant="primary" class="w-full rounded-2xl font-black" :disabled="!$statementFile">
                Importar e Categorizar
            </flux:button>
        </form>

        @if($lastImport)
            <div class="mt-6 p-4 rounded-2xl {{ $lastImport->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'bg-red-50 dark:bg-red-950/30' }}">
                <p class="font-black text-sm">
                    {{ $lastImport->status === 'completed' ? '✓ Importação concluída' : '✗ Falha na importação' }}
                </p>
                <p class="text-xs mt-1">
                    {{ $lastImport->transactions_imported }} de {{ $lastImport->transactions_total }} transações
                    @if($lastImport->bank_detected) · Banco: {{ strtoupper($lastImport->bank_detected) }} @endif
                </p>
            </div>
        @endif
    </div>

    @if($imports->isNotEmpty())
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Histórico</h3>
            <div class="space-y-2">
                @foreach($imports as $import)
                    <div class="flex justify-between items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                        <div>
                            <p class="font-bold text-sm dark:text-white">{{ $import->filename }}</p>
                            <p class="text-[10px] text-zinc-400">{{ $import->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="text-[9px] font-black uppercase px-2 py-1 rounded-full
                            {{ $import->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                            {{ $import->status }} · {{ $import->transactions_imported }} tx
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

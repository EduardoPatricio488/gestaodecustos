<div class="space-y-8 pb-24">
    <div class="flex items-center gap-5">
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
            <flux:icon name="document-text" class="w-8 h-8 text-blue-600" />
        </div>
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">e-Fatura / AT</h1>
            <p class="text-xs text-zinc-400 mt-1">Importação CSV do portal AT · Validação NIF · Alertas IVA trimestral</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6">
            <h3 class="font-black dark:text-white uppercase tracking-widest text-sm mb-4">Importar Faturas (CSV AT)</h3>
            <form wire:submit="import" class="space-y-4">
                <input type="file" wire:model="importFile" accept=".csv,.txt" class="text-sm w-full" />
                <flux:button type="submit" variant="primary" class="rounded-xl font-black">Importar</flux:button>
            </form>
            @if($lastImport)
                <p class="mt-4 text-sm text-emerald-600 font-bold">
                    {{ $lastImport['imported'] }} importadas · {{ $lastImport['skipped'] }} ignoradas · IVA: {{ number_format($lastImport['totalVat'], 2, ',', '.') }}€
                </p>
            @endif
        </div>

        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-3xl p-6">
            <h3 class="font-black text-amber-800 dark:text-amber-300 uppercase tracking-widest text-sm mb-3">IVA Trimestral</h3>
            <p class="text-2xl font-black text-amber-700 dark:text-amber-400">{{ $vatSummary['quarter'] }}</p>
            <p class="text-sm text-amber-800/80 mt-2">{{ $vatSummary['count'] }} faturas</p>
            <p class="text-lg font-black text-amber-700 mt-1">IVA: {{ number_format($vatSummary['vat'], 2, ',', '.') }}€</p>
            <p class="text-[10px] text-amber-600 mt-3 font-bold">Entrega prevista: {{ $vatSummary['due_date'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6">
        <h3 class="font-black dark:text-white uppercase tracking-widest text-sm mb-4">Validar NIF</h3>
        <div class="flex gap-3 max-w-md">
            <flux:input wire:model="nifToValidate" placeholder="123456789" class="flex-1" />
            <flux:button wire:click="validateNif" variant="primary" class="rounded-xl">Validar</flux:button>
        </div>
        @if($nifValid !== null)
            <p class="mt-2 text-sm font-bold {{ $nifValid ? 'text-emerald-600' : 'text-red-500' }}">
                {{ $nifValid ? 'NIF válido ✓' : 'NIF inválido ✗' }}
            </p>
        @endif
    </div>

    @if($invoices->isNotEmpty())
        <div class="space-y-2">
            @foreach($invoices as $inv)
                <div class="flex justify-between items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                    <div>
                        <p class="font-bold dark:text-white">{{ $inv->issuer_name ?? 'Fornecedor' }}</p>
                        <p class="text-[10px] text-zinc-400">NIF {{ $inv->issuer_nif }} · {{ $inv->issued_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="font-black tabular-nums dark:text-white">{{ number_format($inv->amount, 2, ',', '.') }}€</span>
                </div>
            @endforeach
        </div>
    @endif
</div>

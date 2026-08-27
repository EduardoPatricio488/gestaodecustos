<div class="space-y-8 pb-24">
    <div class="flex items-center gap-5 px-1">
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
            <flux:icon name="arrow-up-tray" class="w-8 h-8 text-blue-600" />
        </div>
        <div>
            <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Importar Extrato</h1>
            <p class="text-xs text-zinc-400 mt-1">CSV/OFX de CGD, Millennium, Revolut, N26 — categorização automática</p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 max-w-2xl">
        <form wire:submit="generatePreview" class="space-y-6">
            <div class="border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-2xl p-8 text-center">
                <flux:icon name="document-arrow-up" class="size-12 text-zinc-300 mx-auto mb-4" />
                <input type="file" wire:model="statementFile" accept=".csv,.txt,.ofx" class="text-sm" />
                <p class="text-[10px] text-zinc-400 mt-3">Formatos: CSV ou OFX (máx. 5MB)</p>
            </div>
            <div wire:loading wire:target="statementFile,generatePreview,importStatement" class="text-sm text-blue-500 font-bold">
                A processar extrato...
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <flux:button type="submit" variant="filled" class="w-full rounded-2xl font-black" :disabled="!$statementFile">
                    Pré-visualizar
                </flux:button>
                <flux:button type="button" variant="primary" class="w-full rounded-2xl font-black" wire:click="importStatement" :disabled="!$previewReady">
                    Confirmar Importação
                </flux:button>
            </div>
        </form>

        @if($previewReady)
            <div class="mt-6 p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/30 space-y-3">
                <p class="font-black text-sm">Pré-visualização pronta</p>
                <p class="text-xs">
                    Banco: {{ strtoupper($preview['bank'] ?? '-') }} · Origem: {{ strtoupper($preview['source_file_type'] ?? '-') }}
                </p>
                <p class="text-xs">
                    {{ $preview['transactions_total'] ?? 0 }} transações lidas · {{ $preview['expenses_total'] ?? 0 }} despesas para importar
                </p>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-[11px] text-zinc-500">Selecionadas: {{ count($selectedSignatures) }} de {{ $preview['expenses_total'] ?? 0 }}</p>
                    <div class="flex gap-2">
                        <flux:button type="button" variant="ghost" class="rounded-xl" wire:click="selectAllPreviewRows">
                            Selecionar tudo
                        </flux:button>
                        <flux:button type="button" variant="ghost" class="rounded-xl" wire:click="clearSelectedPreviewRows">
                            Limpar seleção
                        </flux:button>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
                    <table class="min-w-full text-xs">
                        <thead class="bg-zinc-100 dark:bg-zinc-800/70">
                            <tr>
                                <th class="text-left p-2 font-black">Importar</th>
                                <th class="text-left p-2 font-black">Data</th>
                                <th class="text-left p-2 font-black">Descrição</th>
                                <th class="text-right p-2 font-black">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($preview['rows'] ?? []) as $tx)
                                <tr class="border-t border-zinc-200 dark:border-zinc-800">
                                    <td class="p-2 align-top">
                                        <input
                                            type="checkbox"
                                            class="rounded border-zinc-300"
                                            wire:model.live="selectedSignatures"
                                            value="{{ $tx['signature'] }}"
                                        />
                                    </td>
                                    <td class="p-2">{{ \Carbon\Carbon::parse($tx['date'])->format('d/m/Y') }}</td>
                                    <td class="p-2">{{ $tx['description'] }}</td>
                                    <td class="p-2 text-right font-bold text-red-600">{{ number_format($tx['amount'], 2, ',', '.') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-3 text-center text-zinc-400">Nenhuma despesa encontrada na amostra.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end">
                    <flux:button type="button" variant="ghost" class="rounded-xl" wire:click="clearPreview">
                        Limpar Pré-visualização
                    </flux:button>
                </div>
            </div>
        @endif

        @if($lastImport)
            <div class="mt-6 p-4 rounded-2xl {{ $lastImport->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'bg-red-50 dark:bg-red-950/30' }}">
                <p class="font-black text-sm">
                    {{ $lastImport->status === 'completed' ? '✓ Importação concluída' : '✗ Falha na importação' }}
                </p>
                <p class="text-xs mt-1">
                    {{ $lastImport->transactions_imported }} de {{ $lastImport->transactions_total }} transações
                    @if($lastImport->bank_detected) · Banco: {{ strtoupper($lastImport->bank_detected) }} @endif
                </p>
                @if(($lastImport->errors['duplicates_skipped'] ?? 0) > 0)
                    <p class="text-xs mt-1 text-amber-700 dark:text-amber-300">
                        {{ $lastImport->errors['duplicates_skipped'] }} transações duplicadas foram ignoradas.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 max-w-3xl">
        <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Regras Automáticas de Categorização</h3>

        <form wire:submit="saveCategorizationRule" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Texto-chave</label>
                <input wire:model="ruleKeyword" type="text" placeholder="Ex: netflix, uber, continente" class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Categoria</label>
                <select wire:model="ruleCategoryId" class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-3 py-2 text-sm">
                    <option value="">Selecionar</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Prioridade</label>
                <div class="flex gap-2">
                    <input wire:model="rulePriority" type="number" min="1" max="9999" class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-3 py-2 text-sm" />
                    <flux:button type="submit" variant="primary" class="rounded-xl font-black mt-1">Guardar</flux:button>
                </div>
            </div>
        </form>

        <div class="mt-5 space-y-2">
            @forelse($rules as $rule)
                <div class="flex items-center justify-between p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-950/30">
                    <div>
                        <p class="text-sm font-black dark:text-white">"{{ $rule->keyword }}" → {{ $rule->category?->name ?? 'Sem categoria' }}</p>
                        <p class="text-[10px] text-zinc-400 uppercase font-black mt-1">Prioridade {{ $rule->priority }} · {{ $rule->is_active ? 'Ativa' : 'Inativa' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button type="button" variant="ghost" class="rounded-xl" wire:click="toggleRule({{ $rule->id }})">
                            {{ $rule->is_active ? 'Desativar' : 'Ativar' }}
                        </flux:button>
                        <flux:button type="button" variant="danger" class="rounded-xl" wire:click="deleteRule({{ $rule->id }})" wire:confirm="Remover regra?">
                            Remover
                        </flux:button>
                    </div>
                </div>
            @empty
                <p class="text-xs text-zinc-400">Ainda não tens regras personalizadas. Cria uma para melhorar a categorização automática.</p>
            @endforelse
        </div>
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

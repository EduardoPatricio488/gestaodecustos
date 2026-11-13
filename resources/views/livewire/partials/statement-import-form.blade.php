    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/30 flex items-center justify-center">
                    <span class="text-red-600 font-black text-xs">S</span>
                </div>
                <div>
                    <h2 class="font-black dark:text-white">Como obter o PDF Santander</h2>
                    <p class="text-[11px] text-zinc-400">Só precisas de alguns passos.</p>
                </div>
            </div>
            <ol class="space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                <li class="flex gap-3"><span class="w-6 h-6 shrink-0 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-black">1</span><span>Abre a aplicação do <strong>Santander</strong>.</span></li>
                <li class="flex gap-3"><span class="w-6 h-6 shrink-0 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-black">2</span><span>Entra em <strong>Contas</strong>.</span></li>
                <li class="flex gap-3"><span class="w-6 h-6 shrink-0 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-black">3</span><span>Seleciona a conta e abre <strong>Movimentos</strong>.</span></li>
                <li class="flex gap-3"><span class="w-6 h-6 shrink-0 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-black">4</span><span>Carrega em <strong>PDF</strong> para gerar o extrato.</span></li>
                <li class="flex gap-3"><span class="w-6 h-6 shrink-0 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-black">5</span><span>Guarda o PDF no dispositivo e carrega-o aqui.</span></li>
            </ol>
            <div class="rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 p-3 text-xs text-blue-800 dark:text-blue-200">
                <strong>Privacidade:</strong> o Finance Pro lê as datas, descrições e valores dos movimentos. Não precisa do teu PIN, palavra-passe ou códigos de acesso ao banco.
            </div>
        </div>

        <div class="xl:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 sm:p-8 space-y-5">
            <div>
                <h2 class="text-lg font-black dark:text-white">Carregar extrato bancário</h2>
                <p class="text-xs text-zinc-400 mt-1">Começa por carregar o PDF exportado pela aplicação Santander.</p>
            </div>
            <form wire:submit="generatePreview" class="space-y-5">
                <label class="block border-2 border-dashed border-zinc-200 dark:border-zinc-700 hover:border-blue-400 rounded-2xl p-8 text-center cursor-pointer transition">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-red-50 dark:bg-red-950/30 text-red-600 flex items-center justify-center font-black text-sm tracking-wider">PDF</div>
                    <span class="block text-sm font-black dark:text-white">Selecionar extrato PDF</span>
                    <span class="block text-[11px] text-zinc-400 mt-1">PDF do Santander · Também são aceites CSV e OFX</span>
                    <input type="file" wire:model="statementFile" accept=".pdf,.csv,.txt,.ofx" class="mt-4 block w-full max-w-xs mx-auto text-xs" />
                    @if($statementFile)
                        <span class="block text-xs text-emerald-600 font-bold mt-3">{{ $statementFile->getClientOriginalName() }}</span>
                    @endif
                </label>
                @error('statementFile') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                <div wire:loading wire:target="statementFile,generatePreview,importStatement" class="text-sm text-blue-500 font-bold">
                    <span class="inline-flex items-center gap-2"><flux:icon name="arrow-path" class="size-4 animate-spin" /> A ler e analisar o extrato...</span>
                </div>
                <div class="flex flex-wrap gap-3">
                    <flux:button type="submit" variant="filled" class="rounded-xl font-black" :disabled="!$statementFile">
                        Analisar movimentos
                    </flux:button>
                    @if($previewReady)
                        <flux:button type="button" variant="ghost" class="rounded-xl" wire:click="clearPreview">Cancelar análise</flux:button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($previewReady)
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 sm:p-7 space-y-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-xs text-zinc-500">Confirma os movimentos e ajusta os dados antes de guardar.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="px-3 py-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 font-bold">{{ $preview['transactions_total'] ?? 0 }} movimentos</span>
                    <span class="px-3 py-2 rounded-xl bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 font-bold">{{ $preview['expenses_total'] ?? 0 }} despesas</span>
                    <span class="px-3 py-2 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 font-bold">{{ $preview['transfers_total'] ?? 0 }} transferências</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-zinc-500">Selecionados: <strong>{{ count($selectedSignatures) }}</strong> de {{ count($preview['rows'] ?? []) }}</p>
                <div class="flex flex-wrap gap-2">
                    <flux:button type="button" size="sm" variant="ghost" wire:click="selectAllPreviewRows">Selecionar todos</flux:button>
                    <flux:button type="button" size="sm" variant="ghost" wire:click="clearSelectedPreviewRows">Desmarcar todos</flux:button>
                </div>
            </div>

            <div class="space-y-3">
                @foreach(($preview['rows'] ?? []) as $index => $tx)
                    @php($isTransfer = ($tx['type'] ?? '') === 'transfer')
                    @php($isDebtPayment = ($tx['type'] ?? '') === 'debt_payment')
                    <div wire:key="statement-row-{{ $tx['signature'] ?? $index }}" class="rounded-2xl border {{ in_array($tx['signature'] ?? '', $selectedSignatures, true) ? 'border-blue-300 dark:border-blue-800' : 'border-zinc-200 dark:border-zinc-800' }} p-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded border-zinc-300" wire:model.live="selectedSignatures" value="{{ $tx['signature'] ?? '' }}" />
                            <div class="min-w-0 flex-1 grid grid-cols-1 md:grid-cols-12 gap-3">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Data</label>
                                    <input type="date" wire:model="preview.rows.{{ $index }}.date" class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-2 text-xs" />
                                </div>
                                <div class="md:col-span-5">
                                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Descrição</label>
                                    <input type="text" wire:model="preview.rows.{{ $index }}.description" class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-3 py-2 text-xs" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Valor (€)</label>
                                    <input type="number" step="0.01" wire:model="preview.rows.{{ $index }}.amount" class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-2 text-xs font-bold {{ $isTransfer ? 'text-blue-600' : 'text-red-600' }}" />
                                </div>
                                <div class="md:col-span-3">
                                    @if($isTransfer)
                                        <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Método de transferência</label>
                                        <select wire:model="preview.rows.{{ $index }}.transfer_method" class="w-full rounded-lg border border-blue-200 dark:border-blue-800 bg-white dark:bg-zinc-950 px-2 py-2 text-xs">
                                            <option value="">Não identificado</option>
                                            <option value="bank_transfer">Transferência bancária</option>
                                            <option value="immediate_transfer">Transferência imediata</option>
                                            <option value="multibanco">Multibanco</option>
                                            <option value="mbway">MB WAY</option>
                                            <option value="paypal">PayPal</option>
                                            <option value="revolut">Revolut</option>
                                            <option value="other">Outro</option>
                                        </select>
                                    @else
                                        <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Categoria</label>
                                        <select wire:model="preview.rows.{{ $index }}.category_id" class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-2 text-xs">
                                            <option value="">Sem categoria</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pl-7">
                            @if(in_array($tx['type'] ?? 'expense', ['expense', 'transfer'], true))
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" wire:click="$set('preview.rows.{{ $index }}.type', '{{ $isTransfer ? 'expense' : 'transfer' }}')" class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-wider px-3 py-2 rounded-lg border {{ $isTransfer ? 'border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50' : 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/50' }} transition" title="Clica para trocar entre despesa e transferência">
                                        <span aria-hidden="true" class="text-base leading-none">⇄</span>
                                        Tipo atual: {{ $isTransfer ? 'Transferência' : 'Despesa' }}
                                    </button>
                                </div>
                            @else
                                <span class="text-[10px] font-black uppercase tracking-wider text-red-600">Pagamento de dívida</span>
                            @endif
                            @if(isset($tx['balance']))
                                <span class="text-[10px] text-zinc-400">Saldo após movimento: {{ number_format((float) $tx['balance'], 2, ',', '.') }} €</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-zinc-100 dark:border-zinc-800 pt-5">
                <p class="text-xs text-zinc-500">Os movimentos só entram nas tuas contas depois de carregares em confirmar.</p>
                <flux:button type="button" variant="primary" class="rounded-xl font-black" wire:click="importStatement" :disabled="count($selectedSignatures) === 0">
                    Confirmar {{ count($selectedSignatures) }} movimentos
                </flux:button>
            </div>
        </div>
    @endif

    @if($lastImport)
        <div class="p-5 rounded-2xl {{ $lastImport->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900' : 'bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900' }}">
            <p class="font-black text-sm">{{ $lastImport->status === 'completed' ? 'Importação concluída' : 'Falha na importação' }}</p>
            <p class="text-xs mt-1">{{ $lastImport->transactions_imported }} de {{ $lastImport->transactions_total }} movimentos registados.</p>
            @if(($lastImport->errors['duplicates_skipped'] ?? 0) > 0)
                <p class="text-xs mt-1 text-amber-700 dark:text-amber-300">{{ $lastImport->errors['duplicates_skipped'] }} movimentos duplicados foram ignorados.</p>
            @endif
        </div>
    @endif


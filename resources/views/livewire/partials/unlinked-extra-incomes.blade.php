@if($unlinkedExtraIncomes->isNotEmpty())
    <section class="mt-8 space-y-4">
        <div class="flex items-center justify-between px-2">
            <div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Outras entradas extra</h2>
                <p class="mt-1 text-xs text-zinc-500">Ainda não associadas a um contrato de trabalho.</p>
            </div>
            <flux:badge variant="neutral" class="border-none bg-zinc-100 px-2 py-0.5 text-[10px] font-black uppercase text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                {{ $unlinkedExtraIncomes->count() }} Lançamentos
            </flux:badge>
        </div>

        <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @foreach($unlinkedExtraIncomes as $extra)
                <div class="flex flex-col gap-3 border-b border-zinc-100 p-4 last:border-b-0 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ $extra->description }}</p>
                        <p class="mt-1 text-xs text-zinc-500">
                            {{ $extra->received_at?->translatedFormat('d M Y') }} · {{ ucfirst($extra->source ?? 'outro') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-sm font-black tabular-nums text-emerald-600">
                            +{{ number_format((float) ($extra->amount_converted ?? $extra->amount), 2, ',', ' ') }} {{ $workspaceCurrency }}
                        </span>
                        @if($salaryContractOptions->isNotEmpty())
                            <select
                                aria-label="Associar {{ $extra->description }} a um contrato"
                                wire:change="associateExtraWithSalary({{ $extra->id }}, $event.target.value)"
                                class="h-10 max-w-56 rounded-xl border border-zinc-200 bg-zinc-50 px-3 text-xs font-semibold text-zinc-700 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200"
                            >
                                <option value="">Associar a um contrato...</option>
                                @foreach($salaryContractOptions as $salaryContract)
                                    <option value="{{ $salaryContract->id }}">{{ $salaryContract->description }}</option>
                                @endforeach
                            </select>
                        @endif
                        @if($isOwner)
                            <button type="button" wire:click="deleteExtra({{ $extra->id }})" wire:confirm="Apagar esta entrada extra?" class="rounded-lg p-2 text-zinc-400 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/30" aria-label="Apagar entrada extra">
                                <flux:icon name="trash" class="size-4" />
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

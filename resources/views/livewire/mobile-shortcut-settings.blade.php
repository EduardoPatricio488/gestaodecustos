<div>
    <button type="button" wire:click="toggleEditing" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-100 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300">
        <span aria-hidden="true">✎</span> {{ $editing ? 'Concluir' : 'Personalizar' }}
    </button>
    <button type="button" wire:click="openSettings" x-show="$wire.editing" x-cloak class="rounded-xl px-2 py-2 text-xs font-bold text-zinc-500 hover:text-emerald-600">
        Opções
    </button>

    @if($open)
        <div class="fixed inset-0 z-[10050] flex items-end justify-center bg-black/60 p-0 backdrop-blur-sm sm:items-center sm:p-4" wire:key="mobile-shortcut-settings-modal">
            <div class="max-h-[92dvh] w-full max-w-lg overflow-y-auto rounded-t-3xl bg-white p-5 shadow-2xl dark:bg-zinc-900 sm:rounded-3xl">
                <div class="mb-4 flex items-center justify-between">

                    <button type="button" wire:click="$set('open', false)" class="flex size-9 items-center justify-center rounded-xl bg-zinc-100 text-xl text-zinc-500 dark:bg-zinc-800" aria-label="Fechar">×</button>
                </div>

                <div class="rounded-2xl border border-dashed border-zinc-300 p-3 dark:border-zinc-700">
                    <label class="flex items-center gap-2 text-sm font-bold text-zinc-800 dark:text-zinc-100">
                        <input type="checkbox" wire:model.live="dividerEnabled" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                        Mostrar divisão entre os botões
                    </label>
                    <label class="mt-3 block text-[10px] font-bold uppercase tracking-wide text-zinc-400">Colocar divisão depois de</label>
                    <select wire:model.live="dividerAfter" @disabled(!$dividerEnabled) class="mt-1 w-full rounded-lg border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-semibold text-zinc-900 focus:border-emerald-500 focus:ring-emerald-500 disabled:opacity-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        @foreach(collect($shortcuts)->sortBy('order') as $key => $shortcut)
                            <option value="{{ $key }}">{{ $shortcut['label'] ?: $shortcutNames[$key] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    @foreach(collect($shortcuts)->sortBy('order') as $key => $shortcut)
                        <div wire:key="mobile-shortcut-setting-{{ $key }}" class="flex items-center gap-2 rounded-2xl border border-zinc-200 p-3 dark:border-zinc-700">
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <label class="flex shrink-0 items-center gap-2 text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                                    <input type="checkbox" wire:model.live="shortcuts.{{ $key }}.enabled" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="sr-only">Mostrar {{ $shortcutNames[$key] }}</span>
                                </label>
                                <div class="min-w-0 flex-1">
                                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-zinc-400">{{ $shortcutNames[$key] }}</label>
                                    <input type="text" maxlength="32" wire:model.live="shortcuts.{{ $key }}.label" class="w-full rounded-lg border-zinc-200 bg-zinc-50 px-2.5 py-1.5 text-sm font-semibold text-zinc-900 focus:border-emerald-500 focus:ring-emerald-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col gap-1">
                                <button type="button" wire:click="move('{{ $key }}', -1)" class="flex size-7 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300" aria-label="Mover para cima">↑</button>
                                <button type="button" wire:click="move('{{ $key }}', 1)" class="flex size-7 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300" aria-label="Mover para baixo">↓</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex gap-2">
                    <button type="button" wire:click="$set('open', false)" class="flex-1 rounded-xl border border-zinc-200 px-4 py-3 text-sm font-bold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">Cancelar</button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled" class="flex-1 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-black text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50">Guardar alterações</button>
                </div>
            </div>
        </div>
    @endif
</div>

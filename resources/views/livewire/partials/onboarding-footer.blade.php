<div data-onboarding-footer class="flex flex-col gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
    <div class="space-y-1">
        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Passo {{ $footerStep }} de {{ $footerTotal }}</span>
        @if($footerSkip)
            <p class="text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">Podes saltar este passo e preencher os dados mais tarde.</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center justify-end gap-2">
        @if($footerPrevious)
            <button type="button" wire:click="{{ $footerPrevious }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl px-3 text-xs font-bold text-zinc-600 transition hover:bg-zinc-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600 dark:text-zinc-300 dark:hover:bg-zinc-800">
                <flux:icon name="arrow-left" class="size-4" /> Voltar
            </button>
        @endif
        @if($footerSkip)
            <button type="button" wire:click="{{ $footerSkip }}" class="inline-flex h-11 items-center justify-center rounded-xl px-3 text-xs font-bold text-zinc-600 transition hover:bg-zinc-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600 dark:text-zinc-300 dark:hover:bg-zinc-800">
                Saltar
            </button>
        @endif
        @if($footerNextLabel)
            <button type="{{ $footerSubmit ? 'submit' : 'button' }}"
                @if(! $footerSubmit) wire:click="{{ $footerNext }}" @endif
                wire:loading.attr="disabled" wire:target="{{ $footerLoadingTarget }}"
                class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50">
                {{ $footerNextLabel }} <flux:icon name="arrow-right" class="size-4" />
            </button>
        @endif
    </div>
</div>

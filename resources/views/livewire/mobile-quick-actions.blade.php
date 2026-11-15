<div
    x-data="{ opening: false, label: 'formulário' }"
    x-on:mobile-modal-opening.window="opening = true; label = $event.detail?.label || 'formulário'"
    x-on:mobile-modal-ready.window="opening = false"
    x-on:mobile-modal-failed.window="opening = false"
>
    <div x-cloak x-show="opening" x-transition.opacity class="fixed inset-0 z-[100000] flex items-center justify-center bg-zinc-950/45 p-4" role="status" aria-live="polite">
        <div class="flex items-center gap-3 rounded-2xl bg-white px-5 py-4 text-sm font-bold text-zinc-800 shadow-xl dark:bg-zinc-900 dark:text-white">
            <span class="size-5 animate-spin rounded-full border-2 border-emerald-500/30 border-t-emerald-600" aria-hidden="true"></span>
            A preparar formulário…
        </div>
    </div>

    @if($loaded['expense'] ?? false)
        <livewire:expenses :dashboard-mode="true" :key="'mobile-expenses'" />
        <livewire:expense-form :dashboard-mode="true" :key="'mobile-expense-form'" />
    @endif

    @if($loaded['income'] ?? false)
        <livewire:income-hub :dashboard-mode="true" :key="'mobile-income'" />
    @endif

    @if($loaded['investment'] ?? false)
        <livewire:investments-hub :mobile-mode="true" :key="'mobile-investments'" />
    @endif

    @if($loaded['goal'] ?? false)
        <livewire:goals-hub :mobile-mode="true" :key="'mobile-goals'" />
    @endif

    @if($loaded['reminder'] ?? false)
        <livewire:hub.reminders-hub :dashboard-mode="true" :key="'mobile-reminders'" />
    @endif

    @if($loaded['subscription'] ?? false)
        <livewire:subscription-hub :dashboard-mode="true" :key="'mobile-subscriptions'" />
    @endif

    @if($loaded['statement'] ?? false)
        <div
            x-data="{ statementImportOpen: false }"
            x-on:open-mobile-statement-import.window="statementImportOpen = true"
            x-on:keydown.escape.window="statementImportOpen = false"
            x-cloak
            x-show="statementImportOpen"
            x-transition.opacity
            class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                x-on:click.outside="statementImportOpen = false"
                class="relative flex max-h-[88vh] w-full max-w-2xl flex-col overflow-hidden rounded-[2rem] bg-zinc-50 shadow-2xl dark:bg-zinc-950"
            >
                <div class="flex shrink-0 items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">Importação bancária</p>
                        <h2 class="mt-1 text-lg font-black text-zinc-900 dark:text-white">Importar Movimentos</h2>
                    </div>
                    <button type="button" x-on:click="statementImportOpen = false" class="flex size-9 items-center justify-center rounded-xl bg-zinc-100 text-xl text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300" aria-label="Fechar">×</button>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
                    <livewire:statement-import-hub :key="'mobile-statement-import'" />
                </div>
            </div>
        </div>
    @endif
</div>

<x-layouts.app>
    <div class="mx-auto w-full max-w-2xl space-y-5 pb-24">
        <div class="rounded-3xl bg-gradient-to-br from-zinc-950 via-zinc-900 to-emerald-950 p-5 text-white shadow-xl sm:p-7">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-emerald-300">Finance Pro AI</p>
                    <h1 class="mt-2 text-2xl font-black tracking-tight">A tua gestão, simplificada.</h1>
                    <p class="mt-1 text-sm text-zinc-300">Uma experiência mais prática para utilizar no telemóvel.</p>
                </div>
                <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-emerald-300">
                    <span class="text-2xl" aria-hidden="true">▣</span>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" wire:navigate class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-black text-zinc-900 shadow-sm transition active:scale-95">
                <span aria-hidden="true">←</span>
                Voltar ao dashboard completo
            </a>
        </div>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-sm font-black uppercase tracking-wider text-zinc-500">Acesso rápido</h2>
                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Versão temporária</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" x-on:click="$dispatch('open-expense-category-picker')" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-rose-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-rose-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300"><span class="text-xl font-bold" aria-hidden="true">＋</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Nova despesa</span>
                    <span class="mt-1 block text-xs text-zinc-500">Registar gasto</span>
                </button>
                <button type="button" x-on:click="$dispatch('open-mobile-income-menu', { rect: $el.getBoundingClientRect() })" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-emerald-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-emerald-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">↗</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Nova receita</span>
                    <span class="mt-1 block text-xs text-zinc-500">Adicionar rendimento</span>
                </button>
                <button type="button" x-on:click="$dispatch('open-mobile-statement-import')" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><span class="text-xl font-bold" aria-hidden="true">⇄</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Importar Movimentos</span>
                    <span class="mt-1 block text-xs text-zinc-500">Consultar atividade</span>
                </button>
                <button type="button" x-on:click="$dispatch('open-mobile-investment-actions')" class="group rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Novo investimento</span>
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar carteira</span>
                </button>
        <button type="button" x-on:click="$dispatch('open-mobile-reminder-create')" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-amber-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-amber-900/40 dark:bg-zinc-900">
            <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">♧</span></span>
            <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Novo lembrete</span>
            <span class="mt-1 block text-xs text-zinc-500">Agendar um lembrete</span>
        </button>

                <div class="col-span-2 py-1" aria-hidden="true"><div class="h-px w-full bg-zinc-200 dark:bg-zinc-700"></div></div>
                <a href="{{ route('hub.banco') }}" wire:navigate class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Banco</span>
                    <span class="mt-1 block text-xs text-zinc-500">Contas e saldos</span>
                </a>
                <button type="button" x-on:click="$dispatch('open-mobile-goal-create')" class="group rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">◎</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Novo objetivo</span>
                    <span class="mt-1 block text-xs text-zinc-500">Criar uma meta financeira</span>
                </button>
                <a href="{{ route('hub.fitness') }}" wire:navigate class="group rounded-2xl border border-orange-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-orange-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300"><span class="text-xl font-bold" aria-hidden="true">↗</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Zona de Treino</span>
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar atividade física</span>
                </a>
                <a href="{{ route('hub.calendar') }}" wire:navigate class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300"><span class="text-xl font-bold" aria-hidden="true">▦</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Calendário</span>
                    <span class="mt-1 block text-xs text-zinc-500">Ver agenda</span>
                </a>
            </div>
        </section>

        <livewire:mobile-recent-records />

        <div class="rounded-2xl border border-dashed border-zinc-300 p-4 text-center dark:border-zinc-700">
            <p class="text-xs leading-relaxed text-zinc-500">Esta é uma área experimental. Podes continuar a utilizar todas as funcionalidades na versão completa através do menu lateral.</p>
        </div>
    </div>

    <div
        x-data="{ investmentActionsOpen: false }"
        x-on:open-mobile-investment-actions.window="investmentActionsOpen = true"
        x-on:keydown.escape.window="investmentActionsOpen = false"
        x-cloak
        x-show="investmentActionsOpen"
        x-transition.opacity
        class="fixed inset-0 z-[10000] flex items-end justify-center bg-black/60 p-0 backdrop-blur-sm sm:items-center sm:p-4"
        style="display: none;"
    >
        <div x-on:click.outside="investmentActionsOpen = false" class="w-full max-w-md rounded-t-3xl bg-white p-5 shadow-2xl dark:bg-zinc-900 sm:rounded-3xl">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-violet-600">Carteira</p>
                    <h2 class="mt-1 text-xl font-black text-zinc-900 dark:text-white">Investimentos</h2>
                </div>
                <button type="button" x-on:click="investmentActionsOpen = false" class="flex size-9 items-center justify-center rounded-xl bg-zinc-100 text-xl text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300" aria-label="Fechar">×</button>
            </div>
            <div class="grid grid-cols-1 gap-3">
                <button type="button" x-on:click="investmentActionsOpen = false; $dispatch('open-mobile-investment-upload')" class="flex items-center gap-4 rounded-2xl border border-violet-200 p-4 text-left transition active:scale-[0.98] dark:border-violet-900/50">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-violet-50 text-xl text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">⇧</span>
                    <span><span class="block text-sm font-black text-zinc-900 dark:text-white">Upload de relatórios</span><span class="mt-1 block text-xs text-zinc-500">Importar posições e operações</span></span>
                </button>
                <button type="button" x-on:click="investmentActionsOpen = false; $dispatch('open-mobile-investment-create')" class="flex items-center gap-4 rounded-2xl border border-zinc-200 p-4 text-left transition active:scale-[0.98] dark:border-zinc-700">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-xl text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">＋</span>
                    <span><span class="block text-sm font-black text-zinc-900 dark:text-white">Novo investimento</span><span class="mt-1 block text-xs text-zinc-500">Registar um investimento</span></span>
                </button>
            </div>
        </div>
    </div>

    <div
        x-data="{ statementImportOpen: false }"
        x-on:open-mobile-statement-import.window="statementImportOpen = true"
        x-on:keydown.escape.window="statementImportOpen = false"
        x-cloak
        x-show="statementImportOpen"
        x-transition.opacity
        class="fixed inset-0 z-[10000] flex items-end justify-center bg-black/60 p-0 backdrop-blur-sm sm:items-center sm:p-4"
        style="display: none;"
    >
        <div
            x-on:click.outside="statementImportOpen = false"
            class="relative flex max-h-[95dvh] w-full max-w-3xl flex-col overflow-hidden rounded-t-3xl bg-zinc-50 shadow-2xl dark:bg-zinc-950 sm:rounded-3xl"
        >
            <div class="flex shrink-0 items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">Importação bancária</p>
                    <h2 class="mt-1 text-lg font-black text-zinc-900 dark:text-white">Importar Movimentos</h2>
                </div>
                <button type="button" x-on:click="statementImportOpen = false" class="flex size-9 items-center justify-center rounded-xl bg-zinc-100 text-xl text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300" aria-label="Fechar">×</button>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
                <livewire:statement-import-hub />
            </div>
        </div>
    </div>

    <livewire:expenses :dashboard-mode="true" />
    <livewire:income-hub :dashboard-mode="true" />
    <livewire:investments-hub :mobile-mode="true" />
    <livewire:goals-hub :mobile-mode="true" />
    <livewire:hub.reminders-hub :dashboard-mode="true" />

</x-layouts.app>

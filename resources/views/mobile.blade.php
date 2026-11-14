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
                <a href="{{ route('expenses.create') }}" class="group rounded-2xl border border-rose-100 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-rose-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300"><span class="text-xl font-bold" aria-hidden="true">＋</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Nova despesa</span>
                    <span class="mt-1 block text-xs text-zinc-500">Registar gasto</span>
                </a>
                <a href="{{ route('hub.incomes') }}" wire:navigate class="group rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-emerald-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">↗</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Receitas</span>
                    <span class="mt-1 block text-xs text-zinc-500">Consultar entradas</span>
                </a>
                <a href="{{ route('expenses.index') }}" wire:navigate class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><span class="text-xl font-bold" aria-hidden="true">▤</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Despesas</span>
                    <span class="mt-1 block text-xs text-zinc-500">Ver movimentos de saída</span>
                </a>
                <a href="{{ route('movements.index') }}" wire:navigate class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><span class="text-xl font-bold" aria-hidden="true">⇄</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Movimentos</span>
                    <span class="mt-1 block text-xs text-zinc-500">Consultar atividade</span>
                </a>
                <a href="{{ route('hub.banco') }}" wire:navigate class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Banco</span>
                    <span class="mt-1 block text-xs text-zinc-500">Contas e saldos</span>
                </a>
                <a href="{{ route('hub.investments') }}" wire:navigate class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Investimentos</span>
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar carteira</span>
                </a>
                <a href="{{ route('hub.goals') }}" wire:navigate class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">◎</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Objetivos</span>
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar metas</span>
                </a>
                <a href="{{ route('hub.calendar') }}" wire:navigate class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300"><span class="text-xl font-bold" aria-hidden="true">▦</span></span>
                    <span class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">Calendário</span>
                    <span class="mt-1 block text-xs text-zinc-500">Ver agenda</span>
                </a>
            </div>
        </section>

        <a href="{{ route('hub.reminders') }}" wire:navigate class="flex items-center justify-between rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <span class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">♧</span></span>
                <span><span class="block text-sm font-black text-zinc-900 dark:text-white">Lembretes</span><span class="block text-xs text-zinc-500">Não te esqueças do que é importante</span></span>
            </span>
            <span class="text-xl text-zinc-400" aria-hidden="true">›</span>
        </a>

        <div class="rounded-2xl border border-dashed border-zinc-300 p-4 text-center dark:border-zinc-700">
            <p class="text-xs leading-relaxed text-zinc-500">Esta é uma área experimental. Podes continuar a utilizar todas as funcionalidades na versão completa através do menu lateral.</p>
        </div>
    </div>
</x-layouts.app>

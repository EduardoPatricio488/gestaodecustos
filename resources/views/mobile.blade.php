<x-layouts.app>
    @php
        $mobileShortcutDefaults = [
            'expense' => ['label' => 'Nova despesa', 'enabled' => true, 'order' => 1],
            'income' => ['label' => 'Nova receita', 'enabled' => true, 'order' => 2],
            'movements' => ['label' => 'Importar Movimentos', 'enabled' => true, 'order' => 3],
            'investments' => ['label' => 'Novo investimento', 'enabled' => true, 'order' => 4],
            'bank' => ['label' => 'Banco', 'enabled' => true, 'order' => 5],
            'goal' => ['label' => 'Novo objetivo', 'enabled' => true, 'order' => 6],
            'reminder' => ['label' => 'Novo lembrete', 'enabled' => true, 'order' => 7],
            'subscription' => ['label' => 'Nova assinatura', 'enabled' => true, 'order' => 8],
            'fitness' => ['label' => 'Zona de Treino', 'enabled' => true, 'order' => 9],
            'calendar' => ['label' => 'Calendário', 'enabled' => true, 'order' => 10],
            'connect' => ['label' => 'Finance Connect', 'enabled' => true, 'order' => 11],
        ];
        $savedMobileShortcuts = auth()->user()->mobile_shortcuts ?? [];
        $mobileDividerSettings = $savedMobileShortcuts['_divider'] ?? ['enabled' => true, 'after' => 'investments'];
        unset($savedMobileShortcuts['_divider']);
        $mobileShortcutSettings = array_replace_recursive($mobileShortcutDefaults, $savedMobileShortcuts);
        $mobileDividerOrder = ($mobileShortcutSettings[$mobileDividerSettings['after'] ?? 'investments']['order'] ?? 4) + 0.5;
    @endphp
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
                <livewire:mobile-shortcut-settings />
                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Versão temporária</span>
            </div>
            <div
                class="grid grid-cols-2 gap-3"
                x-data="{ editing: false, draggedItem: null }"
                x-on:mobile-shortcuts-edit-mode.window="editing = $event.detail.enabled"
                x-on:mobile-shortcuts-reordered-saved.window="$el.querySelectorAll('[data-mobile-item]').forEach((item, index) => item.style.order = index + 1)"
                x-on:dragstart="if (!editing) return; const item = $event.target.closest('[data-mobile-item]'); if (!item) return; draggedItem = item; $event.dataTransfer.effectAllowed = 'move'; $event.dataTransfer.setData('text/plain', item.dataset.mobileItem); item.classList.add('opacity-40')"
                x-on:dragend="if (draggedItem) draggedItem.classList.remove('opacity-40'); draggedItem = null"
                x-on:dragover.prevent="if (editing) $event.dataTransfer.dropEffect = 'move'"
                x-on:drop.prevent="if (!editing || !draggedItem) return; const target = $event.target.closest('[data-mobile-item]'); if (target && target !== draggedItem) { const rect = target.getBoundingClientRect(); const after = $event.clientY > rect.top + rect.height / 2; target.parentNode.insertBefore(draggedItem, after ? target.nextSibling : target); } const ordered = [...$el.children].filter(child => child.dataset.mobileItem); const items = ordered.map((child, index) => { child.style.order = index + 1; return child.dataset.mobileItem; }); Livewire.dispatch('mobile-shortcuts-reordered', { items }); draggedItem.classList.remove('opacity-40'); draggedItem = null"
            >
                @if($mobileShortcutSettings['expense']['enabled'])
                <button type="button" x-on:click="$dispatch('open-expense-category-picker')" data-mobile-item="expense" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['expense']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-rose-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-rose-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300"><span class="text-xl font-bold" aria-hidden="true">＋</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['expense']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['expense']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'expense', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Registar gasto</span>
                </button>
                @endif
                @if($mobileShortcutSettings['income']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-income-menu', { rect: $el.getBoundingClientRect() })" data-mobile-item="income" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['income']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-emerald-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-emerald-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">↗</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['income']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['income']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'income', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Adicionar rendimento</span>
                </button>
                @endif
                @if($mobileShortcutSettings['movements']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-statement-import')" data-mobile-item="movements" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['movements']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><span class="text-xl font-bold" aria-hidden="true">⇄</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['movements']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['movements']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'movements', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Consultar atividade</span>
                </button>
                @endif
                @if($mobileShortcutSettings['investments']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-investment-actions')" data-mobile-item="investments" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['investments']['order'] }};" class="group rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['investments']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['investments']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'investments', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar carteira</span>
                </button>
                @endif

                @if($mobileShortcutSettings['bank']['enabled'])
                <a href="{{ route('hub.banco') }}" wire:navigate data-mobile-item="bank" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['bank']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['bank']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['bank']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'bank', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Contas e saldos</span>
                </a>
                @endif
                @if($mobileShortcutSettings['goal']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-goal-create')" data-mobile-item="goal" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['goal']['order'] }};" class="group rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">◎</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['goal']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['goal']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'goal', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Criar uma meta financeira</span>
                </button>
                @endif
                @if($mobileShortcutSettings['reminder']['enabled'])
        <button type="button" x-on:click="$dispatch('open-mobile-reminder-create')" data-mobile-item="reminder" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['reminder']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-amber-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-amber-900/40 dark:bg-zinc-900">
            <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">♧</span></span>
            <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['reminder']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['reminder']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'reminder', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
            <span class="mt-1 block text-xs text-zinc-500">Agendar um lembrete</span>
        </button>
                @endif

                @if($mobileShortcutSettings['subscription']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-subscription-create')" data-mobile-item="subscription" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['subscription']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-indigo-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-indigo-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300"><span class="text-xl font-bold" aria-hidden="true">▤</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['subscription']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['subscription']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'subscription', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Configurar custo recorrente</span>
                </button>
                @endif
                @if($mobileShortcutSettings['fitness']['enabled'])
                <a href="{{ route('hub.fitness') }}" wire:navigate data-mobile-item="fitness" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['fitness']['order'] }};" class="group rounded-2xl border border-orange-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-orange-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300"><span class="text-xl font-bold" aria-hidden="true">↗</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['fitness']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['fitness']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'fitness', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar atividade física</span>
                </a>
                @endif
                @if($mobileShortcutSettings['calendar']['enabled'])
                <a href="{{ route('hub.calendar') }}" wire:navigate data-mobile-item="calendar" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['calendar']['order'] }};" class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300"><span class="text-xl font-bold" aria-hidden="true">▦</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['calendar']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['calendar']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:change.stop="Livewire.dispatch('mobile-shortcut-label-updated', { key: 'calendar', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Ver agenda</span>
                </a>
                @endif
                @if($mobileDividerSettings['enabled'] ?? true)
                <div class="col-span-2 flex items-center gap-3 py-1" data-mobile-item="_divider" :draggable="editing" :class="editing ? 'cursor-grab rounded-lg bg-emerald-50/70 ring-2 ring-dashed ring-emerald-400/70 dark:bg-emerald-900/20' : ''" style="order: {{ $mobileDividerOrder }};" aria-hidden="true">
                    <span class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700"></span>
                    <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-zinc-400">Divisão</span>
                    <span class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700"></span>
                </div>
                @endif
                @if($mobileShortcutSettings['connect']['enabled'])
                <a href="/social" wire:navigate data-mobile-item="connect" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['connect']['order'] }};" class="group col-span-2 flex min-h-24 w-full items-center gap-4 rounded-2xl border border-indigo-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-indigo-900/40 dark:bg-zinc-900">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300"><span class="text-2xl font-bold" aria-hidden="true">◎</span></span>
                    <span class="min-w-0">
                        <span class="block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['connect']['label'] }}</span>
                        <span class="mt-1 block text-xs text-zinc-500">Comunidade, partilha e notificações</span>
                    </span>
                    <span class="ml-auto text-xl text-indigo-500" aria-hidden="true">→</span>
                </a>
                @endif
            </div>
        </section>

        <livewire:mobile-recent-records />

        <div class="rounded-2xl border border-dashed border-zinc-300 p-4 text-center dark:border-zinc-700">
            <p class="text-xs leading-relaxed text-zinc-500">Esta área foi pensada para utilizares no telemóvel e registares rapidamente as tuas despesas, receitas, investimentos e outros movimentos do dia a dia.</p>
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
    <livewire:subscription-hub :dashboard-mode="true" />

</x-layouts.app>

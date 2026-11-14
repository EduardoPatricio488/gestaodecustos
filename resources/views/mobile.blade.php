<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10b981">
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="apple-touch-icon" href="/icon-192x192.png?v=20261114-1">
    <script>
        (() => {
            const storageKey = 'finance-pro-mobile-theme';
            const applyTheme = () => {
                let theme = 'light';
                try {
                    theme = localStorage.getItem(storageKey) === 'dark' ? 'dark' : 'light';
                } catch (_) {}
                const isDark = theme === 'dark';
                document.documentElement.classList.toggle('dark', isDark);
                document.documentElement.style.colorScheme = theme;
                return isDark;
            };

            window.FinanceProMobileTheme = {
                apply: applyTheme,
                toggle() {
                    const nextTheme = applyTheme() ? 'light' : 'dark';
                    try { localStorage.setItem(storageKey, nextTheme); } catch (_) {}
                    applyTheme();
                    return nextTheme === 'dark';
                },
            };

            applyTheme();
            window.addEventListener('pageshow', applyTheme);
        })();
    </script>
    <title>{{ config('app.name', 'Finance Pro AI') }} — Mobile</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=20261114-5">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="/flux/flux.css">
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fp-greeting-in {
            from { opacity: 0; transform: translate3d(0, 12px, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        @keyframes fp-greeting-wave {
            0%, 100% { transform: rotate(0deg) translateY(0); }
            25% { transform: rotate(-12deg) translateY(-2px); }
            50% { transform: rotate(10deg) translateY(-2px); }
            75% { transform: rotate(-5deg) translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .fp-mobile-greeting, .fp-mobile-greeting span { animation: none !important; }
        }

        @keyframes fp-modal-backdrop-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fp-modal-panel-in {
            from { opacity: 0; transform: translate3d(0, 22px, 0) scale(.97); }
            to { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
        }

        /* Animação consistente para os modais abertos pelos atalhos móveis. */
        body div[x-show].fixed.inset-0:not([style*="display: none"]) {
            animation: fp-modal-backdrop-in .24s ease-out both;
        }

        body div[x-show].fixed.inset-0:not([style*="display: none"]) > div:not(.pointer-events-none) {
            animation: fp-modal-panel-in .32s cubic-bezier(.2,.8,.2,1) both;
            will-change: transform, opacity;
        }

        @media (prefers-reduced-motion: reduce) {
            body div[x-show].fixed.inset-0:not([style*="display: none"]),
            body div[x-show].fixed.inset-0:not([style*="display: none"]) > div:not(.pointer-events-none) {
                animation: none !important;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-white">
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
        $savedMobileShortcuts = auth()->user()?->mobile_shortcuts ?? [];
        $savedMobileShortcuts = is_array($savedMobileShortcuts) ? $savedMobileShortcuts : [];
        $mobileDividerSettings = $savedMobileShortcuts['_divider'] ?? ['enabled' => true, 'after' => 'investments'];
        unset($savedMobileShortcuts['_divider']);
        $mobileShortcutSettings = array_replace_recursive($mobileShortcutDefaults, $savedMobileShortcuts);
        $mobileDividerOrder = $mobileDividerSettings['order'] ?? (($mobileShortcutSettings[$mobileDividerSettings['after'] ?? 'investments']['order'] ?? 4) + 0.5);
    @endphp
    <div class="mx-auto w-full max-w-2xl space-y-5 px-4 pb-24 pt-4 sm:px-0" style="padding-top:max(1rem, env(safe-area-inset-top));">
        <div class="rounded-3xl bg-gradient-to-br from-zinc-950 via-zinc-900 to-emerald-950 p-5 text-white shadow-xl sm:p-7">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-emerald-300">Finance Pro AI</p>
                    <h1 class="mt-2 text-2xl font-black tracking-tight">A tua gestão, simplificada.</h1>
                    <p class="mt-1 text-sm text-zinc-300">Uma experiência mais prática para utilizar no telemóvel.</p>
                </div>
                <div class="flex shrink-0 flex-col items-center gap-2" x-data="{ dark: document.documentElement.classList.contains('dark'), toggleTheme() { this.dark = window.FinanceProMobileTheme.toggle(); } }">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-white/10 text-emerald-300">
                        <span class="text-2xl" aria-hidden="true">▣</span>
                    </div>
                    <button type="button" x-on:click="toggleTheme()" class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-2.5 py-1.5 text-[10px] font-bold text-white transition active:scale-95" aria-label="Alterar tema">
                        <span x-text="dark ? '☀️ Claro' : '🌙 Escuro'"></span>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('device.mode') }}" class="mt-5">
                @csrf
                <input type="hidden" name="mode" value="full">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-black text-zinc-900 shadow-sm transition active:scale-95">
                    <span aria-hidden="true">↗</span>
                    Entrar no site completo
                </button>
            </form>
        </div>

        @php
            $mobileHour = now()->hour;
            $mobileGreeting = $mobileHour < 12 ? 'Bom dia' : ($mobileHour < 19 ? 'Boa tarde' : 'Boa noite');
            $mobileGreetingIcon = $mobileHour < 12 ? '☀️' : ($mobileHour < 19 ? '🌤️' : '🌙');
            $mobileGreetingMessage = $mobileHour < 12
                ? 'Vamos começar o dia com as finanças em ordem.'
                : ($mobileHour < 19
                    ? 'Continua a acompanhar os teus objetivos.'
                    : 'Está tudo em ordem? Vê como está o teu dia.');
        @endphp
        <div class="fp-mobile-greeting px-1" style="animation: fp-greeting-in .65s cubic-bezier(.2,.8,.2,1) both;">
            <div class="flex items-center gap-2">
                <span class="text-2xl" aria-hidden="true" style="animation: fp-greeting-wave 2.4s ease-in-out infinite;">{{ $mobileGreetingIcon }}</span>
                <h2 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $mobileGreeting }}, {{ auth()->user()->name }}</h2>
            </div>
            <p class="mt-1 pl-9 text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $mobileGreetingMessage }}</p>
        </div>

        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-sm font-black uppercase tracking-wider text-zinc-500">Acesso rápido</h2>
                <livewire:mobile-shortcut-settings />
            </div>
            <div
                class="grid grid-cols-2 gap-3"
                x-data="{ editing: false, draggedItem: null }"
                x-on:mobile-shortcuts-edit-mode.window="editing = $event.detail.enabled"
                x-on:mobile-shortcuts-reordered-saved.window="$el.querySelectorAll('[data-mobile-item]').forEach((item, index) => item.style.order = index + 1)"
                x-on:click.capture="if (editing && !$event.target.closest('input, textarea, select')) { $event.preventDefault(); $event.stopPropagation(); }"
                x-on:pointerdown="if (!editing || $event.target.closest('input, textarea, select') || ($event.pointerType === 'mouse' && $event.button !== 0)) return; const item = $event.target.closest('[data-mobile-item]'); if (!item) return; $event.preventDefault(); draggedItem = item; item.classList.add('opacity-40')"
                x-on:pointermove="if (!editing || !draggedItem) return; const pointTarget = document.elementFromPoint($event.clientX, $event.clientY); const target = pointTarget?.closest?.('[data-mobile-item]'); if (!target || target === draggedItem || target.parentNode !== $el) return; const rect = target.getBoundingClientRect(); const after = $event.clientY > rect.top + rect.height / 2; target.parentNode.insertBefore(draggedItem, after ? target.nextSibling : target); [...$el.children].filter(child => child.dataset.mobileItem).forEach((child, index) => child.style.order = index + 1)"
                x-on:pointerup.window="if (!editing || !draggedItem) return; const ordered = [...$el.children].filter(child => child.dataset.mobileItem); const items = ordered.map((child, index) => { child.style.order = index + 1; return child.dataset.mobileItem; }); Livewire.dispatch('mobile-shortcuts-reordered', { items }); draggedItem.classList.remove('opacity-40'); draggedItem = null"
                x-on:pointercancel.window="if (draggedItem) draggedItem.classList.remove('opacity-40'); draggedItem = null"
            >
                @if($mobileShortcutSettings['expense']['enabled'])
                <button type="button" x-on:click="$dispatch('open-expense-category-picker')" data-mobile-item="expense" :draggable="editing" :class="editing ? 'cursor-grab touch-none select-none ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['expense']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-rose-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-rose-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300"><span class="text-xl font-bold" aria-hidden="true">＋</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['expense']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['expense']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'expense', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Registar gasto</span>
                </button>
                @endif
                @if($mobileShortcutSettings['income']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-income-menu', { rect: $el.getBoundingClientRect() })" data-mobile-item="income" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['income']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-emerald-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-emerald-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">↗</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['income']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['income']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'income', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Adicionar rendimento</span>
                </button>
                @endif
                @if($mobileShortcutSettings['movements']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-statement-import')" data-mobile-item="movements" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['movements']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><span class="text-xl font-bold" aria-hidden="true">⇄</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['movements']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['movements']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'movements', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Consultar atividade</span>
                </button>
                @endif
                @if($mobileShortcutSettings['investments']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-investment-actions')" data-mobile-item="investments" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['investments']['order'] }};" class="group rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['investments']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['investments']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'investments', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar carteira</span>
                </button>
                @endif

                @if($mobileShortcutSettings['bank']['enabled'])
                <a href="{{ route('hub.banco') }}" wire:navigate data-mobile-item="bank" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['bank']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/30 dark:text-emerald-300"><span class="text-xl font-bold" aria-hidden="true">▥</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['bank']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['bank']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'bank', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Contas e saldos</span>
                </a>
                @endif
                @if($mobileShortcutSettings['goal']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-goal-create')" data-mobile-item="goal" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['goal']['order'] }};" class="group rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">◎</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['goal']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['goal']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'goal', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Criar uma meta financeira</span>
                </button>
                @endif
                @if($mobileShortcutSettings['reminder']['enabled'])
        <button type="button" x-on:click="$dispatch('open-mobile-reminder-create')" data-mobile-item="reminder" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['reminder']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-amber-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-amber-900/40 dark:bg-zinc-900">
            <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><span class="text-xl font-bold" aria-hidden="true">♧</span></span>
            <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['reminder']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['reminder']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'reminder', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
            <span class="mt-1 block text-xs text-zinc-500">Agendar um lembrete</span>
        </button>
                @endif

                @if($mobileShortcutSettings['subscription']['enabled'])
                <button type="button" x-on:click="$dispatch('open-mobile-subscription-create')" data-mobile-item="subscription" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['subscription']['order'] }};" class="group flex min-h-32 w-full flex-col items-start rounded-2xl border border-indigo-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-indigo-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300"><span class="text-xl font-bold" aria-hidden="true">▤</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['subscription']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['subscription']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'subscription', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Configurar custo recorrente</span>
                </button>
                @endif
                @if($mobileShortcutSettings['fitness']['enabled'])
                <a href="{{ route('hub.fitness') }}" wire:navigate data-mobile-item="fitness" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['fitness']['order'] }};" class="group rounded-2xl border border-orange-100 bg-white p-4 text-left shadow-sm transition active:scale-[0.98] dark:border-orange-900/40 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300"><span class="text-xl font-bold" aria-hidden="true">↗</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['fitness']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['fitness']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'fitness', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Acompanhar atividade física</span>
                </a>
                @endif
                @if($mobileShortcutSettings['calendar']['enabled'])
                <a href="{{ route('hub.calendar') }}" wire:navigate data-mobile-item="calendar" :draggable="editing" :class="editing ? 'cursor-grab ring-2 ring-dashed ring-emerald-400/70' : ''" style="order: {{ $mobileShortcutSettings['calendar']['order'] }};" class="group rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm transition active:scale-[0.98] dark:border-zinc-700 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300"><span class="text-xl font-bold" aria-hidden="true">▦</span></span>
                    <span x-show="!editing" class="mt-3 block text-sm font-black text-zinc-900 dark:text-white">{{ $mobileShortcutSettings['calendar']['label'] }}</span>
                    <input type="text" value="{{ $mobileShortcutSettings['calendar']['label'] }}" maxlength="32" x-show="editing" x-cloak x-on:click.stop x-on:keydown.enter="$el.blur()" x-on:input.debounce.400ms.stop="$el.previousElementSibling.textContent = $el.value; Livewire.dispatch('mobile-shortcut-label-updated', { key: 'calendar', label: $event.target.value })" class="mt-3 w-full rounded-lg border border-emerald-300 bg-white px-2 py-1 text-sm font-black text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:bg-zinc-800 dark:text-white">
                    <span class="mt-1 block text-xs text-zinc-500">Ver agenda</span>
                </a>
                @endif
                @if($mobileDividerSettings['enabled'] ?? true)
                <div class="col-span-2 flex items-center gap-3 py-1" data-mobile-item="_divider" :draggable="editing" :class="editing ? 'cursor-grab touch-none select-none rounded-lg bg-emerald-50/70 ring-2 ring-dashed ring-emerald-400/70 dark:bg-emerald-900/20' : ''" style="order: {{ $mobileDividerOrder }};" aria-hidden="true">
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
    <livewire:investments-hub :mobile-mode="true" lazy />
    <livewire:goals-hub :mobile-mode="true" lazy />
    <livewire:hub.reminders-hub :dashboard-mode="true" lazy />
    <livewire:subscription-hub :dashboard-mode="true" lazy />

    <script>
        (() => {
            let startY = 0;
            let startX = 0;
            let tracking = false;
            let refreshing = false;
            const threshold = 95;
            const maxHorizontalDrift = 70;

            const isAtTop = () => window.scrollY <= 0
                && (document.scrollingElement?.scrollTop ?? 0) <= 0;

            document.addEventListener('touchstart', (event) => {
                if (event.touches.length !== 1 || !isAtTop()) {
                    tracking = false;
                    return;
                }

                startY = event.touches[0].clientY;
                startX = event.touches[0].clientX;
                tracking = true;
            }, { passive: true });

            document.addEventListener('touchend', (event) => {
                if (!tracking || refreshing || !event.changedTouches.length) return;

                const endY = event.changedTouches[0].clientY;
                const endX = event.changedTouches[0].clientX;
                const pullDistance = endY - startY;
                const horizontalDrift = Math.abs(endX - startX);
                tracking = false;

                if (pullDistance < threshold || horizontalDrift > maxHorizontalDrift || !isAtTop()) return;

                refreshing = true;
                window.location.reload();
            }, { passive: true });

            document.addEventListener('touchcancel', () => {
                tracking = false;
            }, { passive: true });
        })();
    </script>

    @livewireScripts
    @fluxScripts
</body>
</html>

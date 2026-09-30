<div x-data="{
    open: true,
    preferences: false,
    analytics: false,
    init() {
        const readCookie = () => {
            const match = document.cookie.match(/(?:^|; )fp_cookie_consent=([^;]*)/);
            return match ? decodeURIComponent(match[1]) : null;
        };

        let stored = null;

        // localStorage pode estar indisponível em alguns modos de privacidade.
        try {
            stored = window.localStorage.getItem('fp_cookie_consent');
        } catch (error) {
            stored = null;
        }

        // O cookie é a fonte de verdade compatível com o backend.
        if (stored !== 'necessary' && stored !== 'analytics') {
            stored = readCookie();
        }

        if (stored === 'necessary' || stored === 'analytics') {
            this.open = false;
            this.analytics = stored === 'analytics';
        }
    },
    save(value) {
        const consent = value === 'analytics' ? 'analytics' : 'necessary';

        // Guardar localmente melhora a persistência durante navegação Livewire.
        try {
            window.localStorage.setItem('fp_cookie_consent', consent);
        } catch (error) {
            // O cookie abaixo continua a permitir guardar a preferência.
        }

        // O cookie é persistente e permite ao backend decidir se deve carregar analytics.
        document.cookie = 'fp_cookie_consent=' + encodeURIComponent(consent)
            + '; Max-Age=31536000; Path=/; SameSite=Lax'
            + (location.protocol === 'https:' ? '; Secure' : '');

        this.analytics = consent === 'analytics';
        this.open = false;
        this.preferences = false;

        window.dispatchEvent(new CustomEvent('finance-pro-cookie-consent', {
            detail: { analytics: consent === 'analytics' }
        }));
    },
    reopen() {
        this.open = true;
    }
}" x-init="init()" x-on:open-cookie-preferences.window="reopen()" x-on:keydown.escape.window="preferences = false" class="relative z-[99999]">

    {{-- Botão para reabrir --}}
    <button x-show="!open" x-cloak type="button" @click="reopen()" aria-label="Preferências de cookies"
        class="fixed bottom-4 left-4 z-[99998] flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-600 shadow-md transition hover:scale-105 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:text-white">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 3a9 9 0 1 0 9 9 4 4 0 0 1-4-4 4 4 0 0 1-4-4 1 1 0 0 0-1-1Z"/>
            <circle cx="8.5" cy="11.5" r=".8" fill="currentColor"/>
            <circle cx="12" cy="16" r=".8" fill="currentColor"/>
            <circle cx="15.5" cy="13" r=".8" fill="currentColor"/>
        </svg>
    </button>

    {{-- Banner --}}
    <section x-show="open && !preferences" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        role="dialog" aria-labelledby="cookie-consent-title"
        class="fixed bottom-4 left-4 right-4 z-[99999] rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xl ring-1 ring-black/5 dark:border-zinc-800 dark:bg-zinc-900 dark:ring-white/5 sm:right-auto sm:max-w-md">

        {{-- Fechar (= só necessários) --}}
        <button type="button" @click="save('necessary')" aria-label="Fechar e aceitar apenas cookies necessários"
            class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>

        <div class="flex items-start gap-3 pr-8">
            <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3a9 9 0 1 0 9 9 4 4 0 0 1-4-4 4 4 0 0 1-4-4 1 1 0 0 0-1-1Z"/>
                    <circle cx="8.5" cy="11.5" r=".8" fill="currentColor"/>
                    <circle cx="12" cy="16" r=".8" fill="currentColor"/>
                    <circle cx="15.5" cy="13" r=".8" fill="currentColor"/>
                </svg>
            </span>
            <div>
                <h2 id="cookie-consent-title" class="text-base font-bold text-zinc-900 dark:text-white">Privacidade e cookies</h2>
                <p class="mt-1.5 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                    Usamos apenas os cookies necessários ao funcionamento do site. Os de análise (Google Analytics) só são ativados se aceitares.
                </p>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-2">
            <button type="button" @click="save('necessary')"
                class="rounded-xl border border-zinc-300 px-3 py-2.5 text-sm font-semibold text-zinc-800 transition hover:bg-zinc-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:border-zinc-600 dark:text-zinc-100 dark:hover:bg-zinc-800">
                Só necessários
            </button>
            <button type="button" @click="save('analytics')"
                class="rounded-xl bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                Aceitar tudo
            </button>
        </div>

        <div class="mt-3 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <button type="button" @click="preferences = true" class="font-medium underline underline-offset-2 hover:text-zinc-800 dark:hover:text-white">Personalizar</button>
            <span>
                <a class="underline underline-offset-2 hover:text-zinc-800 dark:hover:text-white" href="{{ route('legal.cookies') }}">Cookies</a>
                <span aria-hidden="true" class="mx-1">·</span>
                <a class="underline underline-offset-2 hover:text-zinc-800 dark:hover:text-white" href="{{ route('legal.privacy') }}">Privacidade</a>
            </span>
        </div>
    </section>

    {{-- Modal de preferências --}}
    <div x-show="open && preferences" x-cloak x-transition.opacity
        class="fixed inset-0 z-[100000] flex items-center justify-center bg-zinc-950/50 p-4">

        <div role="dialog" aria-modal="true" aria-labelledby="cookie-prefs-title" @click.outside="preferences = false"
            class="relative w-full max-w-lg rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">

            <button type="button" @click="preferences = false" aria-label="Fechar preferências"
                class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>

            <h2 id="cookie-prefs-title" class="pr-10 text-lg font-bold text-zinc-900 dark:text-white">Preferências de cookies</h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Escolhe que cookies queres permitir. Podes mudar isto a qualquer momento.</p>

            <div class="mt-5 space-y-3">
                {{-- Necessários --}}
                <div class="flex items-start justify-between gap-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <div>
                        <strong class="block text-sm text-zinc-900 dark:text-white">Necessários</strong>
                        <span class="mt-0.5 block text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">Segurança, sessão e funcionamento do site.</span>
                    </div>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Sempre ativos</span>
                </div>

                {{-- Análise --}}
                <label class="flex cursor-pointer items-start justify-between gap-4 rounded-xl border border-zinc-200 p-4 transition hover:border-zinc-300 dark:border-zinc-700 dark:hover:border-zinc-600">
                    <div>
                        <strong class="block text-sm text-zinc-900 dark:text-white">Análise</strong>
                        <span class="mt-0.5 block text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">Métricas de utilização através do Google Analytics.</span>
                    </div>
                    <input type="checkbox" x-model="analytics" class="peer sr-only" aria-label="Permitir cookies de análise">
                    <span class="relative mt-0.5 h-6 w-11 shrink-0 rounded-full bg-zinc-300 transition-colors peer-checked:bg-emerald-600 peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500 peer-focus-visible:ring-offset-2 dark:bg-zinc-600 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:after:translate-x-5"></span>
                </label>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="preferences = false"
                    class="rounded-xl border border-zinc-300 px-4 py-2.5 text-sm font-semibold text-zinc-800 transition hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-100 dark:hover:bg-zinc-800">
                    Voltar
                </button>
                <button type="button" @click="save(analytics ? 'analytics' : 'necessary')"
                    class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Guardar preferências
                </button>
            </div>
        </div>
    </div>
</div>

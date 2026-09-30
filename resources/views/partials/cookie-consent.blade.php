<div x-data="{
    open: true,
    preferences: false,
    analytics: false,
    init() {
        const stored = window.localStorage.getItem('fp_cookie_consent');

        if (stored === 'necessary' || stored === 'analytics') {
            this.open = false;
            this.analytics = stored === 'analytics';
        }
    },
    save(value) {
        const consent = value === 'analytics' ? 'analytics' : 'necessary';

        // Guardar localmente garante que a escolha permanece mesmo
        // durante navegação Livewire, sem depender de uma nova resposta HTTP.
        window.localStorage.setItem('fp_cookie_consent', consent);

        // O cookie permite ao backend decidir se deve carregar analytics.
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
}" x-init="init()" x-on:open-cookie-preferences.window="reopen()" class="relative z-[9999]">
    <button x-show="!open" x-cloak type="button" @click="reopen()" class="fixed bottom-4 right-4 z-[9998] rounded-full border border-zinc-200 bg-white px-4 py-2 text-xs font-bold text-zinc-700 shadow-lg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">Preferências de cookies</button>

    <div x-show="open" x-cloak class="fixed inset-0 bg-zinc-950/50 backdrop-blur-sm"></div>

    <section x-show="open" x-cloak role="dialog" aria-modal="true" aria-labelledby="cookie-consent-title" data-no-auto-close class="fixed bottom-0 left-0 right-0 z-[10000] border-t border-zinc-200 bg-white p-5 shadow-2xl dark:border-zinc-800 dark:bg-zinc-950 sm:p-6">
        <div class="mx-auto max-w-6xl">
            <div x-show="!preferences">
                <h2 id="cookie-consent-title" class="text-lg font-black">Privacidade e cookies</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">Utilizamos apenas dados e cookies necessários para o funcionamento. Cookies de análise, como o Google Analytics, só são ativados com a tua escolha.</p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="button" @click="save('necessary')" class="rounded-xl border border-zinc-300 px-4 py-2.5 text-sm font-bold">Apenas necessários</button>
                    <button type="button" @click="save('analytics')" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white">Aceitar análise</button>
                    <button type="button" @click="preferences=true" class="rounded-xl border border-zinc-300 px-4 py-2.5 text-sm font-bold">Personalizar</button>
                </div>

                <p class="mt-3 text-xs text-zinc-500"><a class="underline" href="{{ route('legal.cookies') }}">Política de Cookies</a> · <a class="underline" href="{{ route('legal.privacy') }}">Privacidade</a></p>
            </div>

            <div x-show="preferences" x-cloak>
                <h2 class="text-lg font-black">Preferências de cookies</h2>

                <label class="mt-4 flex gap-3 rounded-xl border border-zinc-200 p-4">
                    <input type="checkbox" checked disabled aria-label="Cookies necessários sempre ativos">
                    <span><strong class="block text-sm">Necessários</strong><span class="text-xs text-zinc-500">Sempre ativos para segurança, sessão e funcionamento.</span></span>
                </label>

                <label class="mt-3 flex gap-3 rounded-xl border border-zinc-200 p-4">
                    <input type="checkbox" x-model="analytics" aria-label="Permitir cookies de análise">
                    <span><strong class="block text-sm">Análise</strong><span class="text-xs text-zinc-500">Métricas de utilização através do Google Analytics.</span></span>
                </label>

                <div class="mt-4 flex gap-3">
                    <button type="button" @click="save(analytics ? 'analytics' : 'necessary')" class="rounded-xl bg-emerald-600 px-4 py-2.5 font-bold text-white">Guardar preferências</button>
                    <button type="button" @click="preferences=false" class="rounded-xl border border-zinc-300 px-4 py-2.5 font-bold">Voltar</button>
                </div>
            </div>
        </div>
    </section>
</div>

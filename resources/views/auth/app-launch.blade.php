<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#070b13">
    <meta name="color-scheme" content="light dark">
    <script>
        (() => {
            let theme = 'dark';
            try {
                const saved = localStorage.getItem('finance-pro-theme') || localStorage.getItem('finance-pro-mobile-theme');
                if (saved === 'light' || saved === 'dark') theme = saved;
                localStorage.setItem('finance-pro-theme', theme);
                localStorage.setItem('finance-pro-mobile-theme', theme);
            } catch (_) {}
            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.dataset.theme = theme;
            document.documentElement.style.colorScheme = theme;
        })();
    </script>
    <link rel="manifest" href="/manifest.json?v=20261114-2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Finance Pro AI">
    <link rel="apple-touch-icon" href="/icon-192x192.png?v=20261114-2">
    <title>Finance Pro AI</title>
    @vite('resources/css/app.css')
    <style>
        @keyframes fp-orbit { from { transform:rotate(0deg) translateX(30px) rotate(0deg); } to { transform:rotate(360deg) translateX(30px) rotate(-360deg); } }
        @keyframes fp-rise { from { transform:translateY(18px) scale(.9); opacity:0; } to { transform:translateY(0) scale(1); opacity:1; } }
        @keyframes fp-pulse { 0%,100% { opacity:.4; transform:scale(.94); } 50% { opacity:1; transform:scale(1.06); } }
        .fp-orbit { animation:fp-orbit 2.2s linear infinite; }
        .fp-rise { animation:fp-rise .7s cubic-bezier(.2,.8,.2,1) both; }
        .fp-pulse { animation:fp-pulse 1.4s ease-in-out infinite; }
        @keyframes fp-loading-progress { 0% { transform: translateX(-110%); } 100% { transform: translateX(310%); } }
        #fp-navigation-progress { opacity: 0; transition: opacity .2s ease; }
        #fp-navigation-progress.is-loading { opacity: 1; }
        #fp-navigation-progress-bar { animation: fp-loading-progress 1.1s ease-in-out infinite; }
        .fp-launch-button:disabled { opacity: .72; cursor: wait; }
        html[data-theme="light"] body { background:#f4f7fb; color:#18181b; }
        html[data-theme="light"] .fp-launch-card { background:#fff; border-color:#e4e4e7; color:#18181b; }
        html[data-theme="light"] .fp-muted { color:#52525b; }
        html[data-theme="light"] .fp-status { background:#fff; border-color:#d4d4d8; }
        html[data-theme="light"] .fp-status p:last-child { color:#52525b; }
        html[data-theme="light"] .fp-option-secondary { background:#fff; border-color:#d4d4d8; color:#18181b; }
        html[data-theme="light"] .fp-option-secondary span:last-child { color:#71717a; }
        html[data-theme="light"] .fp-option-secondary .fp-option-description { color:#52525b; }
        html[data-theme="light"] .fp-theme-button { background:#fff; border-color:#d4d4d8; color:#27272a; }
        html[data-theme="light"] .fp-heading-description { color:#52525b; }
        @media(prefers-reduced-motion:reduce){.fp-orbit,.fp-rise,.fp-pulse,#fp-navigation-progress-bar{animation:none!important}}
    </style>
</head>
<body class="min-h-screen bg-[#070b13] text-white antialiased">
    <div id="fp-navigation-progress" class="pointer-events-none fixed inset-x-0 top-0 z-[100] h-1 overflow-hidden bg-white/10" role="status" aria-live="polite">
        <div id="fp-navigation-progress-bar" class="h-full w-1/3 rounded-full bg-emerald-300 shadow-[0_0_12px_rgba(110,231,183,.8)]"></div>
    </div>
    <script>
        (() => {
            const button = document.getElementById('fp-theme-toggle');
            const label = document.getElementById('fp-theme-label');
            const icon = document.getElementById('fp-theme-icon');
            const meta = document.querySelector('meta[name="theme-color"]');
            const applyTheme = (theme) => {
                const normalized = theme === 'light' ? 'light' : 'dark';
                document.documentElement.dataset.theme = normalized;
                document.documentElement.classList.toggle('dark', normalized === 'dark');
                document.documentElement.style.colorScheme = normalized;
                if (meta) meta.setAttribute('content', normalized === 'dark' ? '#070b13' : '#f4f7fb');
                if (label) label.textContent = normalized === 'dark' ? 'Tema claro' : 'Tema escuro';
                if (icon) icon.textContent = normalized === 'dark' ? '☀' : '☾';
                try {
                    localStorage.setItem('finance-pro-theme', normalized);
                    localStorage.setItem('finance-pro-mobile-theme', normalized);
                } catch (_) {}
            };
            let current = document.documentElement.dataset.theme || 'dark';
            applyTheme(current);
            button?.addEventListener('click', () => {
                current = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                applyTheme(current);
            });
        })();
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const progress = document.getElementById('fp-navigation-progress');
            document.querySelectorAll('form[action="{{ route('app.launch.choose') }}"]').forEach((form) => {
                form.addEventListener('submit', () => {
                    progress.classList.add('is-loading');
                    document.querySelectorAll('.fp-launch-button').forEach((button) => {
                        button.disabled = true;
                        button.setAttribute('aria-busy', 'true');
                        button.querySelector('span:last-child').textContent = '…';
                    });
                });
            });
        });
    </script>
    <main class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="fp-rise w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-5 flex size-16 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-300">
                    <svg viewBox="0 0 24 24" class="size-9" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 18V9h4v9M10 18V5h4v13M16 18V2h4v16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-xs font-black uppercase tracking-[.3em] text-emerald-300">Finance Pro AI</p>
                <h1 class="mt-3 text-2xl font-black tracking-tight">Como queres entrar?</h1>
                <p class="fp-heading-description mt-2 text-sm text-zinc-400">Escolhe a experiência que pretendes utilizar.</p>
                <button id="fp-theme-toggle" type="button" class="fp-theme-button mt-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/[.06] px-4 py-2 text-xs font-bold text-zinc-200 transition hover:bg-white/10" aria-label="Alterar tema">
                    <span id="fp-theme-icon" aria-hidden="true">☀</span>
                    <span id="fp-theme-label">Tema claro</span>
                </button>
            </div>
            @auth
                <div class="fp-status mb-5 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-center" role="status">
                    <p class="text-sm font-bold text-emerald-200">Sessão já iniciada</p>
                    <p class="mt-1 text-xs text-zinc-300">Olá, {{ auth()->user()->name }}. Podes entrar diretamente no mobile ou no site completo, sem voltar a iniciar sessão.</p>
                </div>
            @else
                <div class="fp-status mb-5 rounded-2xl border border-white/10 bg-white/[.04] px-4 py-3 text-center" role="status">
                    <p class="text-sm font-bold text-zinc-200">Ainda não tens sessão iniciada</p>
                    <p class="mt-1 text-xs text-zinc-400">Escolhe uma opção e inicia sessão quando te for pedido.</p>
                </div>
            @endauth
            <div class="space-y-3">
                <form method="POST" action="{{ route('app.launch.choose') }}">
                    @csrf
                    <input type="hidden" name="mode" value="mobile">
                    <button type="submit" class="fp-launch-button flex w-full items-center gap-4 rounded-2xl border border-emerald-400/50 bg-emerald-500/10 p-5 text-left transition hover:bg-emerald-500/15 active:scale-[.98]">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-zinc-950"><svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="6" y="2" width="12" height="20" rx="2"/><path d="M10 18h4"/></svg></span>
                        <span class="min-w-0 flex-1"><span class="block text-base font-black">Apenas registos rápidos</span><span class="mt-1 block text-xs leading-relaxed text-zinc-400">Entrar na conta e abrir a versão mobile.</span></span>
                        <span class="text-2xl text-emerald-300">›</span>
                    </button>
                </form>
                <form method="POST" action="{{ route('app.launch.choose') }}">
                    @csrf
                    <input type="hidden" name="mode" value="full">
                    <button type="submit" class="fp-option-secondary flex w-full items-center gap-4 rounded-2xl border border-white/10 bg-white/[.04] p-5 text-left transition hover:bg-white/[.08] active:scale-[.98]">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-zinc-700 text-white"><svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></span>
                        <span class="min-w-0 flex-1"><span class="block text-base font-black">Entrar no site completo</span><span class="mt-1 block text-xs leading-relaxed text-zinc-400">Abrir a página inicial e todas as funcionalidades.</span></span>
                        <span class="text-2xl text-zinc-400">›</span>
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>

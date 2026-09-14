<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Configurações PWA -->
    <meta name="theme-color" content="#10b981">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icon-192x192.png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Registo do Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>

    <!-- Tema do Bunker Offline -->
    <style>
        .fp-bunker-theme-toggle {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:7px;
            min-height:38px;
            padding:8px 11px;
            border:1px solid #d4d4d8;
            border-radius:12px;
            background:#fff;
            color:#18181b;
            cursor:pointer;
            font-size:10px;
            font-weight:900;
            transition:all .18s ease;
        }
        .fp-bunker-theme-toggle:hover { border-color:#10b981; transform:translateY(-1px); }

        /* Bunker: claro é o tema predefinido */
        .fp-bunker:not(.fp-bunker-theme-dark) {
            background:#fff !important;
            color:#18181b !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .top {
            background:rgba(255,255,255,.94) !important;
            border-bottom-color:#e4e4e7 !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .eyebrow { color:#71717a !important; }
        .fp-bunker:not(.fp-bunker-theme-dark) .hero p,
        .fp-bunker:not(.fp-bunker-theme-dark) .muted { color:#52525b !important; }
        .fp-bunker:not(.fp-bunker-theme-dark) .card {
            background:#fff !important;
            border-color:#e4e4e7 !important;
            box-shadow:0 12px 35px rgba(24,24,27,.07) !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) label { color:#3f3f46 !important; }
        .fp-bunker:not(.fp-bunker-theme-dark) input,
        .fp-bunker:not(.fp-bunker-theme-dark) select {
            background:#fff !important;
            color:#18181b !important;
            border-color:#d4d4d8 !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .btn,
        .fp-bunker:not(.fp-bunker-theme-dark) .chip {
            background:#f4f4f5 !important;
            color:#18181b !important;
            border-color:#d4d4d8 !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .btn.primary {
            background:#10b981 !important;
            border-color:#10b981 !important;
            color:#03251a !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .stat,
        .fp-bunker:not(.fp-bunker-theme-dark) .row {
            background:#fafafa !important;
            border-color:#e4e4e7 !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .stat span,
        .fp-bunker:not(.fp-bunker-theme-dark) .row-meta { color:#71717a !important; }
        .fp-bunker:not(.fp-bunker-theme-dark) .badge {
            background:#e4e4e7 !important;
            color:#3f3f46 !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .storage { background:#e4e4e7 !important; }
        .fp-bunker:not(.fp-bunker-theme-dark) .notice {
            background:#fff !important;
            color:#18181b !important;
            border-color:#d4d4d8 !important;
            box-shadow:0 18px 50px rgba(24,24,27,.14) !important;
        }
        .fp-bunker:not(.fp-bunker-theme-dark) .danger-zone { border-color:#fecaca !important; }

        /* Tema escuro */
        .fp-bunker.fp-bunker-theme-dark .fp-bunker-theme-toggle {
            background:#18181b;
            color:#fafafa;
            border-color:#3f3f46;
        }
        .fp-bunker.fp-bunker-theme-dark .fp-bunker-theme-toggle:hover { border-color:#10b981; }
    </style>
</head>
    <body class="font-sans antialiased" data-authenticated="{{ auth()->check() ? '1' : '0' }}">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            (() => {
                const STORAGE_KEY = 'finance-pro-bunker-theme';

                const applyBunkerTheme = (bunker, theme) => {
                    const dark = theme === 'dark';
                    bunker.classList.toggle('fp-bunker-theme-dark', dark);
                    const button = bunker.querySelector('[data-bunker-theme-toggle]');
                    if (button) {
                        button.innerHTML = dark ? '☀️ Claro' : '🌙 Escuro';
                        button.setAttribute('aria-label', dark ? 'Mudar para tema claro' : 'Mudar para tema escuro');
                        button.title = dark ? 'Mudar para tema claro' : 'Mudar para tema escuro';
                    }
                };

                const setupBunkerTheme = (bunker) => {
                    if (!bunker || bunker.dataset.themeReady === '1') return;
                    const topbar = bunker.querySelector('.topbar');
                    if (!topbar) return;

                    bunker.dataset.themeReady = '1';
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'fp-bunker-theme-toggle';
                    button.dataset.bunkerThemeToggle = '1';
                    button.addEventListener('click', () => {
                        const next = bunker.classList.contains('fp-bunker-theme-dark') ? 'light' : 'dark';
                        localStorage.setItem(STORAGE_KEY, next);
                        applyBunkerTheme(bunker, next);
                    });
                    topbar.appendChild(button);
                    applyBunkerTheme(bunker, localStorage.getItem(STORAGE_KEY) || 'light');
                };

                const scan = () => document.querySelectorAll('.fp-bunker').forEach(setupBunkerTheme);
                scan();

                new MutationObserver(scan).observe(document.body, { childList: true, subtree: true });
            })();
        </script>
    </body>
</html>

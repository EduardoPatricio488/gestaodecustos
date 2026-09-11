<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<!-- SÍMBOLO NOVO (SACO DE DINHEIRO) -->
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💰</text></svg>">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    /* Dashboard Business: item principal da área empresarial */
    [data-flux-sidebar] a[href="/empresa/dashboard"] {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.14), rgba(5, 150, 105, 0.07)) !important;
        color: #047857 !important;
        font-weight: 900 !important;
        border: 1px solid rgba(16, 185, 129, 0.30) !important;
        border-radius: 1rem !important;
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.10) !important;
        padding-top: 0.7rem !important;
        padding-bottom: 0.7rem !important;
        transform: translateX(2px);
    }

    [data-flux-sidebar] a[href="/empresa/dashboard"]:hover {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.20), rgba(5, 150, 105, 0.11)) !important;
        border-color: rgba(16, 185, 129, 0.45) !important;
        transform: translateX(3px);
    }

    .dark [data-flux-sidebar] a[href="/empresa/dashboard"] {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.18), rgba(5, 150, 105, 0.09)) !important;
        color: #6ee7b7 !important;
        border-color: rgba(16, 185, 129, 0.28) !important;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.08) !important;
    }

    [data-flux-sidebar] a[href="/empresa/dashboard"] svg {
        color: #059669 !important;
    }

    .dark [data-flux-sidebar] a[href="/empresa/dashboard"] svg {
        color: #34d399 !important;
    }

    /* Finance Pro AI: substitui o antigo botão de modo claro/escuro no fundo da sidebar. */
    [data-flux-sidebar] button[x-data*="darkMode"] {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        min-height: 3.75rem !important;
        cursor: default !important;
        pointer-events: none !important;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(255, 255, 255, 0.96)) !important;
        border-color: rgba(16, 185, 129, 0.22) !important;
    }

    .dark [data-flux-sidebar] button[x-data*="darkMode"] {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(24, 24, 27, 0.96)) !important;
        border-color: rgba(16, 185, 129, 0.22) !important;
    }

    [data-flux-sidebar] button[x-data*="darkMode"] > * {
        display: none !important;
    }

    [data-flux-sidebar] button[x-data*="darkMode"]::before {
        content: 'F';
        display: flex;
        width: 2.25rem;
        height: 2.25rem;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        font-size: 1rem;
        font-weight: 900;
        font-style: italic;
        box-shadow: 0 8px 18px rgba(16, 185, 129, 0.22);
    }

    [data-flux-sidebar] button[x-data*="darkMode"]::after {
        content: 'Finance Pro AI\A Gestão financeira inteligente';
        white-space: pre-line;
        display: block;
        color: #18181b;
        font-size: 0.7rem;
        font-weight: 900;
        line-height: 1.45;
        letter-spacing: 0.04em;
        text-align: left;
    }

    .dark [data-flux-sidebar] button[x-data*="darkMode"]::after {
        color: #ffffff;
    }
</style>

@fluxAppearance

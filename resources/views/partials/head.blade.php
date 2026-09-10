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
</style>

@fluxAppearance

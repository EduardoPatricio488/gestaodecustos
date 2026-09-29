<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
    $seoDefaults = [
        'home' => [
            'title' => 'Finance Pro AI — Gestão financeira inteligente',
            'description' => 'Gere as tuas finanças pessoais e empresariais num só lugar. Controla despesas, receitas, investimentos e subscrições com o Finance Pro AI.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'WebSite',
        ],
        'legal.terms' => [
            'title' => 'Termos de Serviço — Finance Pro AI',
            'description' => 'Consulta os Termos de Serviço do Finance Pro AI e as condições de utilização da plataforma.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'WebPage',
        ],
        'legal.privacy' => [
            'title' => 'Política de Privacidade — Finance Pro AI',
            'description' => 'Consulta a Política de Privacidade do Finance Pro AI e conhece como os dados são tratados e protegidos.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'WebPage',
        ],
        'public.contact' => [
            'title' => 'Contacto — Finance Pro AI',
            'description' => 'Entra em contacto com a equipa do Finance Pro AI para obter ajuda, esclarecer dúvidas ou enviar sugestões.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'ContactPage',
        ],
        'careers.apply' => [
            'title' => 'Carreiras — Finance Pro AI',
            'description' => 'Consulta oportunidades de carreira e envia a tua candidatura para fazer parte da equipa Finance Pro AI.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'WebPage',
        ],
    ];

    $seoRoute = request()->route()?->getName();
    $seoPage = $seoDefaults[$seoRoute] ?? null;
    $seoTitle = $seoTitle ?? ($seoPage['title'] ?? ($title ?? config('app.name', 'Finance Pro AI').' — Gestão financeira inteligente'));
    $seoDescription = $seoDescription ?? ($seoPage['description'] ?? 'Finance Pro AI é uma plataforma de gestão financeira pessoal e empresarial para controlar despesas, receitas, investimentos, subscrições e muito mais.');
    $seoImage = $seoImage ?? ($seoPage['image'] ?? asset('og-image.svg'));
    $seoUrl = $seoUrl ?? url()->current();

    $indexableRoutes = array_keys($seoDefaults);
    $isIndexableSeoPage = $isIndexableSeoPage ?? in_array($seoRoute, $indexableRoutes, true);
    $seoRobots = $seoRobots ?? ($isIndexableSeoPage ? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1' : 'noindex,nofollow,noarchive');

    $schemaMarkup = $schemaMarkup ?? [
        '@context' => 'https://schema.org',
        '@type' => $seoPage['schemaType'] ?? 'WebPage',
        'name' => $seoTitle,
        'url' => $seoUrl,
        'description' => $seoDescription,
        'inLanguage' => 'pt-PT',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => config('app.name', 'Finance Pro AI'),
            'url' => url('/'),
        ],
    ];

    if ($seoRoute === 'home') {
        $schemaMarkup = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    'name' => config('app.name', 'Finance Pro AI'),
                    'url' => url('/'),
                    'description' => $seoDescription,
                    'inLanguage' => 'pt-PT',
                ],
                [
                    '@type' => 'Organization',
                    'name' => config('app.name', 'Finance Pro AI'),
                    'url' => url('/'),
                    'logo' => asset('icon-512x512.png'),
                ],
                [
                    '@type' => 'SoftwareApplication',
                    'name' => config('app.name', 'Finance Pro AI'),
                    'applicationCategory' => 'FinanceApplication',
                    'operatingSystem' => 'Web',
                    'url' => url('/'),
                    'description' => $seoDescription,
                    'image' => $seoImage,
                ],
            ],
        ];
    }
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoUrl }}">

<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:site_name" content="{{ config('app.name', 'Finance Pro AI') }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seoTitle }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=20260929">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icon-192x192.png') }}?v=20260929">
<link rel="shortcut icon" href="{{ asset('favicon.svg') }}?v=20260929">
<link rel="apple-touch-icon" href="{{ asset('icon-192x192.png') }}?v=20260929">

<script type="application/ld+json">{!! json_encode($schemaMarkup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ED5683P4Z9"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-ED5683P4Z9');
</script>

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/modal-controls.js'])

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

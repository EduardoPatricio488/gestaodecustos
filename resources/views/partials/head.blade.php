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
            'title' => 'Contacto — Fala com a equipa Finance Pro AI',
            'description' => 'Entra em contacto com a equipa do Finance Pro AI para obter ajuda, esclarecer dúvidas ou enviar sugestões.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'ContactPage',
        ],
        'legal.cookies' => [
            'title' => 'Política de Cookies — Finance Pro AI',
            'description' => 'Conhece os cookies necessários e as opções de análise e tracking do Finance Pro AI.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'WebPage',
        ],
        'legal.accessibility' => [
            'title' => 'Acessibilidade — Finance Pro AI',
            'description' => 'Declaração de acessibilidade e formas de comunicar barreiras no Finance Pro AI.',
            'image' => asset('og-image.svg'),
            'schemaType' => 'WebPage',
        ],
        'careers.apply' => [
            'title' => 'Carreiras e candidaturas — Finance Pro AI',
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
                ['@type' => 'WebSite', 'name' => config('app.name', 'Finance Pro AI'), 'url' => url('/'), 'description' => $seoDescription, 'inLanguage' => 'pt-PT'],
                ['@type' => 'Organization', 'name' => config('app.name', 'Finance Pro AI'), 'url' => url('/'), 'logo' => asset('icon-512x512.png')],
                ...((filled(config('legal.company_name')) && filled(config('legal.address'))) ? [[
                    '@type' => 'LocalBusiness', 'name' => config('legal.company_name'), 'url' => url('/'), 'image' => asset('icon-512x512.png'),
                    'address' => ['@type' => 'PostalAddress', 'streetAddress' => config('legal.address'), 'addressCountry' => 'PT'], 'email' => config('legal.email'),
                ]] : []),
                ['@type' => 'SoftwareApplication', 'name' => config('app.name', 'Finance Pro AI'), 'applicationCategory' => 'FinanceApplication', 'operatingSystem' => 'Web', 'url' => url('/'), 'description' => $seoDescription, 'image' => $seoImage],
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

{{-- Favicon oficial: saco de moedas. Usa SVG diretamente e versão nova para invalidar cache. --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=20260929-3">
<link rel="shortcut icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=20260929-3">
<link rel="apple-touch-icon" href="{{ asset('icon-192x192.png') }}?v=20260929-2">

<script type="application/ld+json">{!! json_encode($schemaMarkup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>

@if (request()->cookie('fp_cookie_consent') === 'analytics')
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ED5683P4Z9"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-ED5683P4Z9');
</script>
@endif

@fonts
@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/modal-controls.js'])

@fluxAppearance

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls ?? [] as $item)
    @php
        $loc = htmlspecialchars((string) ($item['loc'] ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $changefreq = htmlspecialchars((string) ($item['changefreq'] ?? 'weekly'), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $priority = htmlspecialchars((string) ($item['priority'] ?? '0.5'), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    @endphp
    @if ($loc !== '')
    <url>
        <loc>{{ $loc }}</loc>
        <changefreq>{{ $changefreq }}</changefreq>
        <priority>{{ $priority }}</priority>
    </url>
    @endif
@endforeach
</urlset>

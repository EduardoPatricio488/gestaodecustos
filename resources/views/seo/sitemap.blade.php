@php
    $xmlDeclaration = '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
{!! $xmlDeclaration !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls ?? [] as $item)
    @php
        $loc = (string) ($item['loc'] ?? '');
        $changefreq = (string) ($item['changefreq'] ?? 'weekly');
        $priority = (string) ($item['priority'] ?? '0.5');
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

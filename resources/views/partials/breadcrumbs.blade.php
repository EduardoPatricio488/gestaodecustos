@php
    $crumbs = $breadcrumbs ?? [
        ['label' => 'Início', 'url' => route('home')],
    ];
@endphp
<nav aria-label="Breadcrumb" class="mb-6 text-xs font-semibold text-zinc-500">
    <ol class="flex flex-wrap items-center gap-2">
        @foreach($crumbs as $index => $crumb)
            <li class="flex items-center gap-2">
                @if($index > 0)<span aria-hidden="true">/</span>@endif
                @if(!empty($crumb['url']) && $index < count($crumbs) - 1)
                    <a href="{{ $crumb['url'] }}" class="hover:text-emerald-600 underline-offset-2 hover:underline">{{ $crumb['label'] }}</a>
                @else
                    <span aria-current="page">{{ $crumb['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@props([
    'mode' => null,
    'label' => null,
    'color' => 'brand',
    'amount' => null,
    'text' => null,
    'value' => null,
    'bg' => 'zinc-950',
    'textColor' => 'white'
])

{{-- ========================================================= --}}
{{--  COMPONENTE MULTI-MODO WRAPPED                            --}}
{{-- ========================================================= --}}

@switch($mode)

    {{-- TITLE --}}
    @case('title')
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-{{ $color }}-200 dark:text-{{ $color }}-300 mb-6">
            {{ $label }}
        </p>
    @break

    {{-- VALUE --}}
    @case('value')
        <p class="text-7xl md:text-8xl font-black italic tabular-nums tracking-tighter">
            {{ number_format($amount, 0, ',', ' ') }}€
        </p>
    @break

    {{-- SUBTEXT --}}
    @case('subtext')
        <p class="text-zinc-500 dark:text-zinc-400 mt-6 text-xs font-black uppercase tracking-widest opacity-70">
            {{ $text }}
        </p>
    @break

    {{-- BADGE --}}
    @case('badge')
        <div class="mt-8">
            <span class="bg-black/10 dark:bg-white/10 px-6 py-2 rounded-full text-sm font-black backdrop-blur-sm">
                {{ number_format($amount, 0, ',', ' ') }}€
            </span>
        </div>
    @break

    {{-- RECORD --}}
    @case('record')
        <div class="flex justify-between items-center mt-3">
            <span class="font-black uppercase">{{ $label }}</span>
            <span class="font-black">{{ number_format($value, 0, ',', ' ') }}€</span>
        </div>
    @break

    {{-- CARD --}}
    @case('card')
        <div class="p-12 rounded-[2.5rem] min-h-[350px] flex flex-col justify-center text-center shadow-2xl border border-white/5
                    bg-{{ $bg }} text-{{ $textColor }} transition-all duration-500 backdrop-blur-xl">
            {{ $slot }}
        </div>
    @break

@endswitch

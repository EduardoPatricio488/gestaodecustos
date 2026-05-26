@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand {{ $attributes->merge(['class' => 'gap-3']) }}>
        <x-slot name="logo">
            <span class="flex size-9 items-center justify-center rounded-xl brand-gradient shadow-lg shadow-brand-500/25">
                <flux:icon name="currency-euro" variant="micro" class="size-5 text-white" />
            </span>
        </x-slot>
        Gestão de Custos
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo">
            <span class="flex size-9 items-center justify-center rounded-xl brand-gradient shadow-lg shadow-brand-500/25">
                <flux:icon name="currency-euro" variant="micro" class="size-5 text-white" />
            </span>
        </x-slot>
        Gestão de Custos
    </flux:brand>
@endif

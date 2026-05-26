@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div>
        <flux:heading size="xl" class="!text-zinc-900 dark:!text-white">{{ $title }}</flux:heading>
        @if ($description)
            <flux:text class="mt-1 max-w-xl text-zinc-500 dark:text-zinc-400">{{ $description }}</flux:text>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>

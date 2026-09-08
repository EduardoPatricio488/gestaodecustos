@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-8 flex min-w-0 flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        <flux:heading size="xl" class="!text-zinc-900 dark:!text-white">{{ $title }}</flux:heading>
        @if ($description)
            <flux:text class="mt-1 max-w-xl text-zinc-500 dark:text-zinc-400">{{ $description }}</flux:text>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex w-full min-w-0 shrink-0 flex-wrap items-center gap-2 sm:w-auto sm:justify-end">
            {{ $actions }}
        </div>
    @endif
</div>

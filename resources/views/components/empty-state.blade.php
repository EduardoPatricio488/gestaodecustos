@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/50 px-6 py-14 text-center dark:border-zinc-700 dark:bg-zinc-900/30']) }}>
  <div class="mb-4 flex size-14 items-center justify-center rounded-2xl bg-brand-500/10 text-brand-600 dark:text-brand-400">
    <flux:icon :name="$icon" class="size-7" />
  </div>
  <flux:heading size="lg">{{ $title }}</flux:heading>
  @if ($description)
    <flux:text class="mt-2 max-w-sm text-zinc-500">{{ $description }}</flux:text>
  @endif
  @if (isset($action))
    <div class="mt-6">
      {{ $action }}
    </div>
  @endif
</div>

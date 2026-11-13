<div>
    <button type="button" wire:click="openExtraModal" class="inline-flex h-12 items-center gap-2 rounded-2xl bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
        <flux:icon name="plus" class="size-4" /> Nova Assinatura
    </button>
    @if($onboardingSaved)<p role="status" class="mt-3 text-sm font-bold text-emerald-600">Assinatura guardada. Podes adicionar outra ou continuar.</p>@endif
    @if($showExtraModal)
        @include('livewire.subscription-hub', array_merge($data, ['onboardingModal' => true]))
    @endif
</div>

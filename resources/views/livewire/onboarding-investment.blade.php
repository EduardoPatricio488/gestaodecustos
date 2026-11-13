<div x-data="{ showXtbUploadModal: false }" @keydown.escape.window="showXtbUploadModal = false">
    <div class="flex flex-wrap gap-3">
        <button type="button" wire:click="createAsset" class="inline-flex h-12 items-center gap-2 rounded-2xl bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
            <flux:icon name="plus" class="size-4" /> Novo Investimento
        </button>
        <button type="button" @click="showXtbUploadModal = true" class="inline-flex h-12 items-center gap-2 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 text-sm font-bold text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
            <flux:icon name="arrow-up-tray" class="size-4" /> Upload de relatórios
        </button>
    </div>
    @if($onboardingSaved)<p role="status" class="mt-3 text-sm font-bold text-emerald-600">Investimento guardado. Podes adicionar outro ou continuar.</p>@endif
    @include('livewire.partials.investment-modal', ['onboardingModal' => true])
    @include('livewire.partials.investment-report-modal', ['onboardingModal' => true])
</div>

<div class="space-y-4">
    @if(! $showImportForm)
        <button type="button" wire:click="openImportForm" class="inline-flex h-12 items-center gap-2 rounded-2xl bg-blue-600 px-5 text-sm font-bold text-white hover:bg-blue-700">
            <flux:icon name="arrow-up-tray" class="size-4" /> Importar extrato
        </button>
    @else
        @include('livewire.partials.statement-import-form', $data)
    @endif
</div>

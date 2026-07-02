{{-- MODAL: EMITIR NOVA FATURA --}}
<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail.name === 'add-invoice-modal') open = true"
    x-on:modal-close.window="if ($event.detail.name === 'add-invoice-modal') open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40 backdrop-blur-sm"
>

    <div
        x-show="open"
        x-transition
        class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6"
    >
        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-black uppercase tracking-tight dark:text-white">
                Emitir Fatura
            </h2>

            <button
                @click="open = false"
                class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition"
            >
                <flux:icon name="x-mark" class="size-5" />
            </button>
        </div>

        {{-- FORM --}}
        <form wire:submit.prevent="save" class="space-y-4">

            {{-- CLIENTE --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Cliente
                </label>
                <input
                    type="text"
                    wire:model.defer="client_name"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                    placeholder="Nome do cliente"
                >
                @error('client_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NÚMERO DA FATURA --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Nº Fatura
                </label>
                <input
                    type="text"
                    wire:model.defer="invoice_number"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                    placeholder="Automático se vazio"
                >
                @error('invoice_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- VALOR BASE --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Valor Base (€)
                </label>
                <input
                    type="number"
                    step="0.01"
                    wire:model.live="amount_excl_vat"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                    placeholder="0.00"
                >
                @error('amount_excl_vat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- IVA --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    IVA (€)
                </label>
                <input
                    type="number"
                    step="0.01"
                    wire:model="vat_amount"
                    readonly
                    class="w-full mt-1 rounded-xl bg-zinc-200 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm opacity-70"
                >
            </div>

            {{-- TOTAL --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Total (€)
                </label>
                <input
                    type="number"
                    step="0.01"
                    wire:model="total_amount"
                    readonly
                    class="w-full mt-1 rounded-xl bg-zinc-200 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm opacity-70"
                >
            </div>

            {{-- DATA LIMITE --}}
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Data Limite
                </label>
                <input
                    type="date"
                    wire:model.defer="due_date"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                >
                @error('due_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BOTÃO --}}
            <button
                type="submit"
                class="w-full py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black uppercase tracking-widest transition"
            >
                Emitir Fatura
            </button>
        </form>
    </div>
</div>

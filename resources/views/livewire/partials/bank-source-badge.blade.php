{{-- Indica se o valor está associado a uma conta bancária ou é dinheiro físico --}}
@if($record->bank_account_id && $record->bankAccount)
    <span class="inline-flex items-center gap-1 text-[9px] font-black text-zinc-400 uppercase tracking-widest">
        <flux:icon name="building-library" variant="micro" class="size-3" />
        {{ $record->bankAccount->name }}
    </span>
@else
    <span class="inline-flex items-center gap-1 text-[9px] font-black text-zinc-400 uppercase tracking-widest">
        <flux:icon name="banknotes" variant="micro" class="size-3" />
        Dinheiro Físico
    </span>
@endif

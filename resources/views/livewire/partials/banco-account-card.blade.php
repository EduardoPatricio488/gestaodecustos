<div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 group hover:shadow-lg hover:shadow-black/5 transition-all duration-300 relative overflow-hidden">

    {{-- Background accent --}}
    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"
         style="background: linear-gradient(135deg, {{ $acc->color ?? '#6366f1' }}08 0%, transparent 60%)"></div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="size-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                 style="background-color: {{ $acc->color ?? '#6366f1' }}20; border: 1.5px solid {{ $acc->color ?? '#6366f1' }}30">
                <flux:icon name="{{ $acc->icon ?? 'building-library' }}" class="size-5"
                           style="color: {{ $acc->color ?? '#6366f1' }}" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-black text-zinc-900 dark:text-white truncate leading-tight">{{ $acc->name }}</p>
                <p class="text-[10px] text-zinc-400 leading-tight mt-0.5">{{ $acc->bank_name ?? ucfirst($acc->type) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button wire:click="openAccountModal({{ $acc->id }})"
                    class="size-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                <flux:icon name="pencil" variant="micro" class="size-3.5 text-zinc-500" />
            </button>
            <button wire:click="deleteAccount({{ $acc->id }})"
                    onclick="return confirm('Eliminar a conta \'{{ addslashes($acc->name) }}\'?')"
                    class="size-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                <flux:icon name="trash" variant="micro" class="size-3.5 text-red-500" />
            </button>
        </div>
    </div>

    {{-- Saldo --}}
    <div class="mb-3">
        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-0.5">Saldo Actual</p>
        <p class="text-2xl font-black tabular-nums privacy-target {{ $acc->balance < 0 ? 'text-red-600' : 'text-zinc-900 dark:text-white' }}">
            {{ number_format($acc->balance, 2, ',', '.') }} {{ $acc->currency ?? 'EUR' }}
        </p>
    </div>

    {{-- Barra de crédito (apenas para contas crédito) --}}
    @if($acc->type === 'credito' && $acc->credit_limit)
        @php
            $used = abs(min(0, $acc->balance));
            $pct = min(100, ($used / $acc->credit_limit) * 100);
        @endphp
        <div class="mb-3">
            <div class="flex justify-between text-[10px] text-zinc-400 mb-1">
                <span>Crédito usado</span>
                <span>{{ number_format($pct, 1) }}%</span>
            </div>
            <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ $pct }}%; background: {{ $pct > 80 ? '#ef4444' : ($pct > 50 ? '#f59e0b' : ($acc->color ?? '#6366f1')) }}"></div>
            </div>
            <p class="text-[9px] text-zinc-400 mt-1">Limite: {{ number_format($acc->credit_limit, 2, ',', '.') }} €</p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800">
        {{-- IBAN truncado --}}
        @if($acc->iban)
            <p class="text-[10px] text-zinc-400 font-mono truncate max-w-[120px]" title="{{ $acc->iban }}">
                {{ substr($acc->iban, 0, 8) }}•••{{ substr($acc->iban, -4) }}
            </p>
        @else
            <p class="text-[10px] text-zinc-300 dark:text-zinc-700">{{ ucfirst($acc->type) }}</p>
        @endif

        {{-- Estado --}}
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider
            {{ ($acc->status ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
               (($acc->status ?? 'active') === 'archived' ? 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-500' :
               'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400') }}">
            {{ match($acc->status ?? 'active') {
                'active' => 'Activa',
                'archived' => 'Arquivada',
                'hidden' => 'Oculta',
                default => 'Activa'
            } }}
        </span>
    </div>

    {{-- Alerta de saldo baixo --}}
    @if($acc->alert_below !== null && $acc->balance < $acc->alert_below)
        <div class="mt-2 flex items-center gap-1.5 text-[10px] text-amber-600 dark:text-amber-400 font-bold">
            <flux:icon name="exclamation-triangle" variant="micro" class="size-3.5" />
            Abaixo do limite de alerta
        </div>
    @endif
</div>

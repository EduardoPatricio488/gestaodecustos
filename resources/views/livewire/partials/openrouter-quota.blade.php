@php
    $quotaAvailable = (bool) ($aiQuota['available'] ?? false);
    $quotaUsed = (int) ($aiQuota['used'] ?? 0);
    $quotaLimit = (int) ($aiQuota['limit'] ?? 0);
    $quotaRemaining = (int) ($aiQuota['remaining'] ?? 0);
    $quotaPercent = (int) ($aiQuota['percent'] ?? 0);
    $quotaWarning = $quotaAvailable && $quotaLimit > 0 && $quotaPercent >= 80;
    $quotaColor = ! $quotaAvailable ? 'text-zinc-300' : ($quotaRemaining <= 0 ? 'text-red-400' : ($quotaWarning ? 'text-amber-300' : 'text-emerald-300'));
@endphp
<div @if($poll ?? true) wire:poll.60s="refreshScannerQuota" @endif class="flex items-center justify-between gap-3 rounded-xl border {{ !$quotaAvailable ? 'border-zinc-700 bg-zinc-900/70' : ($quotaRemaining <= 0 ? 'border-red-500/30 bg-red-500/5' : ($quotaWarning ? 'border-amber-500/30 bg-amber-500/5' : 'border-emerald-500/20 bg-emerald-500/5')) }} px-3 py-2.5 text-left">
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
            <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400">Quota IA</span>
            @if($quotaAvailable)
                <span class="text-xs font-black {{ $quotaColor }}">{{ number_format($quotaRemaining, 0, ',', '.') }} restantes</span>
                <span class="text-[10px] text-zinc-500">({{ number_format($quotaUsed, 0, ',', '.') }}/{{ number_format($quotaLimit, 0, ',', '.') }})</span>
            @else
                <span class="text-xs font-bold text-zinc-300">Indisponível</span>
            @endif
        </div>
        @if($quotaAvailable)
            <div class="mt-2 h-1.5 w-full max-w-56 overflow-hidden rounded-full bg-black/20">
                <div class="h-full rounded-full {{ $quotaRemaining <= 0 ? 'bg-red-500' : ($quotaWarning ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ min(100, $quotaPercent) }}%"></div>
            </div>
            @if($quotaRemaining <= 0)
                <p class="mt-1 text-[10px] text-red-300">Limite atingido · Reposição às 00:00 UTC</p>
            @elseif($quotaWarning)
                <p class="mt-1 text-[10px] text-amber-300">A aproximar-se do limite · Reposição às 00:00 UTC</p>
            @endif
        @else
            <p class="mt-1 text-[10px] text-zinc-400">{{ $aiQuota['error'] ?? 'Não foi possível consultar a quota.' }}</p>
        @endif
    </div>
    <button type="button" wire:click="refreshScannerQuota" wire:loading.attr="disabled" wire:target="refreshScannerQuota" class="shrink-0 rounded-lg border border-white/10 px-2.5 py-1.5 text-[9px] font-bold uppercase text-zinc-300 hover:bg-white/5 disabled:opacity-50">Atualizar</button>
</div>
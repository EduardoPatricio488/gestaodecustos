@php
    $quotaAvailable = (bool) ($aiQuota['available'] ?? false);
    $quotaUsed = (int) ($aiQuota['used'] ?? 0);
    $quotaLimit = (int) ($aiQuota['limit'] ?? 0);
    $quotaRemaining = (int) ($aiQuota['remaining'] ?? 0);
    $quotaPercent = (int) ($aiQuota['percent'] ?? 0);
    $quotaWarning = $quotaAvailable && $quotaLimit > 0 && $quotaPercent >= 80;
@endphp
<div @if($poll ?? true) wire:poll.60s="refreshScannerQuota" @endif class="flex items-center justify-between gap-3 px-1 py-1 text-left">
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
            <span class="text-[10px] font-semibold text-zinc-400">Quota IA</span>
            @if($quotaAvailable)
                <span class="text-[10px] font-semibold {{ $quotaRemaining <= 0 ? 'text-zinc-500' : ($quotaWarning ? 'text-amber-500' : 'text-emerald-600') }}">{{ number_format($quotaRemaining, 0, ',', '.') }} disponíveis</span>
                <span class="text-[10px] text-zinc-400">{{ number_format($quotaUsed, 0, ',', '.') }}/{{ number_format($quotaLimit, 0, ',', '.') }}</span>
                @if($quotaRemaining <= 0)
                    <span class="text-[10px] text-zinc-400">· Reposição às 00:00 UTC</span>
                @elseif($quotaWarning)
                    <span class="text-[10px] text-zinc-400">· Próximo do limite</span>
                @endif
            @else
                <span class="text-[10px] text-zinc-400">Indisponível</span>
            @endif
        </div>
        @if($quotaAvailable)
            <div class="mt-1.5 h-1 w-full max-w-40 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800">
                <div class="h-full rounded-full {{ $quotaRemaining <= 0 ? 'bg-zinc-400' : ($quotaWarning ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ min(100, $quotaPercent) }}%"></div>
            </div>
        @elseif(!empty($aiQuota['error']))
            <p class="mt-1 text-[10px] text-zinc-400">{{ $aiQuota['error'] }}</p>
        @endif
    </div>
    <button type="button" wire:click="refreshScannerQuota" wire:loading.attr="disabled" wire:target="refreshScannerQuota" class="shrink-0 px-1.5 py-1 text-[10px] text-zinc-400 transition hover:text-zinc-600 dark:hover:text-zinc-200 disabled:opacity-50">Atualizar</button>
</div>
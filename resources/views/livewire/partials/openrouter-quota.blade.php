@php
    $quotaAvailable = (bool) ($aiQuota['available'] ?? false);
    $quotaUsed = (int) ($aiQuota['used'] ?? 0);
    $quotaLimit = (int) ($aiQuota['limit'] ?? 0);
    $quotaRemaining = (int) ($aiQuota['remaining'] ?? 0);
    $quotaPercent = (int) ($aiQuota['percent'] ?? 0);
    $quotaWarning = $quotaAvailable && $quotaLimit > 0 && $quotaPercent >= 80;
@endphp
<div wire:poll.60s="refreshScannerQuota" class="rounded-2xl border {{ !$quotaAvailable ? 'border-zinc-700 bg-zinc-900/70' : ($quotaRemaining <= 0 ? 'border-red-500/30 bg-red-500/10' : ($quotaWarning ? 'border-amber-500/30 bg-amber-500/10' : 'border-emerald-500/20 bg-emerald-500/5')) }} p-4 text-left">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Quota diária da IA</p>
            @if($quotaAvailable)
                <p class="mt-1 text-sm font-black {{ $quotaRemaining <= 0 ? 'text-red-400' : ($quotaWarning ? 'text-amber-300' : 'text-emerald-300') }}">
                    {{ number_format($quotaRemaining, 0, ',', '.') }} pedidos restantes
                </p>
            @else
                <p class="mt-1 text-xs font-bold text-zinc-300">Quota indisponível</p>
            @endif
        </div>
        <button type="button" wire:click="refreshScannerQuota" wire:loading.attr="disabled" wire:target="refreshScannerQuota" class="rounded-lg border border-white/10 px-3 py-2 text-[10px] font-black uppercase text-zinc-300 hover:bg-white/5 disabled:opacity-50">Atualizar</button>
    </div>
    @if($quotaAvailable)
        <div class="mt-3 h-2 overflow-hidden rounded-full bg-black/20">
            <div class="h-full rounded-full {{ $quotaRemaining <= 0 ? 'bg-red-500' : ($quotaWarning ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ $quotaPercent }}%"></div>
        </div>
        <div class="mt-2 flex flex-wrap justify-between gap-2 text-[10px] font-semibold text-zinc-400">
            <span>{{ number_format($quotaUsed, 0, ',', '.') }} de {{ number_format($quotaLimit, 0, ',', '.') }} utilizados ({{ $quotaPercent }}%)</span>
            <span>Reinicia às 00:00 UTC</span>
        </div>
        @if($quotaRemaining <= 0)
            <p class="mt-2 text-xs font-bold text-red-300">Quota esgotada. O upload pode ser feito, mas a análise por IA só estará disponível após a reposição.</p>
        @elseif($quotaWarning)
            <p class="mt-2 text-xs font-bold text-amber-300">Estás perto do limite diário de pedidos gratuitos.</p>
        @endif
    @else
        <p class="mt-2 text-[10px] text-zinc-400">{{ $aiQuota['error'] ?? 'Não foi possível obter a quota.' }} O estado será atualizado automaticamente.</p>
    @endif
</div>

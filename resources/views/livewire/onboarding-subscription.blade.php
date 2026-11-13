<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <button type="button" wire:click="openExtraModal" class="inline-flex h-12 items-center gap-2 rounded-2xl bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
            <flux:icon name="plus" class="size-4" /> Nova Assinatura
        </button>
        @if($onboardingSaved)
            <p role="status" class="text-sm font-bold text-emerald-600">Assinatura guardada. Podes adicionar outra ou continuar.</p>
        @endif
    </div>

    <section class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-200">Assinaturas guardadas</h3>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Serviços recorrentes registados neste espaço financeiro.</p>
            </div>
            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-black text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $savedSubscriptions->count() }}</span>
        </div>

        @forelse($savedSubscriptions as $subscription)
            @php
                $cycleLabel = match ($subscription->cycle) {
                    'quarterly' => 'Trimestral',
                    'semiannual' => 'Semestral',
                    'annual', 'yearly' => 'Anual',
                    default => 'Mensal',
                };
                $status = $subscription->status ?: ($subscription->is_active ? 'active' : 'paused');
                $statusLabel = match ($status) {
                    'paused' => 'Pausada',
                    'cancelled' => 'Cancelada',
                    default => 'Ativa',
                };
            @endphp
            <article wire:key="onboarding-subscription-{{ $subscription->id }}" class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="truncate text-sm font-black text-zinc-900 dark:text-white">{{ $subscription->name }}</h4>
                            <span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase tracking-wider {{ $status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : ($status === 'paused' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300') }}">{{ $statusLabel }}</span>
                        </div>
                        <p class="mt-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                            {{ $subscription->category?->name ?? 'Sem categoria' }} · {{ $cycleLabel }}
                            @if($subscription->bankAccount)
                                · {{ $subscription->bankAccount->name }}
                            @endif
                        </p>
                        <p class="mt-1 text-[10px] text-zinc-400">
                            Início: {{ $subscription->started_at?->format('d/m/Y') ?? '—' }}
                            <span class="mx-1">·</span>
                            Renovação: {{ $subscription->renewal_date?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                    <p class="shrink-0 text-base font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format((float) $subscription->amount, 2, ',', '.') }} €</p>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-zinc-200 px-5 py-7 text-center dark:border-zinc-800">
                <flux:icon name="credit-card" class="mx-auto size-7 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-2 text-sm font-semibold text-zinc-500 dark:text-zinc-400">Ainda não tens assinaturas guardadas.</p>
                <p class="mt-1 text-xs text-zinc-400">Carrega em «Nova Assinatura» para registares o primeiro serviço recorrente.</p>
            </div>
        @endforelse
    </section>

    @if($showExtraModal)
        @include('livewire.subscription-hub', array_merge($data, ['onboardingModal' => true]))
    @endif
</div>

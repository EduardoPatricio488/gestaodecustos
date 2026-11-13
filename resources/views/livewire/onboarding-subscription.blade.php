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
                    <div class="flex shrink-0 flex-col items-end gap-3">
                        <p class="text-base font-black tabular-nums text-zinc-900 dark:text-white">{{ number_format((float) $subscription->amount, 2, ',', '.') }} €</p>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="edit({{ $subscription->id }})" class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-zinc-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-300">
                                <flux:icon name="pencil-square" class="size-3.5" /> Editar
                            </button>
                            <button type="button" wire:click="delete({{ $subscription->id }})" wire:confirm="Tens a certeza de que pretendes eliminar esta assinatura?" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-rose-600 transition hover:bg-rose-50 dark:border-rose-900/60 dark:text-rose-400 dark:hover:bg-rose-950/30">
                                <flux:icon name="trash" class="size-3.5" /> Eliminar
                            </button>
                        </div>
                    </div>
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

@php
    $isCurrent = $currentPlan === $plan->slug;
    $isBusiness = ($variant ?? '') === 'business' || $plan->hasFeature('business_mode');
    $isExtra = ($variant ?? '') === 'extra';
@endphp

<div class="glass-card p-8 {{ $isExtra ? 'bg-white dark:bg-zinc-900 border-2 border-amber-400' : ($isBusiness ? 'bg-zinc-950 text-white border border-zinc-800' : 'bg-white dark:bg-zinc-900 border-2 border-emerald-500') }} rounded-[2.5rem] flex flex-col relative {{ $isCurrent ? 'ring-4 ring-brand-500/20' : '' }}">
    @if($isExtra)
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-amber-500 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl">Extra</div>
    @elseif($isBusiness)
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-violet-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl">Empresa</div>
    @endif

    <div class="mb-8">
        <span class="px-3 py-1 rounded-full {{ $isExtra ? 'bg-amber-500 text-white' : ($isBusiness ? 'bg-violet-500 text-white' : 'bg-emerald-500 text-white') }} text-[10px] font-black uppercase">{{ $plan->name }}</span>
        <div class="mt-4 flex items-baseline gap-1">
    {{-- Mostra o preço formatado. Ex: 5,00 ou 10,00 --}}
    <span class="text-5xl font-black {{ $isBusiness && ! $isExtra ? 'text-white' : 'dark:text-white text-zinc-900' }}">
        {{ number_format($plan->price, 0, ',', ' ') }}€
    </span>
    <span class="{{ $isBusiness && ! $isExtra ? 'text-zinc-400' : 'text-zinc-500' }} font-bold">/mês</span>
</div>
        @if($plan->description)
            <p class="mt-3 text-sm {{ $isBusiness && ! $isExtra ? 'text-zinc-400' : 'text-zinc-500' }}">{{ $plan->description }}</p>
        @endif
    </div>

    <ul class="space-y-3 mb-10 flex-1">
        @forelse($plan->featureKeys() as $feat)
            <li class="flex items-center gap-3 text-sm font-medium {{ $isBusiness && ! $isExtra ? 'text-white' : 'dark:text-white' }}">
                <flux:icon name="check-circle" variant="solid" class="{{ $isExtra ? 'text-amber-500' : ($isBusiness ? 'text-violet-500' : 'text-emerald-500') }} w-5 h-5" />
                {{ \App\Models\SubscriptionPlan::FEATURE_LABELS[$feat] ?? str_replace('_', ' ', $feat) }}
            </li>
        @empty
            <li class="text-sm text-zinc-400 italic">Sem extras listados.</li>
        @endforelse
    </ul>

    @if($isCurrent && $plan->hasFeature('business_mode'))
        <flux:button href="{{ route('hub.business.gateway') }}" variant="primary" class="w-full !h-12 font-black uppercase tracking-widest">Aceder à Empresa</flux:button>
    @else
        <flux:button wire:click="upgrade('{{ $plan->slug }}')" variant="{{ $isCurrent ? 'ghost' : 'primary' }}" class="w-full !h-12 font-bold uppercase" :disabled="$isCurrent">
            {{ $isCurrent ? 'Plano Ativo' : 'Aderir a '.$plan->name }}
        </flux:button>
    @endif
</div>

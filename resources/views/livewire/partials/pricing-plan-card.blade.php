@php
    $isCurrent = $currentPlan === $plan->slug;
    $isBusiness = ($variant ?? '') === 'business' || $plan->hasFeature('business_mode');
    $isExtra = ($variant ?? '') === 'extra';

    // 🔥 LÓGICA DE COR DINÂMICA (Ponto crucial para a tua personalização)
    // 1. Usa a cor da BD | 2. Se for Business usa Roxo | 3. Fallback: Verde esmeralda
    $brandColor = $plan->color ?? ($isBusiness ? '#8b5cf6' : '#10b981');
@endphp

<div class="glass-card p-8 rounded-[2.5rem] flex flex-col relative transition-all duration-500 hover:scale-[1.02]
    {{ $isExtra ? 'bg-white dark:bg-zinc-900 border-2' : ($isBusiness ? 'bg-zinc-950 text-white border border-zinc-800 shadow-2xl scale-105 z-10' : 'bg-white dark:bg-zinc-900 border-2 border-zinc-100 dark:border-zinc-800 shadow-lg') }}
    {{ $isCurrent ? 'ring-4' : '' }}"
    style="{{ $isCurrent ? 'ring-color: ' . $brandColor . '33;' : '' }} {{ $isExtra ? 'border-color: ' . $brandColor . ';' : '' }}">

    {{-- BADGES NO TOPO (Extra / Empresa) --}}
    @if($isExtra)
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl"
             style="background-color: {{ $brandColor }};">
             Extra
        </div>
    @elseif($isBusiness)
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-violet-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl">
            Empresa
        </div>
    @endif

    <div class="mb-8">
        {{-- NOME DO PLANO --}}
        <span class="px-3 py-1 rounded-full text-white text-[10px] font-black uppercase shadow-sm"
              style="background-color: {{ $brandColor }};">
            {{ $plan->name }}
        </span>

        {{-- PREÇO --}}
        <div class="mt-4 flex items-baseline gap-1">
            <span class="text-5xl font-black {{ ($isBusiness && !$isExtra) ? 'text-white' : 'dark:text-white text-zinc-900' }}">
                {{ number_format($plan->price, 0, ',', ' ') }}€
            </span>
            <span class="{{ ($isBusiness && !$isExtra) ? 'text-zinc-400' : 'text-zinc-500' }} font-bold">/mês</span>
        </div>

        {{-- DESCRIÇÃO --}}
        @if($plan->description)
            <p class="mt-4 text-sm font-medium italic leading-relaxed {{ ($isBusiness && !$isExtra) ? 'text-zinc-400' : 'text-zinc-500' }}">
                {{ $plan->description }}
            </p>
        @endif
    </div>

    {{-- LISTA DE FUNCIONALIDADES --}}
    <ul class="space-y-4 mb-10 flex-1 text-left">
        @forelse($plan->featureKeys() as $feat)
            <li class="flex items-center gap-3 text-sm font-bold {{ ($isBusiness && !$isExtra) ? 'text-zinc-200' : 'text-zinc-700 dark:text-zinc-300' }}">
                <flux:icon name="check-circle" variant="solid" class="w-5 h-5 shrink-0" style="color: {{ $brandColor }};" />
                {{ \App\Models\SubscriptionPlan::FEATURE_LABELS[$feat] ?? str_replace('_', ' ', ucfirst($feat)) }}
            </li>
        @empty
            <li class="text-sm text-zinc-400 italic text-center">Sem funcionalidades listadas.</li>
        @endforelse
    </ul>

    {{-- BOTÕES DE ACÇÃO --}}
    @if($isCurrent && $plan->hasFeature('business_mode'))
        <flux:button href="{{ route('hub.business.gateway') }}" variant="primary" class="w-full !h-14 font-black uppercase tracking-widest shadow-lg rounded-2xl">
            Aceder à Empresa
        </flux:button>
    @else
        <flux:button
            wire:click="upgrade('{{ $plan->slug }}')"
            variant="{{ $isCurrent ? 'ghost' : 'primary' }}"
            class="w-full !h-14 font-black uppercase tracking-widest shadow-lg rounded-2xl transition-all active:scale-95"
            style="{{ !$isCurrent ? 'background-color: ' . $brandColor . ';' : '' }} {{ !$isCurrent ? 'border: none;' : '' }}"
            :disabled="$isCurrent"
        >
            {{ $isCurrent ? 'Plano Ativo' : 'Aderir ao ' . $plan->name }}
        </flux:button>
    @endif
</div>

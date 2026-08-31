<div class="max-w-6xl mx-auto py-12 px-6 space-y-12 text-left">
    <div class="text-center space-y-4">
        <flux:heading size="xl" class="text-5xl font-black italic tracking-tighter uppercase">Planos e Mensalidades</flux:heading>
        <flux:subheading class="text-lg text-zinc-500">Escolha o nível de controlo que a sua família ou negócio exige.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 items-stretch">
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] flex flex-col {{ in_array($currentPlan, ['free', '', null], true) ? 'ring-2 ring-zinc-500' : '' }}">
            <div class="mb-8">
                <span class="px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase text-zinc-500">Free</span>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-5xl font-black dark:text-white">0€</span>
                    <span class="text-zinc-500 font-bold">/mês</span>
                </div>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center gap-3 text-sm font-medium"><flux:icon name="check-circle" variant="solid" class="text-emerald-500 w-5 h-5" /> Gestão Pessoal Simples</li>
                <li class="flex items-center gap-3 text-sm font-medium text-zinc-400"><flux:icon name="lock-closed" class="w-5 h-5 opacity-40" /> Sem Área Empresa</li>
            </ul>
            <flux:button wire:click="upgrade('free')" variant="ghost" class="w-full font-bold" :disabled="in_array($currentPlan, ['free', null, ''], true)">
                {{ in_array($currentPlan, ['free', null, ''], true) ? 'Plano Atual' : 'Mudar para Grátis' }}
            </flux:button>
        </div>

        @foreach($corePlans as $plan)
            @include('livewire.partials.pricing-plan-card', ['plan' => $plan, 'currentPlan' => $currentPlan, 'variant' => $plan->slug === 'business' ? 'business' : 'pro'])
        @endforeach
    </div>

    @if($extraPlans->isNotEmpty())
        <div class="space-y-6 pt-4">
            <div class="flex items-center gap-4">
                <h3 class="text-xs font-black uppercase tracking-[0.25em] text-zinc-400 whitespace-nowrap">Planos extras</h3>
                <div class="flex-1 h-px bg-zinc-200 dark:bg-zinc-800"></div>
            </div>
            <p class="text-sm text-zinc-500 -mt-2">Ofertas adicionais publicadas no gestor de preçários.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 items-stretch">
                @foreach($extraPlans as $plan)
                    @include('livewire.partials.pricing-plan-card', ['plan' => $plan, 'currentPlan' => $currentPlan, 'variant' => 'extra'])
                @endforeach
            </div>
        </div>
    @endif

    @if($showSuccessModal)
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-zinc-950/80 backdrop-blur-xl">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-[3rem] shadow-2xl p-10 text-center">
                <div class="text-6xl mb-6">{{ $newPlanData['icon'] }}</div>
                <h2 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter">Plano Ativado!</h2>
                <p class="text-zinc-500 mt-4">O plano {{ $newPlanData['name'] }} está ativo nesta conta.</p>
                <flux:button wire:click="finish" variant="primary" class="w-full h-14 mt-10 rounded-2xl font-black uppercase tracking-widest shadow-lg">
                    Começar a usar
                </flux:button>
            </div>
        </div>
    @endif
</div>

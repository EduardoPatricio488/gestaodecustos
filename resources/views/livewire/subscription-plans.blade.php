<div class="max-w-6xl mx-auto py-12 px-6 space-y-12 text-left">
    <div class="text-center space-y-4">
        <flux:heading size="xl" class="text-5xl font-black italic tracking-tighter uppercase">Planos e Mensalidades</flux:heading>
        <flux:subheading class="text-lg text-zinc-500">Escolha o nível de controlo que a sua família ou negócio exige.</flux:subheading>
        <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">Modo demonstração — ativação imediata sem pagamento real</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
        {{-- PLANO 0€ --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] flex flex-col {{ $currentPlan == 'free' ? 'ring-2 ring-zinc-500' : '' }}">
            <div class="mb-8">
                <span class="px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase text-zinc-500">Gratuito</span>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-5xl font-black dark:text-white">0€</span>
                    <span class="text-zinc-500 font-bold">/mês</span>
                </div>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center gap-3 text-sm font-medium"><flux:icon name="check-circle" variant="solid" class="text-emerald-500 w-5 h-5" /> Gestão Pessoal Simples</li>
                <li class="flex items-center gap-3 text-sm font-medium text-zinc-400"><flux:icon name="lock-closed" class="w-5 h-5 opacity-40" /> Sem Área Empresa</li>
            </ul>
            <flux:button wire:click="upgrade('free')" variant="ghost" class="w-full font-bold" :disabled="$currentPlan == 'free'">
                {{ $currentPlan == 'free' ? 'Plano Atual' : 'Mudar para Grátis' }}
            </flux:button>
        </div>

        {{-- PLANO 5€ - PREMIUM (PLUS) --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border-2 border-emerald-500 rounded-[2.5rem] flex flex-col relative shadow-xl shadow-emerald-500/10 {{ $currentPlan == 'plus' ? 'ring-4 ring-emerald-500/20' : '' }}">
            <div class="mb-8">
                <span class="px-3 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-black uppercase">Premium ⭐</span>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-5xl font-black dark:text-white">5€</span>
                    <span class="text-zinc-500 font-bold">/mês</span>
                </div>
            </div>
            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center gap-3 text-sm font-medium dark:text-white"><flux:icon name="check-circle" variant="solid" class="text-emerald-500 w-5 h-5" /> Scanner IA de Recibos</li>
                <li class="flex items-center gap-3 text-sm font-medium dark:text-white"><flux:icon name="check-circle" variant="solid" class="text-emerald-500 w-5 h-5" /> Contas de Casal</li>
            </ul>
            <flux:button wire:click="upgrade('plus')" variant="{{ $currentPlan == 'plus' ? 'ghost' : 'primary' }}" color="emerald" class="w-full !h-12 font-bold uppercase shadow-lg shadow-emerald-500/20" :disabled="$currentPlan == 'plus'">
                {{ $currentPlan == 'plus' ? 'Plano Ativo' : 'Obter Premium' }}
            </flux:button>
        </div>

        {{-- PLANO 10€ - BUSINESS (PRO) --}}
        <div class="glass-card p-8 bg-zinc-950 text-white border border-zinc-800 rounded-[2.5rem] flex flex-col relative scale-105 shadow-2xl {{ $currentPlan == 'pro' ? 'ring-4 ring-violet-500/20' : '' }}">
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-violet-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl">Recomendado</div>
            <div class="mb-8">
                <span class="px-3 py-1 rounded-full bg-violet-500 text-white text-[10px] font-black uppercase">Business 💎</span>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-5xl font-black text-white">10€</span>
                    <span class="text-zinc-400 font-bold">/mês</span>
                </div>
            </div>
            <ul class="space-y-4 mb-10 flex-1 text-left">
                <li class="flex items-center gap-3 text-sm font-bold text-white"><flux:icon name="check-circle" variant="solid" class="text-violet-500 w-5 h-5" /> Toda a Área Empresa</li>
                <li class="flex items-center gap-3 text-sm font-bold text-white"><flux:icon name="check-circle" variant="solid" class="text-violet-500 w-5 h-5" /> Gestão de RH & Férias</li>
                <li class="flex items-center gap-3 text-sm font-bold text-white"><flux:icon name="check-circle" variant="solid" class="text-violet-500 w-5 h-5" /> IA Estrategista de Negócio</li>
            </ul>

            {{-- LÓGICA DE ACESSO AO GATEWAY --}}
            @if($currentPlan == 'pro')
                <flux:button href="{{ route('hub.business.gateway') }}" variant="primary" color="violet" class="w-full !h-14 font-black uppercase tracking-widest bg-violet-600 hover:bg-violet-700 border-none shadow-xl shadow-violet-500/20">
                    Aceder à Empresa
                </flux:button>
            @else
                <flux:button wire:click="upgrade('pro')" variant="primary" color="violet" class="w-full !h-14 font-black uppercase tracking-widest bg-violet-600 hover:bg-violet-700 border-none shadow-xl shadow-violet-500/20">
                    Aderir ao Business
                </flux:button>
            @endif
        </div>
    </div>

    {{-- MODAL DE SUCESSO --}}
    @if($showSuccessModal)
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-zinc-950/80 backdrop-blur-xl animate-in fade-in duration-500">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-[3rem] shadow-2xl p-10 text-center animate-in zoom-in-95 duration-300">
                <div class="text-6xl mb-6">{{ $newPlanData['icon'] }}</div>
                <h2 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter">Plano Ativado!</h2>
                <p class="text-zinc-500 mt-4">Agora tens acesso ao ecossistema Business do Finance Pro.</p>

                <flux:button wire:click="finish" variant="primary" color="{{ $newPlanData['color'] }}" class="w-full h-14 mt-10 rounded-2xl font-black uppercase tracking-widest shadow-lg">
                    Começar a usar
                </flux:button>
            </div>
        </div>
    @endif
</div>

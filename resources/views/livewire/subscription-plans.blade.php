<div class="max-w-6xl mx-auto py-12 px-6 space-y-12">
    <div class="text-center space-y-4">
        <flux:heading size="xl" class="text-5xl font-black">Planos e Mensalidades</flux:heading>
        <flux:subheading class="text-lg text-zinc-500">Escolha o nível de controlo que a sua família ou negócio exige.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">

        {{-- PLANO 0€ --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] flex flex-col">
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

            <flux:button wire:click="upgrade('free')" variant="ghost" class="w-full" :disabled="$currentPlan == 'free'">
                {{ $currentPlan == 'free' ? 'Plano Atual' : 'Mudar para Grátis' }}
            </flux:button>
        </div>

        {{-- PLANO 5€ --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border-2 border-brand-500 rounded-[2.5rem] flex flex-col relative shadow-xl shadow-brand-500/10">
            <div class="mb-8">
                <span class="px-3 py-1 rounded-full bg-brand-500 text-white text-[10px] font-black uppercase">Premium ⭐</span>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-5xl font-black dark:text-white">5€</span>
                    <span class="text-zinc-500 font-bold">/mês</span>
                </div>
            </div>

            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center gap-3 text-sm font-medium text-zinc-900 dark:text-white"><flux:icon name="check-circle" variant="solid" class="text-brand-500 w-5 h-5" /> Scanner IA de Recibos</li>
                <li class="flex items-center gap-3 text-sm font-medium text-zinc-900 dark:text-white"><flux:icon name="check-circle" variant="solid" class="text-brand-500 w-5 h-5" /> Contas de Casal</li>
                <li class="flex items-center gap-3 text-sm font-medium text-zinc-400"><flux:icon name="lock-closed" class="w-5 h-5 opacity-40" /> Sem Área Empresa</li>
            </ul>

            <flux:button wire:click="upgrade('plus')" variant="{{ $currentPlan == 'plus' ? 'ghost' : 'primary' }}" class="w-full !h-12 font-bold uppercase" :disabled="$currentPlan == 'plus'">
                {{ $currentPlan == 'plus' ? 'Plano Ativo' : 'Obter Premium' }}
            </flux:button>
        </div>

        {{-- PLANO 10€ - BUSINESS --}}
        <div class="glass-card p-8 bg-zinc-950 text-white border border-zinc-800 rounded-[2.5rem] flex flex-col relative scale-105 shadow-2xl">
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-purple-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Recomendado</div>

            <div class="mb-8">
                <span class="px-3 py-1 rounded-full bg-purple-500 text-white text-[10px] font-black uppercase">Business 💎</span>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-5xl font-black text-white">10€</span>
                    <span class="text-zinc-400 font-bold">/mês</span>
                </div>
            </div>

            <ul class="space-y-4 mb-10 flex-1">
                <li class="flex items-center gap-3 text-sm font-bold text-white"><flux:icon name="check-circle" variant="solid" class="text-purple-500 w-5 h-5" /> Toda a Área Empresa</li>
                <li class="flex items-center gap-3 text-sm font-bold text-white"><flux:icon name="check-circle" variant="solid" class="text-purple-500 w-5 h-5" /> Gestão de RH & Férias</li>
                <li class="flex items-center gap-3 text-sm font-bold text-white"><flux:icon name="check-circle" variant="solid" class="text-purple-500 w-5 h-5" /> IA Estrategista de Negócio</li>
            </ul>

            {{-- ESTE BOTÃO FAZ O REDIRECIONAMENTO PARA A EMPRESA --}}
            <flux:button wire:click="upgrade('pro')" variant="primary" class="w-full !h-14 font-black uppercase tracking-widest bg-purple-600 hover:bg-purple-700 border-none shadow-xl shadow-purple-500/20">
                {{ $currentPlan == 'pro' ? 'Aceder à Empresa' : 'Aderir ao Business' }}
            </flux:button>
        </div>

    </div>
</div>

<div class="max-w-3xl mx-auto py-12 px-6">
    {{-- PROGRESSO --}}
    <div class="flex items-center gap-4 mb-12">
        @foreach([1, 2, 3] as $i)
            <div class="h-2 flex-1 rounded-full {{ $step >= $i ? 'bg-brand-600' : 'bg-zinc-200 dark:bg-zinc-800' }}"></div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-12 shadow-2xl relative overflow-hidden">

        {{-- PASSO 1: EXPLICAÇÃO --}}
        @if($step == 1)
            <div class="space-y-8 animate-in fade-in zoom-in duration-500">
                <div class="size-20 bg-brand-500/10 text-brand-600 rounded-[2rem] flex items-center justify-center shadow-inner">
                    <flux:icon name="presentation-chart-line" variant="solid" class="size-10" />
                </div>

                <div class="space-y-4">
                    <h1 class="text-4xl font-black uppercase italic italic tracking-tighter dark:text-white">O Seu Novo Centro de Comando</h1>
                    <p class="text-zinc-500 text-lg leading-relaxed">
                        A área empresarial foi desenhada para separar as suas finanças pessoais dos seus negócios. Aqui terá acesso a:
                    </p>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm font-bold text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-center gap-2"><flux:icon name="check-circle" class="size-4 text-emerald-500" /> Faturação Certificada</li>
                        <li class="flex items-center gap-2"><flux:icon name="check-circle" class="size-4 text-emerald-500" /> Gestão de Equipa</li>
                        <li class="flex items-center gap-2"><flux:icon name="check-circle" class="size-4 text-emerald-500" /> Fluxo de Caixa Real</li>
                        <li class="flex items-center gap-2"><flux:icon name="check-circle" class="size-4 text-emerald-500" /> IA Estrategista</li>
                    </ul>
                </div>

                <flux:button wire:click="nextStep" variant="primary" class="w-full h-16 rounded-2xl font-black uppercase tracking-widest text-sm">
                    Configurar a minha Empresa
                </flux:button>
            </div>
        @endif

        {{-- PASSO 2: DADOS --}}
        @if($step == 2)
            <div class="space-y-8 animate-in slide-in-from-right duration-300">
                <h2 class="text-3xl font-black uppercase italic dark:text-white">Identidade Corporativa</h2>

                <div class="flex flex-col items-center gap-4">
                    <label class="relative group cursor-pointer">
                        <div class="size-32 rounded-[2.5rem] border-4 border-dashed border-zinc-200 dark:border-zinc-800 flex items-center justify-center overflow-hidden bg-zinc-50 dark:bg-zinc-950 transition-all group-hover:border-brand-500/50">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="size-full object-cover">
                            @else
                                <flux:icon name="camera" class="size-8 text-zinc-300 group-hover:text-brand-500 transition-colors" />
                            @endif
                        </div>
                        <input type="file" wire:model="photo" class="hidden">
                    </label>
                    <span class="text-[10px] font-black uppercase text-zinc-400">Logótipo da Empresa</span>
                </div>

                <div class="grid gap-6">
                    <flux:input wire:model="name" label="Nome Comercial" placeholder="Ex: Finance Pro Lda" />
                    <flux:select wire:model="industry" label="Área de Atuação">
                        <option value="">Selecione...</option>
                        <option value="Tecnologia">Tecnologia & Software</option>
                        <option value="Serviços">Prestação de Serviços</option>
                        <option value="Retalho">Comércio / Loja</option>
                        <option value="Construção">Construção & Imobiliário</option>
                    </flux:select>
                    <flux:input wire:model="tax_number" label="NIF / Tax ID" placeholder="Opcional" />
                </div>

                <div class="flex gap-4">
                    <flux:button wire:click="prevStep" variant="ghost" class="flex-1 h-14 rounded-2xl">Voltar</flux:button>
                    <flux:button wire:click="nextStep" variant="primary" class="flex-[2] h-14 rounded-2xl font-black uppercase">Próximo Passo</flux:button>
                </div>
            </div>
        @endif

        {{-- PASSO 3: CAPITAL --}}
        @if($step == 3)
            <div class="space-y-8 animate-in slide-in-from-right duration-300 text-center">
                <h2 class="text-3xl font-black uppercase italic dark:text-white">Estado Bancário Atual</h2>
                <p class="text-zinc-500">Qual é o capital disponível nas contas da empresa neste momento?</p>

                <div class="relative max-w-xs mx-auto my-10">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-2xl font-black text-brand-600">€</span>
                    <input type="number" wire:model="initial_capital"
                        class="w-full bg-zinc-50 dark:bg-zinc-950 border-none rounded-[2rem] h-24 text-center text-5xl font-black focus:ring-4 focus:ring-brand-500/20 dark:text-white shadow-inner">
                </div>

                <div class="p-6 bg-amber-50 dark:bg-amber-900/10 rounded-3xl border border-amber-100 dark:border-amber-800/50 text-left">
                    <p class="text-xs text-amber-700 dark:text-amber-400 font-bold leading-relaxed italic">
                        * Este valor servirá de base para o cálculo da sua "Runway" (previsão de quanto tempo a empresa sobrevive com os custos atuais).
                    </p>
                </div>

                <div class="flex gap-4">
                    <flux:button wire:click="prevStep" variant="ghost" class="flex-1 h-14 rounded-2xl">Voltar</flux:button>
                    <flux:button wire:click="createCompany" variant="primary" class="flex-[2] h-14 rounded-2xl font-black uppercase bg-emerald-600 hover:bg-emerald-700 border-none shadow-xl shadow-emerald-500/20">
                        Ativar Empresa 🚀
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</div>

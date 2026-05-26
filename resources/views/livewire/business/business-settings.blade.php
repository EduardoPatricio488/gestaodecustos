<div class="space-y-10 pb-24">
    {{-- 1. HEADER DE CONFIGURAÇÃO (ESTILO PREMIUM SaaS) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="cog-6-tooth" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Perfil do Negócio</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Master Config</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Identidade corporativa, parâmetros fiscais e <span class="text-brand-600 font-bold uppercase">Saúde Operacional</span></p>
                </div>
            </div>

            <flux:button wire:click="save" variant="primary" icon="check" class="rounded-2xl px-8 h-14 font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 w-full md:w-auto">
                Guardar Alterações
            </flux:button>
        </header>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-8 space-y-8">
            {{-- 2. IDENTIDADE VISUAL & MERCADO --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm relative overflow-hidden group">
                <div class="flex items-center gap-3 mb-10">
                    <div class="p-2 bg-brand-500/10 rounded-xl text-brand-600">
                        <flux:icon name="identification" variant="outline" class="size-5" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Branding & Presença no Mercado</h3>
                </div>

                <div class="flex flex-col md:flex-row items-start gap-12">
                    {{-- UPLOAD DE LOGO PROFISSIONAL --}}
                    <div class="relative group/logo">
                        <div class="size-48 rounded-[3rem] overflow-hidden border-8 border-zinc-50 dark:border-zinc-950 shadow-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center transition-all group-hover/logo:scale-105 duration-500">
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ $workspace->logo_url }}" class="w-full h-full object-cover">
                            @endif

                            {{-- Overlay de Loading --}}
                            <div wire:loading wire:target="logo" class="absolute inset-0 bg-zinc-900/80 backdrop-blur-sm flex flex-col items-center justify-center gap-2">
                                <flux:icon name="arrow-path" class="text-brand-500 animate-spin size-8" />
                                <span class="text-[8px] font-black text-white uppercase tracking-widest">A carregar...</span>
                            </div>
                        </div>

                        <label for="logo-upload" class="absolute -bottom-2 -right-2 flex size-14 items-center justify-center bg-zinc-900 dark:bg-brand-600 text-white rounded-2xl cursor-pointer hover:scale-110 transition-all shadow-xl border-4 border-white dark:border-zinc-900">
                            <flux:icon name="camera" class="w-6 h-6" />
                            <input type="file" id="logo-upload" wire:model="logo" class="hidden" accept="image/*">
                        </label>
                    </div>

                    <div class="flex-1 w-full space-y-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Nome da Marca / Comercial</flux:label>
                            <flux:input wire:model="name" placeholder="Ex: Finance Pro IA" class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Indústria / Setor de Atuação</flux:label>
                            <flux:input wire:model="industry" placeholder="Ex: Tecnologia, Consultoria, Restauração..." class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. INFORMAÇÃO LEGAL & FISCAL --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm group">
                <div class="flex items-center gap-3 mb-10">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="document-check" variant="outline" class="size-5" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Dados de Conformidade & Audit</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Razão Social (Nome Fiscal)</flux:label>
                        <flux:input wire:model="legal_name" placeholder="Ex: Empresa Exemplo, S.A." class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">NIF / Número Contribuinte</flux:label>
                        <flux:input
                            wire:model="tax_number"
                            placeholder="500 000 000"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Email de Faturação</flux:label>
                        <flux:input wire:model="business_email" icon="envelope" placeholder="financeiro@empresa.com" class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Capital Social Registado (€)</flux:label>
                        <flux:input
                            wire:model="initial_capital"
                            type="number"
                            icon="banknotes"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                </div>

                <div class="mt-8 space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Sede / Morada Completa</flux:label>
                    <flux:textarea
                        wire:model="address"
                        rows="3"
                        placeholder="Introduz a morada oficial para registo em faturas..."
                        class="rounded-[1.8rem] shadow-inner border-none !bg-zinc-50 dark:!bg-zinc-950 p-6 text-sm"
                    />
                </div>
            </div>
        </div>

        {{-- 4. COLUNA DE SAÚDE OPERACIONAL (LATERAL) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- Card: Business Runway (Black Glass) --}}
            <div class="stat-card bg-zinc-950 text-white p-10 rounded-[3rem] shadow-2xl border border-zinc-800 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-3xl rounded-full group-hover:scale-125 transition-transform duration-1000"></div>

                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-brand-400 mb-4">Business Runway</p>
                    <h3 class="text-6xl font-black italic tracking-tighter leading-none mb-8 group-hover:scale-105 transition-transform duration-500">
                        {{ $runway }}
                    </h3>

                    <div class="p-5 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-md">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black uppercase text-zinc-500">Liquidez Atual</span>
                            <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        </div>
                        <p class="text-2xl font-black text-emerald-400 tracking-tighter">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($cash, 2, ',', ' ') }} €
                            </span>
                        </p>
                    </div>
                </div>
                <flux:icon name="bolt" class="absolute -right-6 -bottom-6 size-32 text-white/5 -rotate-12" />
            </div>

            {{-- Card: Burn Rate --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm group">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Custo Operacional (Burn Rate)</p>
                        <p class="text-3xl font-black dark:text-white tracking-tighter italic">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($burnRate, 2, ',', ' ') }} €
                            </span>
                            <span class="text-xs text-zinc-500 font-bold not-italic ml-1">/ MÊS</span>
                        </p>
                    </div>

                    <div class="space-y-3">
                        @php $health = $burnRate > 0 ? min(100, ($cash / ($burnRate * 6)) * 100) : 100; @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase text-zinc-500 tracking-widest italic">Saúde da Reserva (6 Meses)</span>
                            <span class="text-[10px] font-black {{ $health < 30 ? 'text-red-500' : 'text-brand-500' }}">{{ round($health) }}%</span>
                        </div>
                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800">
                            <div class="h-full transition-all duration-1000 ease-out {{ $health < 30 ? 'bg-red-500 shadow-[0_0_10px_red]' : 'bg-brand-500 shadow-[0_0_10px_blue]' }}"
                                 style="width: {{ $health }}%"></div>
                        </div>
                    </div>

                    <p class="text-[10px] text-zinc-400 font-medium italic leading-relaxed">
                        A reserva é calculada com base na tua liquidez total dividida pelo custo fixo médio dos últimos meses.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- RODAPÉ DE SISTEMA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ $workspace->name }} · Protocolo Master de Configuração
        </p>
    </footer>
</div>

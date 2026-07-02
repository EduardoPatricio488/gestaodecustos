<div class="space-y-10 pb-24" x-data="{ privacyMode: true }">

    {{-- 1. HEADER DE CONFIGURAÇÃO (VERSÃO DIAMANTE) --}}
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
                        <h1 class="text-4xl font-black uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">
                            Perfil do Negócio
                        </h1>
                        <flux:badge variant="neutral"
                            class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">
                            Versão Diamante
                        </flux:badge>
                    </div>

                    <p class="text-sm text-zinc-500 font-medium italic mt-2">
                        Identidade corporativa, parâmetros fiscais,
                        <span class="text-brand-600 font-bold uppercase">Saúde Operacional</span>
                        e
                        <span class="text-amber-500 font-bold uppercase">Conformidade Legal</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <button
                    type="button"
                    x-on:click="privacyMode = !privacyMode"
                    class="rounded-xl p-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
                >
                    <template x-if="privacyMode">
                        <flux:icon name="eye-slash" class="size-5" />
                    </template>
                    <template x-if="!privacyMode">
                        <flux:icon name="eye" class="size-5" />
                    </template>
                </button>

                <flux:button wire:click="save" variant="primary" icon="check"
                    class="rounded-2xl px-8 h-14 font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 w-full md:w-auto">
                    Guardar Alterações
                </flux:button>
            </div>
        </header>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- COLUNA PRINCIPAL (IDENTIDADE + LEGAL) --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- 2. BRANDING & MERCADO --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm relative overflow-hidden group">

                <div class="flex items-center gap-3 mb-10">
                    <div class="p-2 bg-brand-500/10 rounded-xl text-brand-600">
                        <flux:icon name="identification" variant="outline" class="size-5" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
                        Branding & Presença no Mercado
                    </h3>
                </div>

                <div class="flex flex-col md:flex-row items-start gap-12">

                    {{-- LOGO --}}
                    <div class="relative group/logo">
                        <div class="size-48 rounded-[3rem] overflow-hidden border-8 border-zinc-50 dark:border-zinc-950 shadow-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center transition-all group-hover/logo:scale-105 duration-500">

                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ $workspace->logo_url }}" class="w-full h-full object-cover">
                            @endif

                            <div wire:loading wire:target="logo"
                                class="absolute inset-0 bg-zinc-900/80 backdrop-blur-sm flex flex-col items-center justify-center gap-2">
                                <flux:icon name="arrow-path" class="text-brand-500 animate-spin size-8" />
                                <span class="text-[8px] font-black text-white uppercase tracking-widest">A carregar...</span>
                            </div>
                        </div>

                        <label for="logo-upload"
                            class="absolute -bottom-2 -right-2 flex size-14 items-center justify-center bg-zinc-900 dark:bg-brand-600 text-white rounded-2xl cursor-pointer hover:scale-110 transition-all shadow-xl border-4 border-white dark:border-zinc-900">
                            <flux:icon name="camera" class="w-6 h-6" />
                            <input type="file" id="logo-upload" wire:model="logo" class="hidden" accept="image/*">
                        </label>
                    </div>

                    {{-- CAMPOS PRINCIPAIS --}}
                    <div class="flex-1 w-full space-y-6">

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                                Nome da Marca / Comercial
                            </flux:label>
                            <flux:input wire:model="name"
                                placeholder="Ex: NexarTech Solutions"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                                Indústria / Setor de Atuação
                            </flux:label>
                            <flux:input wire:model="industry"
                                placeholder="Ex: Tecnologia & Automação Empresarial"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                        </div>

                    </div>
                </div>
            </div>

            {{-- 3. DADOS DE CONFORMIDADE & AUDIT --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm group">

                <div class="flex items-center gap-3 mb-10">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="document-check" variant="outline" class="size-5" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
                        Dados de Conformidade & Audit
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                            Razão Social (Nome Fiscal)
                        </flux:label>
                        <flux:input wire:model="legal_name"
                            placeholder="Ex: NexarTech Solutions Lda"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                            NIF / Número Contribuinte
                        </flux:label>
                        <flux:input wire:model="tax_number"
                            placeholder="509 882 314"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                            Email de Faturação
                        </flux:label>
                        <flux:input wire:model="business_email"
                            icon="envelope"
                            placeholder="finance@nexartech.com"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                            Capital Social Registado (€)
                        </flux:label>
                        <flux:input wire:model="initial_capital"
                            type="number"
                            icon="banknotes"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-2xl h-14 shadow-inner" />
                    </div>

                </div>

                <div class="mt-8 space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">
                        Sede / Morada Completa
                    </flux:label>
                    <flux:textarea wire:model="address"
                        rows="3"
                        placeholder="Rua de Lisboa nº67, 1000-000 Lisboa"
                        class="rounded-[1.8rem] shadow-inner border-none !bg-zinc-50 dark:!bg-zinc-950 p-6 text-sm" />
                </div>
            </div>
        </div>

        {{-- COLUNA LATERAL DIAMANTE --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- BUSINESS RUNWAY --}}
            <div class="stat-card bg-zinc-950 text-white p-10 rounded-[3rem] shadow-2xl border border-zinc-800 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-3xl rounded-full group-hover:scale-125 transition-transform duration-1000"></div>

                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-brand-400 mb-4">
                        Business Runway
                    </p>

                    <h3 class="text-6xl font-black italic tracking-tighter leading-none mb-8 group-hover:scale-105 transition-transform duration-500">
                        {{ $runway }}
                    </h3>

                    <div class="p-5 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-md">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black uppercase text-zinc-500">Liquidez Atual</span>
                            <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        </div>

                        <p class="text-2xl font-black text-emerald-400 tracking-tighter">
                            <span :class="privacyMode ? 'blur-md select-none' : ''"
                                class="transition-all duration-500 inline-block">
                                {{ number_format($cash, 2, ',', ' ') }} €
                            </span>
                        </p>
                    </div>
                </div>

                <flux:icon name="bolt" class="absolute -right-6 -bottom-6 size-32 text-white/5 -rotate-12" />
            </div>

            {{-- BURN RATE + SAÚDE DA RESERVA --}}
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm group">

                <div class="space-y-6">

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">
                            Custo Operacional (Burn Rate)
                        </p>

                        <p class="text-3xl font-black dark:text-white tracking-tighter italic">
                            <span :class="privacyMode ? 'blur-md select-none' : ''"
                                class="transition-all duration-500 inline-block">
                                {{ number_format($burnRate, 2, ',', ' ') }} €
                            </span>
                            <span class="text-xs text-zinc-500 font-bold not-italic ml-1">/ MÊS</span>
                        </p>
                    </div>

                    @php
                        $health = $burnRate > 0 ? min(100, ($cash / ($burnRate * 6)) * 100) : 100;
                    @endphp

                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black uppercase text-zinc-500 tracking-widest italic">
                                Saúde da Reserva (6 Meses)
                            </span>
                            <span class="text-[10px] font-black {{ $health < 30 ? 'text-red-500' : 'text-brand-500' }}">
                                {{ round($health) }}%
                            </span>
                        </div>

                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800">
                            <div class="h-full transition-all duration-1000 ease-out
                                {{ $health < 30 ? 'bg-red-500 shadow-[0_0_10px_red]' : 'bg-brand-500 shadow-[0_0_10px_blue]' }}"
                                style="width: {{ $health }}%">
                            </div>
                        </div>
                    </div>

                    <p class="text-[10px] text-zinc-400 font-medium italic leading-relaxed">
                        A reserva é calculada com base na tua liquidez total dividida pelo custo fixo médio dos últimos meses.
                    </p>
                </div>
            </div>

            {{-- ESTADO LEGAL & FISCAL --}}
            <div class="p-8 bg-zinc-950 border border-zinc-800 rounded-[2.5rem] shadow-xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400">
                        <flux:icon name="shield-check" class="size-4" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">
                        Estado Legal & Fiscal
                    </h3>
                </div>

                @php
                    $hasLegal = filled($legal_name) && filled($tax_number);
                    $legalStatus = $hasLegal ? 'Regularizado' : 'Incompleto';
                @endphp

                <ul class="space-y-2 text-sm text-zinc-300">
                    <li>• Situação Legal: {{ $legalStatus }}</li>
                    <li>• Razão Social: {{ $legal_name ?: 'Não definido' }}</li>
                    <li>• NIF: {{ $tax_number ?: 'Não definido' }}</li>
                    <li>• Email Fiscal: {{ $business_email ?: 'Não definido' }}</li>
                    <li>• Capital Social: {{ number_format($initial_capital ?? 0, 2, ',', ' ') }} €</li>
                </ul>

                <div class="flex items-center justify-between mt-4">
                    <span class="text-[9px] font-black uppercase text-zinc-500 tracking-[0.3em]">
                        Nível de Conformidade
                    </span>
                    <flux:badge
                        variant="{{ $hasLegal ? 'success' : 'warning' }}"
                        size="sm"
                        class="uppercase font-black text-[9px] px-3 border-none"
                    >
                        {{ $hasLegal ? 'Alto' : 'A Rever' }}
                    </flux:badge>
                </div>
            </div>

            {{-- INDICADORES DE RISCO --}}
            @php
                $liquidityRisk = $cash < 0 ? 'Alto' : ($cash < ($burnRate * 2) ? 'Moderado' : 'Baixo');
                $operationalRisk = $burnRate > 0 && $health < 30 ? 'Alto' : ($health < 60 ? 'Moderado' : 'Baixo');
            @endphp

            <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-500/10 rounded-lg text-red-500">
                        <flux:icon name="exclamation-triangle" class="size-4" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
                        Indicadores de Risco
                    </h3>
                </div>

                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li>• Risco de Liquidez: {{ $liquidityRisk }}</li>
                    <li>• Risco Operacional (Burn Rate): {{ $operationalRisk }}</li>
                    <li>• Dependência de Reserva: {{ $health < 30 ? 'Crítica' : ($health < 60 ? 'Sensível' : 'Estável') }}</li>
                </ul>

                <p class="text-[10px] text-zinc-400 font-medium italic leading-relaxed">
                    Estes indicadores são calculados com base na liquidez atual, custo operacional e capacidade de reserva
                    para 6 meses de operação contínua.
                </p>
            </div>

            {{-- DOCUMENTOS OFICIAIS (PLACEHOLDER) --}}
            <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="folder-open" class="size-4" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
                        Documentos Oficiais
                    </h3>
                </div>

                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 italic">
                    Em breve poderás anexar certidões, estatutos, contratos sociais e outros documentos críticos
                    para auditoria e compliance diretamente neste painel.
                </p>
            </div>
        </div>
    </div>
{{-- 4. ZONA DE PERIGO (CRITICAL ACTIONS) --}}
    <div class="mt-20 pt-10 border-t border-red-500/20">
        <div class="glass-card p-8 bg-red-500/[0.02] dark:bg-red-500/[0.05] border border-red-500/20 rounded-[2.5rem] relative overflow-hidden group">

            <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10 text-left">
                <div class="flex items-center gap-6">
                    <div class="size-16 rounded-2xl bg-red-500/10 flex items-center justify-center text-red-600 shadow-inner">
                        <flux:icon name="exclamation-triangle" variant="outline" class="size-8" />
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-red-600 dark:text-red-500 uppercase italic tracking-tighter leading-none">Zona de Perigo</h3>
                        <p class="text-xs text-zinc-500 font-medium mt-2 leading-relaxed">
                            Ações irreversíveis relacionadas com o teu vínculo à <span class="font-bold">{{ $workspace->name }}</span>.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 w-full md:w-auto">
                    @if(auth()->user()->isOwner())
                        {{-- OPÇÃO PARA O CEO --}}
                        <flux:button
                            wire:click="deleteCompany"
                            wire:confirm="ATENÇÃO: Esta ação vai apagar TODOS os dados da empresa, incluindo faturação, colaboradores e projetos. Esta ação é irreversível. Tens a certeza?"
                            variant="ghost"
                            class="flex-1 md:flex-none h-14 px-8 rounded-2xl font-black uppercase text-[10px] tracking-widest text-red-600 hover:bg-red-600 hover:text-white transition-all border border-red-500/20"
                        >
                            Apagar Empresa Permanentemente
                        </flux:button>
                    @else
                        {{-- OPÇÃO PARA O COLABORADOR --}}
                        <flux:button
                            wire:click="leaveCompany"
                            wire:confirm="Queres mesmo abandonar a equipa desta empresa? Perderás acesso imediato ao terminal."
                            variant="ghost"
                            class="flex-1 md:flex-none h-14 px-8 rounded-2xl font-black uppercase text-[10px] tracking-widest text-red-600 hover:bg-red-600 hover:text-white transition-all border border-red-500/20"
                        >
                            Abandonar Equipa / Sair
                        </flux:button>
                    @endif
                </div>
            </div>

            {{-- Detalhe visual de fundo --}}
            <div class="absolute -right-10 -bottom-10 size-40 bg-red-600/5 blur-3xl rounded-full"></div>
        </div>
    </div>
    {{-- FOOTER --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ $workspace->name }} · Perfil Empresarial Versão Diamante
        </p>
    </footer>
</div>

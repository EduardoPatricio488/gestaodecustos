<div class="space-y-10 pb-24" x-data="{ privacyMode: true }">
    {{-- 1. HEADER FISCAL (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-amber-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-amber-500/20 blur-2xl rounded-full group-hover:bg-amber-500/40 transition-all duration-700 shadow-amber-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="receipt-percent" class="w-10 h-10 text-amber-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">
                            Impostos & Obrigações
                        </h1>
                        <flux:badge variant="neutral" class="bg-amber-500/10 text-amber-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">
                            Fiscalidade Ativa
                        </flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">
                        Previsão de fluxos tributários e
                        <span class="text-amber-600 font-bold uppercase tracking-tighter">Reserva de Capital</span>
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

                <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl font-bold">
                    Painel Business
                </flux:button>
            </div>
        </header>
    </div>

    {{-- 2. CENTRO DE PROVISÃO (BLACK VAULT) --}}
    <div class="stat-card bg-zinc-950 text-white p-10 rounded-[3rem] shadow-2xl relative overflow-hidden border border-zinc-800 group transition-all hover:scale-[1.01]">
        <div class="absolute -right-20 -top-20 size-80 bg-amber-500/10 blur-[120px] rounded-full group-hover:bg-amber-500/20 transition-all duration-1000"></div>

        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-12">
            <div class="space-y-6 flex-1 text-center lg:text-left">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-amber-500 mb-2">
                        Total de Provisão Recomendada
                    </p>
                    <h2 class="text-6xl sm:text-7xl font-black tracking-tighter italic leading-none text-white">
                        <span
                            :class="privacyMode ? 'blur-xl select-none' : ''"
                            class="transition-all duration-700 inline-block"
                        >
                            {{ number_format($totalTaxDebt, 2, ',', ' ') }}
                        </span>
                        <span class="text-3xl sm:text-4xl">€</span>
                    </h2>
                </div>
                <div class="p-5 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-md max-w-xl mx-auto lg:mx-0">
                    <p class="text-sm font-medium text-zinc-400 leading-relaxed">
                        Montante estratégico a manter em <b>liquidez imediata</b> para cobrir IVA, TSU,
                        provisão de IRC, derrama municipal e retenções na fonte (IRS) do período corrente.
                    </p>
                </div>
            </div>

            <div class="hidden lg:flex flex-col items-center gap-4">
                <div class="relative">
                    <div class="p-8 bg-zinc-900 border border-zinc-800 rounded-[2.5rem] shadow-2xl">
                        <flux:icon name="shield-check" class="size-20 text-amber-500/50" />
                    </div>
                    <div class="absolute -bottom-2 -right-2 size-8 bg-emerald-500 rounded-full border-4 border-zinc-950 flex items-center justify-center shadow-lg">
                        <flux:icon name="check" variant="micro" class="size-4 text-white" />
                    </div>
                </div>
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-600">
                    Audit Ready
                </p>
            </div>
        </div>

        <flux:icon name="receipt-percent" class="absolute -left-10 -bottom-10 size-48 text-white/5 -rotate-12" />
    </div>

    {{-- 3. GRID PRINCIPAL DE OBRIGAÇÕES --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- CONTA CORRENTE DE IVA --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:border-brand-500/30 transition-all">
            <div>
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                        Conta Corrente de IVA
                    </h3>
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="arrows-right-left" variant="outline" class="size-4" />
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="flex justify-between items-center group/row">
                        <div class="flex items-center gap-2">
                            <div class="size-1.5 rounded-full bg-red-500"></div>
                            <span class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-tight">
                                IVA Liquidado
                            </span>
                        </div>
                        <span
                            :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="text-sm font-black text-red-500 transition-all duration-500"
                        >
                            +{{ number_format($vatCollected, 2, ',', ' ') }}€
                        </span>
                    </div>

                    <div class="flex justify-between items-center group/row">
                        <div class="flex items-center gap-2">
                            <div class="size-1.5 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-tight">
                                IVA Dedutível
                            </span>
                        </div>
                        <span
                            :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="text-sm font-black text-emerald-600 transition-all duration-500"
                        >
                            -{{ number_format($vatDeductible, 2, ',', ' ') }}€
                        </span>
                    </div>

                    <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-end">
                        <div>
                            <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                                Saldo devedor
                            </p>
                            <p class="text-[10px] font-black dark:text-white uppercase mt-1">
                                IVA a {{ $vatNet >= 0 ? 'Pagar' : 'Recuperar' }}
                            </p>
                        </div>
                        <span
                            :class="privacyMode ? 'blur-md select-none' : ''"
                            class="text-2xl font-black dark:text-white tracking-tighter italic transition-all duration-500"
                        >
                            {{ number_format($vatNet, 2, ',', ' ') }} €
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SEGURANÇA SOCIAL (TSU) --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:border-amber-500/30 transition-all">
            <div>
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                        Segurança Social (TSU)
                    </h3>
                    <div class="p-2 bg-amber-500/10 rounded-lg text-amber-600">
                        <flux:icon name="users" variant="outline" class="size-4" />
                    </div>
                </div>

                <div>
                    <p class="text-4xl font-black text-amber-600 tracking-tighter italic leading-none">
                        <span
                            :class="privacyMode ? 'blur-md select-none' : ''"
                            class="transition-all duration-500 inline-block"
                        >
                            {{ number_format($tsuEstimate, 2, ',', ' ') }}
                        </span> €
                    </p>
                    <p class="text-[9px] text-zinc-500 font-bold uppercase mt-4 tracking-widest leading-relaxed">
                        Estimativa de 23,75% sobre o <br> volume total da folha salarial ativa.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">
                    Prazo Limite
                </span>
                <flux:badge variant="warning" size="sm" class="uppercase font-black text-[9px] px-3 border-none bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                    Até dia 20
                </flux:badge>
            </div>
        </div>

        {{-- PROVISÃO DE IRC --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:border-brand-500/30 transition-all">
            <div>
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                        Provisão de IRC
                    </h3>
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <flux:icon name="presentation-chart-bar" variant="outline" class="size-4" />
                    </div>
                </div>

                <div>
                    <p class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">
                        <span
                            :class="privacyMode ? 'blur-md select-none' : ''"
                            class="transition-all duration-500 inline-block"
                        >
                            {{ number_format($ircProvision, 2, ',', ' ') }}
                        </span> €
                    </p>
                    <p class="text-[9px] text-zinc-500 font-bold uppercase mt-4 tracking-widest leading-relaxed">
                        Cálculo prudencial de 21% sobre <br> o lucro tributável estimado.
                    </p>
                </div>
            </div>

            <div class="mt-8 space-y-3">
                <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                    <p class="text-[8px] font-black text-zinc-500 uppercase leading-none text-center">
                        Reserva para encerramento de exercício
                    </p>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100/60 dark:border-amber-700/40">
                    <p class="text-[8px] font-black text-amber-700 dark:text-amber-300 uppercase leading-none text-center">
                        Considerar derrama municipal e ajustamentos fiscais
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. SECÇÃO EXTRA: IRS & DERRAMA / ESTADO FISCAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
        {{-- IRS RETIDO NA FONTE --}}
        <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                        Retenções na Fonte (IRS)
                    </h3>
                    <div class="p-2 bg-red-500/10 rounded-lg text-red-500">
                        <flux:icon name="banknotes" class="size-4" />
                    </div>
                </div>

                <p class="text-3xl font-black text-red-500 tracking-tighter italic leading-none">
                    <span
                        :class="privacyMode ? 'blur-md select-none' : ''"
                        class="transition-all duration-500 inline-block"
                    >
                        {{ number_format($irsWithheld, 2, ',', ' ') }}
                    </span> €
                </p>
                <p class="text-[9px] text-zinc-500 font-bold uppercase mt-4 tracking-widest leading-relaxed">
                    Montante estimado de retenções na fonte a entregar à Autoridade Tributária.
                </p>
            </div>
        </div>

        {{-- DERRAMA MUNICIPAL --}}
        <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">
                        Derrama Municipal
                    </h3>
                    <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                        <flux:icon name="building-office" class="size-4" />
                    </div>
                </div>

                <p class="text-3xl font-black text-blue-500 tracking-tighter italic leading-none">
                    <span
                        :class="privacyMode ? 'blur-md select-none' : ''"
                        class="transition-all duration-500 inline-block"
                    >
                        {{ number_format($derrama, 2, ',', ' ') }}
                    </span> €
                </p>
                <p class="text-[9px] text-zinc-500 font-bold uppercase mt-4 tracking-widest leading-relaxed">
                    Estimativa de 1,5% sobre o lucro tributável, sujeita a regras municipais específicas.
                </p>
            </div>
        </div>

        {{-- ESTADO FISCAL DO WORKSPACE --}}
        <div class="p-8 bg-zinc-950 border border-zinc-800 rounded-[2.5rem] shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 mb-4">
                    Estado Fiscal do Workspace
                </h3>

                <ul class="space-y-2 text-sm text-zinc-300">
                    <li>• IVA: {{ $vatNet >= 0 ? 'A Pagar' : 'A Recuperar' }}</li>
                    <li>• TSU: {{ number_format($tsuEstimate, 2, ',', ' ') }} €</li>
                    <li>• IRC: {{ number_format($ircProvision, 2, ',', ' ') }} €</li>
                    <li>• Derrama: {{ number_format($derrama, 2, ',', ' ') }} €</li>
                    <li>• IRS Retido: {{ number_format($irsWithheld, 2, ',', ' ') }} €</li>
                </ul>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <span class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.3em]">
                    Nível de Exposição
                </span>
                @php
                    $riskScore = $totalTaxDebt > 0 ? 'Moderado' : 'Baixo';
                @endphp
                <flux:badge
                    variant="{{ $riskScore === 'Baixo' ? 'success' : 'warning' }}"
                    size="sm"
                    class="uppercase font-black text-[9px] px-3 border-none"
                >
                    {{ $riskScore }}
                </flux:badge>
            </div>
        </div>
    </div>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ auth()->user()->currentWorkspace->name }} · Protocolo de Gestão Tributária
        </p>
    </footer>
</div>

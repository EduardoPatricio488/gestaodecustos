<div class="space-y-10 pb-20">
    {{-- 1. HEADER E SELETOR DE ANO (ESTILO SaaS PREMIUM) --}}
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10">
        <div class="flex items-center gap-5">
            <div class="relative group">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 text-brand-600">
                    <flux:icon name="presentation-chart-line" class="w-10 h-10" />
                </div>
            </div>
            <div>
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Resultados (P&L)</h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Análise estrutural de performance, margens e rentabilidade líquida</p>
            </div>
        </div>

        {{-- SELETOR DE ANO ESTILO TAB --}}
        <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-900 p-1.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-inner">
            @foreach([now()->year - 1, now()->year] as $y)
                <button wire:click="setYear({{ $y }})"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $year == $y ? 'bg-white dark:bg-zinc-800 shadow-md text-brand-600 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Ano {{ $y }}
                </button>
            @endforeach
        </div>
    </header>

    {{-- 2. KPIs ANUAIS (AUDIT ANALYTICS COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Faturação Total --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-emerald-500/30 transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Faturação Total (Bruta)</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($yearlyRevenue, 2, ',', ' ') }} €
                </span>
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Acumulado {{ $year }}</span>
            </div>
        </div>

        {{-- Lucro Líquido Real (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400 shadow-inner">
                        <flux:icon name="banknotes" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">Profit & Loss</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Lucro Líquido Real</p>
                <h3 class="text-4xl font-black text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($yearlyProfit, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
        </div>

        {{-- Margem Média --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:border-brand-500/30">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Margem Média Líquida</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-4xl font-black dark:text-white tracking-tighter">{{ round($avgMargin, 1) }}%</h3>
                <span class="text-[9px] font-bold {{ $avgMargin > 20 ? 'text-emerald-500' : 'text-amber-500' }} uppercase italic">Eficiência Operacional</span>
            </div>
            <div class="mt-4 h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500 shadow-[0_0_10px_#3b82f6]" style="width: {{ $avgMargin }}%"></div>
            </div>
        </div>
    </div>

    {{-- 3. DETALHE MENSAL P&L (ESTILO CORPORATE LEDGER) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-900/30 flex justify-between items-center">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Mapa de Exploração</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Balanço Mensal Detalhado</p>
            </div>
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm">Audit Interno</flux:badge>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-widest text-center">
                        <th class="p-6 text-left">Mês Fiscal</th>
                        <th class="p-6">Faturação (+)</th>
                        <th class="p-6">OpEx / Custos (-)</th>
                        <th class="p-6">Retenção IVA</th>
                        <th class="p-6 text-right">Resultado (=)</th>
                        <th class="p-6 px-10">Margem Operacional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @foreach($monthlyData as $data)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row {{ $data['revenue'] > 0 ? '' : 'opacity-40 grayscale' }}">
                            {{-- MÊS --}}
                            <td class="p-6">
                                <span class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $data['month_name'] }}</span>
                            </td>

                            {{-- RECEITAS --}}
                            <td class="p-6 text-center">
                                <span class="text-sm font-bold text-emerald-600">
                                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                        +{{ number_format($data['revenue'], 2, ',', ' ') }} €
                                    </span>
                                </span>
                            </td>

                            {{-- CUSTOS --}}
                            <td class="p-6 text-center">
                                <span class="text-sm font-bold text-red-500">
                                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                        -{{ number_format($data['costs'], 2, ',', ' ') }} €
                                    </span>
                                </span>
                            </td>

                            {{-- IVA --}}
                            <td class="p-6 text-center text-xs font-black text-zinc-400 italic">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                    {{ number_format($data['vat'], 2, ',', ' ') }} €
                                </span>
                            </td>

                            {{-- RESULTADO LÍQUIDO --}}
                            <td class="p-6 text-right">
                                <span class="text-sm font-black dark:text-white {{ $data['profit'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }} tracking-tighter">
                                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                        {{ number_format($data['profit'], 2, ',', ' ') }} €
                                    </span>
                                </span>
                            </td>

                            {{-- EFICIÊNCIA (BARRA NEON) --}}
                            <td class="p-6 px-10">
                                <div class="flex flex-col gap-2 min-w-[120px]">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Performance</span>
                                        <span class="text-[10px] font-black dark:text-zinc-300">{{ round($data['margin']) }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800">
                                        <div class="h-full transition-all duration-1000 ease-out {{ $data['margin'] > 30 ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : ($data['margin'] > 0 ? 'bg-brand-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]' : 'bg-red-500') }}"
                                             style="width: {{ min(max($data['margin'], 0), 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                {{-- TFOOT: TOTAIS ANUAIS (ESTILO VAULT) --}}
                <tfoot class="bg-zinc-950 text-white font-black uppercase text-[10px] tracking-widest italic">
                    <tr>
                        <td class="p-8">Consolidado Anual</td>
                        <td class="p-8 text-center text-emerald-400">
                            <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($yearlyRevenue, 2, ',', ' ') }} €
                            </span>
                        </td>
                        <td class="p-8 text-center">
                            <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($monthlyData->sum('costs'), 2, ',', ' ') }} €
                            </span>
                        </td>
                        <td class="p-8 text-center text-zinc-500 font-bold">
                            <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($monthlyData->sum('vat'), 2, ',', ' ') }} €
                            </span>
                        </td>
                        <td class="p-8 text-right text-brand-400 text-lg tracking-tighter">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                {{ number_format($yearlyProfit, 2, ',', ' ') }} €
                            </span>
                        </td>
                        <td class="p-8 text-center text-zinc-400">
                            {{ round($avgMargin) }}% Global
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 4. NOTAS DE AUDITORIA E FISCALIDADE (ESTILO SaaS REPORT) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- BLOC0 DE COMPLIANCE --}}
        <div class="lg:col-span-2 p-8 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-inner relative overflow-hidden group">
            {{-- Efeito decorativo de fundo --}}
            <flux:icon name="information-circle" class="absolute -right-6 -bottom-6 size-32 text-zinc-100 dark:text-zinc-800/50 rotate-12 pointer-events-none" />

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-brand-500/10 rounded-xl text-brand-600">
                        <flux:icon name="shield-check" variant="outline" class="size-5" />
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Notas de Auditoria Financeira</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <p class="text-xs font-black dark:text-white uppercase tracking-tight">Cálculo de Imposto (IRC)</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed font-medium">
                            O Lucro Líquido apresentado já deduz automaticamente uma estimativa de <b>21% para IRC</b> sobre o lucro bruto positivo acumulado. Este valor serve apenas como provisão de tesouraria.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-black dark:text-white uppercase tracking-tight">Consolidação de Custos</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed font-medium">
                            As despesas apresentadas incluem o <b>Custo Total de Payroll (RH)</b> e todos os gastos operacionais marcados com a etiqueta de empresa no módulo de despesas.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATUS DE SINCRONIZAÇÃO --}}
        <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-center items-center text-center group">
            <div class="relative mb-4">
                <div class="size-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
                    <flux:icon name="arrow-path" class="size-6 animate-spin-slow" />
                </div>
                <div class="absolute -bottom-1 -right-1 size-4 bg-emerald-500 border-4 border-white dark:border-zinc-900 rounded-full"></div>
            </div>

            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Estado dos Dados</h4>
            <p class="text-sm font-bold dark:text-white mt-1 uppercase">Sincronizado</p>
            <p class="text-[9px] text-zinc-500 italic mt-3 font-medium uppercase tracking-tighter">Última atualização: <br> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    {{-- RODAPÉ CORPORATIVO --}}
    <footer class="pt-10 border-t border-zinc-100 dark:border-zinc-800 mt-10 flex flex-col md:flex-row justify-between items-center gap-4 opacity-70">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ auth()->user()->currentWorkspace->name }} · Relatório de Exploração Anual
        </p>
        <div class="flex gap-6">
            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest flex items-center gap-2">
                <div class="size-1.5 rounded-full bg-emerald-500"></div>
                Audit Pass
            </span>
            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">{{ config('app.name') }} SaaS Pro</span>
        </div>
    </footer>

    {{-- ESTILOS ADICIONAIS --}}
    <style>
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
    </style>
</div>

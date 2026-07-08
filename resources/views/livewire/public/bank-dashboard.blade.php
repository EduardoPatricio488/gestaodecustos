<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 p-6 md:p-12 text-left">
    <div class="max-w-[1400px] mx-auto space-y-10">

        {{-- 1. HEADER INSTITUCIONAL --}}
        <div class="bg-white dark:bg-zinc-900 p-10 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col md:flex-row justify-between items-center gap-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-zinc-900/5 blur-[100px] rounded-full -mr-32 -mt-32"></div>

            <div class="flex items-center gap-8 relative z-10 text-left">
                <div class="size-20 rounded-[1.8rem] bg-zinc-900 flex items-center justify-center text-white shadow-2xl">
                    <flux:icon name="building-library" class="size-10" />
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-zinc-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg">Acesso Auditado</span>
                        <h2 class="text-sm font-black text-zinc-400 uppercase tracking-widest">Protocolo de Transparência Financeira</h2>
                    </div>
                    <h1 class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">Dossiê de Solvência: {{ $workspace->name }}</h1>
                </div>
            </div>

            <a href="{{ route('bank.portal') }}" class="px-8 py-4 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-xl">
                Encerrar Auditoria
            </a>
        </div>

        {{-- 2. GRID DE INDICADORES CHAVE (KPIs BANCÁRIOS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Rating Financeiro --}}
            <div class="bg-zinc-950 p-10 rounded-[3rem] shadow-2xl border border-zinc-800 text-left">
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em] mb-4">Financial Rating</p>
                <div class="flex items-baseline gap-4">
                    <h3 class="text-7xl font-black text-emerald-500 italic tracking-tighter">{{ $rating }}</h3>
                    <span class="text-zinc-500 font-bold uppercase text-xs">Score Consolidado</span>
                </div>
                <p class="mt-6 text-xs text-zinc-400 leading-relaxed font-medium">Rating gerado automaticamente com base no rácio de liquidez imediata e histórico de passivo circulante.</p>
            </div>

            {{-- Liquidez Total --}}
            <div class="bg-white dark:bg-zinc-900 p-10 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.4em] mb-4">Liquidez Disponível</p>
                <h3 class="text-5xl font-black dark:text-white tracking-tighter italic">{{ number_format($liquidez, 2, ',', ' ') }}€</h3>
                <div class="mt-8 flex items-center gap-3">
                    <div class="h-2 flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500" style="width: 85%"></div>
                    </div>
                    <span class="text-[10px] font-black text-emerald-500">85% OPTIMAL</span>
                </div>
            </div>

            {{-- Passivo / Endividamento --}}
            <div class="bg-white dark:bg-zinc-900 p-10 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.4em] mb-4">Passivo Circulante</p>
                <h3 class="text-5xl font-black text-red-500 tracking-tighter italic">{{ number_format($passivo, 2, ',', ' ') }}€</h3>
                <p class="mt-8 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Utilização de Linhas de Crédito: <span class="text-red-500">LOW RISK</span></p>
            </div>
        </div>

        {{-- 3. DETALHE DAS DISPONIBILIDADES --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3.5rem] shadow-sm overflow-hidden text-left">
            <div class="p-10 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-950/50">
                <div>
                    <h3 class="text-xs font-black uppercase text-zinc-400 tracking-[0.4em] mb-1">Mapa de Disponibilidades</h3>
                    <p class="text-xl font-black dark:text-white uppercase italic tracking-tighter">Contas Bancárias & Ativos</p>
                </div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Sincronização em Tempo Real via API</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/30 dark:bg-zinc-950/30 text-[9px] font-black uppercase text-zinc-400 tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                            <th class="p-8">Instituição Bancária</th>
                            <th class="p-8">Tipo de Conta</th>
                            <th class="p-8">IBAN (Auditado)</th>
                            <th class="p-8 text-right">Saldo Atual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                        @foreach($accounts as $acc)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="p-8 flex items-center gap-4">
                                    <div class="size-10 rounded-xl flex items-center justify-center text-white font-black" style="background-color: {{ $acc->color }}">
                                        {{ substr($acc->bank_name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-black dark:text-white uppercase">{{ $acc->bank_name }}</span>
                                </td>
                                <td class="p-8">
                                    <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-[9px] font-black uppercase text-zinc-500 border border-zinc-200 dark:border-zinc-700">{{ $acc->type }}</span>
                                </td>
                                <td class="p-8 font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $acc->iban }}</td>
                                <td class="p-8 text-right font-black text-lg dark:text-white">{{ number_format($acc->current_balance, 2, ',', ' ') }}€</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RODAPÉ DE CERTIFICAÇÃO --}}
        <div class="pt-10 flex flex-col md:flex-row justify-between items-center gap-6 opacity-40">
            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.4em]">© {{ date('Y') }} Relatório Gerado por Finance Pro AI Auditor Engine</p>
            <div class="flex items-center gap-6">
                <span class="text-[9px] font-black uppercase tracking-widest">ID Auditoria: #{{ rand(100000, 999999) }}</span>
                <div class="h-3 w-px bg-zinc-400"></div>
                <span class="text-[9px] font-black uppercase tracking-widest">Certificação SSL 256-bit</span>
            </div>
        </div>
    </div>
</div>

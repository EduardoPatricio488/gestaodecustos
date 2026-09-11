<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-white p-4 sm:p-6 lg:p-10">
    <div class="max-w-[1500px] mx-auto space-y-6">
        {{-- CABEÇALHO --}}
        <header class="relative overflow-hidden rounded-[2rem] bg-zinc-950 text-white shadow-2xl">
            <div class="absolute -right-24 -top-24 size-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
            <div class="relative p-6 sm:p-8 lg:p-10">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-7">
                    <div class="flex items-start gap-5">
                        <div class="size-14 sm:size-16 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center shrink-0">
                            <flux:icon name="building-library" class="size-7 sm:size-8 text-emerald-400" />
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/15 text-emerald-300 text-[9px] font-black uppercase tracking-widest border border-emerald-500/20">Acesso bancário autorizado</span>
                                <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest">Dossiê financeiro</span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight">{{ $workspace->legal_name ?: $workspace->name }}</h1>
                            <p class="mt-2 text-xs sm:text-sm text-zinc-400">Visão consolidada dos dados empresariais disponibilizados à instituição financeira.</p>
                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                                <span>NIF: {{ $workspace->tax_number ? implode(' ', str_split(preg_replace('/\D/', '', (string) $workspace->tax_number), 3)) : 'Não indicado' }}</span>
                                <span>{{ $workspace->industry ?: 'Atividade não indicada' }}</span>
                                <span>{{ strtoupper($workspace->currency ?: 'EUR') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                        <div class="rounded-2xl bg-white/5 border border-white/10 px-4 py-3">
                            <p class="text-[8px] uppercase tracking-widest text-zinc-500 font-black">Última autorização</p>
                            <p class="mt-1 text-xs font-bold text-white">{{ optional($lastBankAccessRequest?->responded_at)->format('d/m/Y H:i') ?: 'Sessão atual' }}</p>
                        </div>
                        <a href="{{ route('bank.portal') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-zinc-950 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-400 transition-all">
                            <flux:icon name="arrow-left-start-on-rectangle" class="size-4" />
                            Terminar acesso
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- KPIs --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="rounded-2xl bg-zinc-950 text-white p-6 shadow-lg">
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Rating interno</p>
                <div class="mt-3 flex items-end gap-3">
                    <span class="text-5xl font-black italic tracking-tighter text-emerald-400">{{ $rating }}</span>
                    <span class="text-[10px] text-zinc-500 font-bold pb-2">Liquidez + cobrança</span>
                </div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Liquidez</p>
                <p class="mt-3 text-3xl font-black tracking-tight">{{ number_format($liquidez, 2, ',', ' ') }}€</p>
                <p class="mt-1 text-[10px] text-zinc-400">Fundos disponíveis</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Recebimentos em aberto</p>
                <p class="mt-3 text-3xl font-black tracking-tight">{{ number_format($receivables, 2, ',', ' ') }}€</p>
                <p class="mt-1 text-[10px] {{ $overdueReceivables > 0 ? 'text-red-500' : 'text-emerald-500' }} font-bold">{{ number_format($overdueReceivables, 2, ',', ' ') }}€ vencidos</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Passivo bancário</p>
                <p class="mt-3 text-3xl font-black tracking-tight">{{ number_format($passivo, 2, ',', ' ') }}€</p>
                <p class="mt-1 text-[10px] text-zinc-400">Rácio {{ number_format($currentRatio, 2, ',', ' ') }}x</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Resultado do mês</p>
                <p class="mt-3 text-3xl font-black tracking-tight {{ $netPosition >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($netPosition, 2, ',', ' ') }}€</p>
                <p class="mt-1 text-[10px] text-zinc-400">Receita − despesas empresariais</p>
            </div>
        </section>

        {{-- PERFIL + RISCO --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 sm:p-7">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Perfil empresarial</p>
                        <h2 class="mt-1 text-xl font-black tracking-tight">Dados de identificação e escala</h2>
                    </div>
                    <flux:icon name="building-office-2" class="size-5 text-zinc-300" />
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-950 p-4"><p class="text-[8px] text-zinc-400 uppercase font-black">Colaboradores</p><p class="mt-2 text-xl font-black">{{ $employeeCount }}</p></div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-950 p-4"><p class="text-[8px] text-zinc-400 uppercase font-black">Clientes</p><p class="mt-2 text-xl font-black">{{ $clientCount }}</p></div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-950 p-4"><p class="text-[8px] text-zinc-400 uppercase font-black">Fornecedores</p><p class="mt-2 text-xl font-black">{{ $supplierCount }}</p></div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-950 p-4"><p class="text-[8px] text-zinc-400 uppercase font-black">Projetos</p><p class="mt-2 text-xl font-black">{{ $projectCount }}</p></div>
                </div>
                <div class="mt-5 grid md:grid-cols-2 gap-4 text-xs">
                    <div><span class="text-zinc-400 font-bold">Email empresarial</span><p class="mt-1 font-bold break-all">{{ $workspace->business_email ?: 'Não indicado' }}</p></div>
                    <div><span class="text-zinc-400 font-bold">Morada</span><p class="mt-1 font-bold">{{ $workspace->address ?: 'Não indicada' }}</p></div>
                </div>
            </div>

            <div class="rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 sm:p-7">
                <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest">Indicadores de risco</p>
                <h2 class="mt-1 text-xl font-black tracking-tight">Saúde financeira</h2>
                <div class="mt-6 space-y-5">
                    <div>
                        <div class="flex justify-between text-[10px] font-black uppercase"><span>Liquidez</span><span>{{ number_format($currentRatio, 2, ',', ' ') }}x</span></div>
                        <div class="mt-2 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, ($currentRatio / 3) * 100) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[10px] font-black uppercase"><span>Recebimentos vencidos</span><span>{{ number_format($collectionRisk, 1, ',', ' ') }}%</span></div>
                        <div class="mt-2 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden"><div class="h-full {{ $collectionRisk > 40 ? 'bg-red-500' : ($collectionRisk > 20 ? 'bg-amber-500' : 'bg-emerald-500') }} rounded-full" style="width: {{ min(100, $collectionRisk) }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[10px] font-black uppercase"><span>Exposição bancária</span><span>{{ number_format($debtRatio, 1, ',', ' ') }}%</span></div>
                        <div class="mt-2 h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden"><div class="h-full bg-zinc-700 dark:bg-zinc-400 rounded-full" style="width: {{ min(100, $debtRatio) }}%"></div></div>
                    </div>
                    <div class="pt-2 text-[10px] text-zinc-400 leading-relaxed">Indicadores calculados exclusivamente a partir dos dados financeiros autorizados nesta sessão.</div>
                </div>
            </div>
        </section>

        {{-- EVOLUÇÃO --}}
        <section class="rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 sm:p-7">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Análise temporal</p>
                    <h2 class="mt-1 text-xl font-black tracking-tight">Receita vs. despesas</h2>
                </div>
                <div class="flex items-center gap-1 p-1 rounded-xl bg-zinc-100 dark:bg-zinc-950">
                    @foreach(['3' => '3M', '6' => '6M', '12' => '12M'] as $value => $label)
                        <button type="button" wire:click="$set('period', '{{ $value }}')" class="px-3 py-2 rounded-lg text-[9px] font-black {{ $period === $value ? 'bg-white dark:bg-zinc-800 shadow-sm text-zinc-900 dark:text-white' : 'text-zinc-400' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <div class="mt-8 space-y-4">
                @foreach($monthlyTrend as $month)
                    <div class="grid grid-cols-[38px_1fr_90px] sm:grid-cols-[55px_1fr_110px] gap-3 items-center">
                        <span class="text-[9px] font-black uppercase text-zinc-400">{{ $month['label'] }}</span>
                        <div class="space-y-1.5">
                            <div class="h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, ($month['revenue'] / $trendMax) * 100) }}%"></div></div>
                            <div class="h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden"><div class="h-full bg-red-400 rounded-full" style="width: {{ min(100, ($month['expenses'] / $trendMax) * 100) }}%"></div></div>
                        </div>
                        <div class="text-right"><p class="text-[9px] font-black text-emerald-600">{{ number_format($month['revenue'], 0, ',', ' ') }}€</p><p class="text-[9px] font-bold text-red-500">{{ number_format($month['expenses'], 0, ',', ' ') }}€</p></div>
                    </div>
                @endforeach
                <div class="flex gap-5 pt-3 text-[9px] font-black uppercase tracking-widest text-zinc-400"><span><i class="inline-block size-2 rounded-full bg-emerald-500 mr-1"></i> Receita</span><span><i class="inline-block size-2 rounded-full bg-red-400 mr-1"></i> Despesas</span></div>
            </div>
        </section>

        {{-- CONTAS --}}
        <section class="rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-7 border-b border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div><p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">Exposição bancária</p><h2 class="mt-1 text-xl font-black">Contas e linhas de crédito</h2></div>
                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">{{ $accounts->count() }} contas registadas</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-zinc-50 dark:bg-zinc-950 text-[9px] font-black uppercase tracking-widest text-zinc-400"><tr><th class="px-6 py-4">Banco</th><th class="px-6 py-4">Conta</th><th class="px-6 py-4">IBAN</th><th class="px-6 py-4">Tipo</th><th class="px-6 py-4 text-right">Saldo</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($accounts as $account)
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/30">
                                <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="size-9 rounded-xl flex items-center justify-center text-white text-xs font-black" style="background-color: {{ $account->color ?: '#18181b' }}">{{ strtoupper(substr($account->bank_name ?: $account->name, 0, 1)) }}</div><span class="text-sm font-black">{{ $account->bank_name ?: 'Banco' }}</span></div></td>
                                <td class="px-6 py-4 text-xs font-bold text-zinc-500">{{ $account->name }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-zinc-500">{{ $account->iban ?: '—' }}</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-[8px] font-black uppercase">{{ $account->type }}</span></td>
                                <td class="px-6 py-4 text-right font-black">{{ number_format((float) $account->current_balance, 2, ',', ' ') }}€</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-xs text-zinc-400 font-semibold">Não existem contas bancárias registadas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- REGISTOS FINANCEIROS --}}
        <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800"><p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Receitas / faturação</p><h2 class="mt-1 text-lg font-black">Últimas faturas</h2></div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($recentInvoices as $invoice)
                        <div class="px-6 py-4 flex items-center justify-between gap-4"><div class="min-w-0"><p class="text-xs font-black truncate">{{ $invoice->invoice_number ?: 'Fatura' }} · {{ $invoice->client_name ?: 'Cliente não indicado' }}</p><p class="text-[9px] text-zinc-400 mt-1">{{ optional($invoice->created_at)->format('d/m/Y') }} · {{ ucfirst($invoice->status ?: '—') }}</p></div><span class="text-sm font-black shrink-0">{{ number_format((float) $invoice->total_amount, 2, ',', ' ') }}€</span></div>
                    @empty
                        <div class="p-8 text-center text-xs text-zinc-400">Sem faturas registadas.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800"><p class="text-[9px] font-black text-red-500 uppercase tracking-widest">Custos operacionais</p><h2 class="mt-1 text-lg font-black">Últimas despesas</h2></div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($recentExpenses as $expense)
                        <div class="px-6 py-4 flex items-center justify-between gap-4"><div class="min-w-0"><p class="text-xs font-black truncate">{{ $expense->description ?: 'Despesa empresarial' }}</p><p class="text-[9px] text-zinc-400 mt-1">{{ optional($expense->spent_at)->format('d/m/Y') }} · {{ $expense->category?->name ?: 'Sem categoria' }}</p></div><span class="text-sm font-black text-red-500 shrink-0">-{{ number_format((float) $expense->amount, 2, ',', ' ') }}€</span></div>
                    @empty
                        <div class="p-8 text-center text-xs text-zinc-400">Sem despesas empresariais registadas.</div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- CUSTOS DE PESSOAL + AUDITORIA --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6">
                <p class="text-[9px] font-black text-violet-600 uppercase tracking-widest">Estrutura de custos</p>
                <h2 class="mt-1 text-lg font-black">Pessoal</h2>
                <div class="mt-5 flex items-end justify-between"><div><p class="text-3xl font-black">{{ number_format($payroll, 2, ',', ' ') }}€</p><p class="text-[10px] text-zinc-400">Massa salarial mensal registada</p></div><span class="px-3 py-2 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 text-[9px] font-black">{{ $employeeCount }} colaboradores</span></div>
            </div>
            <div class="rounded-[1.75rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6">
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Controlo de acesso</p>
                <h2 class="mt-1 text-lg font-black">Auditoria bancária</h2>
                <div class="mt-5 flex items-center justify-between gap-4"><div><p class="text-sm font-black">Sessão autorizada</p><p class="text-[10px] text-zinc-400 mt-1">{{ $pendingBankRequests }} pedido(s) pendente(s) no lado empresarial.</p></div><span class="size-3 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/30"></span></div>
            </div>
        </section>

        <footer class="flex flex-col sm:flex-row justify-between gap-3 pt-3 pb-8 text-[8px] font-black uppercase tracking-[0.2em] text-zinc-400">
            <span>Finance Pro IA · Dossiê de dados autorizados</span>
            <span>Dados apresentados conforme autorização da empresa</span>
        </footer>
    </div>
</div>

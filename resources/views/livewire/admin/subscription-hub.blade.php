<div class="space-y-8 text-left pb-20">
    {{-- HEADER --}}
    <x-page-header title="💳 Gestão de Subscrições" description="Monitorize faturas, planos ativos e a saúde financeira da plataforma.">
        <x-slot:actions>
            <div class="flex gap-2">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Procurar fatura ou user..." class="w-72 shadow-sm" />
                  <flux:select wire:model.live="filterPlan" class="w-44">
                    <option value="all">Todos os Planos</option>
                    <option value="premium">💎 Plano Premium</option>
                    <option value="business">🏢 Plano Business</option>
                </flux:select>

                <flux:select wire:model.live="filterStatus" class="w-44">
                    <option value="all">Todos os Estados</option>
                    <option value="paid">✅ Pagamento OK</option>
                    <option value="pending">⏳ Aguarda</option>
                    <option value="failed">❌ Falhou</option>
                </flux:select>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- KPIS FINANCEIROS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stat-card bg-zinc-900 text-white p-6 rounded-[2rem] border border-zinc-800 shadow-xl">
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">Faturação Mensal (MRR)</p>
            <p class="text-3xl font-black mt-1 text-emerald-400">{{ number_format($stats['mrr'], 2) }} €</p>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Receita Acumulada</p>
            <p class="text-3xl font-black mt-1 dark:text-white">{{ number_format($stats['total_revenue'], 2) }} €</p>
        </div>

         <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
            <p class="text-[10px] font-black uppercase text-brand-600 tracking-widest">Subscrições Ativas</p>
            <p class="text-3xl font-black text-brand-600 mt-1">{{ $stats['active_premium'] }}</p>

            {{-- Divisão por plano --}}
            <div class="mt-3 flex items-center gap-3">
                <div class="flex items-center gap-1">
                    <span class="size-2 bg-emerald-500 rounded-full"></span>
                    <span class="text-[10px] font-black text-zinc-500 uppercase">{{ $stats['count_premium'] }} Premium</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="size-2 bg-violet-500 rounded-full"></span>
                    <span class="text-[10px] font-black text-zinc-500 uppercase">{{ $stats['count_business'] }} Business</span>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-red-500 tracking-widest">Churn / Falhas</p>
            <p class="text-3xl font-black mt-1 text-red-500">{{ $stats['failed_payments'] }}</p>
        </div>
    </div>

    {{-- LISTA DE FATURAÇÃO --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden text-left">
        <div class="p-6 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Histórico de Transações Reais</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-950/50 border-b border-zinc-100 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-500 tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Nº Fatura</th>
                        <th class="px-6 py-5">Utilizador</th>
                        <th class="px-6 py-5 text-center">Plano</th>
                        <th class="px-6 py-5 text-center">Valor</th>
                        <th class="px-6 py-5 text-center">Estado</th>
                        <th class="px-8 py-5 text-right">Data do Pagamento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($payments as $p)
                        <tr class="hover:bg-brand-500/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-xs font-mono font-bold text-zinc-500 group-hover:text-brand-600 transition-colors">{{ $p->invoice_id }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col text-left">
                                    <span class="text-sm font-black text-zinc-900 dark:text-white leading-none">{{ $p->user_name }}</span>
                                    <span class="text-[10px] text-zinc-500 mt-1 italic leading-none">{{ $p->user_email }}</span>
                                </div>
                            </td>

                            {{-- COLUNA DO PLANO COM CORES DA IMAGEM --}}
                            <td class="px-6 py-5 text-center">
                                @php
                                    $planColor = match(strtolower($p->plan_type)) {
                                        'premium' => 'emerald', // Verde para Premium
                                        'business' => 'violet', // Roxo para Business
                                        default => 'zinc'
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $planColor }}" class="uppercase font-black text-[9px] px-3 shadow-sm">
                                    {{ strtoupper($p->plan_type) }}
                                </flux:badge>
                            </td>

                            <td class="px-6 py-5 text-center">
                                <span class="text-sm font-black text-zinc-900 dark:text-white">
                                    {{ number_format($p->amount, 2) }} €
                                </span>
                            </td>

                            <td class="px-6 py-5 text-center">
                                <flux:badge size="sm" color="{{ $p->status == 'paid' ? 'emerald' : ($p->status == 'failed' ? 'red' : 'zinc') }}" variant="pill" class="font-black uppercase text-[8px] tracking-tighter">
                                    {{ $p->status === 'paid' ? 'Liquidado' : $p->status }}
                                </flux:badge>
                            </td>

                            <td class="px-8 py-5 text-right">
                                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-tighter">
                                    {{ \Carbon\Carbon::parse($p->paid_at)->format('d/m/Y') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-20 text-center text-zinc-400 italic font-black uppercase text-xs opacity-40">
                                Nenhuma transação financeira registada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t dark:border-zinc-800 bg-zinc-50/30">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<div class="space-y-8 text-left pb-20">
    <x-page-header title="Faturação & Pagamentos" description="Receita, planos ativos e histórico de faturas. Pesquisa, filtra e corrige qualquer registo.">
        <x-slot:actions>
            <flux:button wire:click="startCreatePayment" variant="primary" icon="plus" class="rounded-2xl font-black uppercase tracking-widest h-12">
                Registo manual
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="flex flex-wrap gap-3">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Fatura, nome ou email..." class="w-full sm:w-80 shadow-sm" />

        <flux:select wire:model.live="filterPlan" class="w-44">
            <option value="all">Todos os planos</option>
            <option value="free">Free</option>
            @foreach($dbPlans as $plan)
                <option value="{{ $plan->slug }}">{{ $plan->name }}</option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="filterStatus" class="w-44">
            <option value="all">Todos os estados</option>
            <option value="paid">Liquidado</option>
            <option value="pending">Pendente</option>
            <option value="failed">Falhou</option>
            <option value="refunded">Reembolsado</option>
        </flux:select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-zinc-900 text-white p-6 rounded-[2rem] border border-zinc-800 shadow-xl">
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">MRR estimado</p>
            <p class="text-3xl font-black mt-1 text-emerald-400">{{ number_format($stats['mrr'], 2, ',', ' ') }} €</p>
            <p class="text-[10px] text-zinc-500 mt-2">Utilizadores ativos × preço atual do plano</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Receita deste mês</p>
            <p class="text-3xl font-black mt-1 dark:text-white">{{ number_format($stats['month_revenue'], 2, ',', ' ') }} €</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Segmentação</p>
            <p class="text-3xl font-black dark:text-white mt-1">{{ $stats['total_users'] }} <small class="text-xs text-zinc-400 font-bold uppercase">users</small></p>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-[10px] font-black uppercase text-zinc-500">
                    <span>Free</span><span>{{ $stats['count_free'] }}</span>
                </div>
                @foreach($stats['plan_details'] as $plan)
                    <div class="flex justify-between text-[10px] font-black uppercase {{ $plan['color'] === 'violet' ? 'text-violet-600' : 'text-emerald-600' }}">
                        <span class="truncate">{{ $plan['name'] }}</span><span>{{ $plan['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-red-500 tracking-widest">Alertas</p>
            <p class="text-3xl font-black mt-1 text-red-500">{{ $stats['failed_payments'] }}</p>
            <p class="text-[10px] text-zinc-400 mt-2 font-bold uppercase">{{ $stats['pending_payments'] }} pendentes · {{ number_format($stats['total_revenue'], 2, ',', ' ') }} € acumulados</p>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b dark:border-zinc-800 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Histórico de transações</h3>
            <span class="text-[10px] font-black text-zinc-400 uppercase">{{ $payments->total() }} registos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-950/50 border-b border-zinc-100 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-500 tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Fatura</th>
                        <th class="px-6 py-4">Utilizador</th>
                        <th class="px-6 py-4">Plano</th>
                        <th class="px-6 py-4 text-right">Valor</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Método</th>
                        <th class="px-6 py-4">Data</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($payments as $p)
                        @php
                            $planName = $dbPlans->firstWhere('slug', $p->plan_type)?->name ?? strtoupper($p->plan_type ?: 'FREE');
                            $statusLabel = match($p->status) {
                                'paid' => 'Liquidado',
                                'pending' => 'Pendente',
                                'failed' => 'Falhou',
                                'refunded' => 'Reembolsado',
                                default => $p->status,
                            };
                            $statusColor = match($p->status) {
                                'paid' => 'emerald',
                                'pending' => 'amber',
                                'failed' => 'red',
                                'refunded' => 'zinc',
                                default => 'zinc',
                            };
                        @endphp
                        <tr class="hover:bg-brand-500/[0.02] transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-xs font-mono font-bold text-zinc-600 dark:text-zinc-300">{{ $p->invoice_id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col min-w-0">
                                    <span class="text-sm font-black text-zinc-900 dark:text-white leading-none">{{ $p->user?->name ?? '—' }}</span>
                                    <span class="text-[10px] text-zinc-500 mt-1 italic">{{ $p->user?->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300">
                                    {{ $planName }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black dark:text-white whitespace-nowrap">
                                {{ number_format($p->amount, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="{{ $statusColor }}" variant="pill" class="font-black uppercase text-[8px]">
                                    {{ $statusLabel }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-[11px] font-bold uppercase text-zinc-400">{{ $p->method ?: '—' }}</td>
                            <td class="px-6 py-4 font-bold text-[11px] text-zinc-400 whitespace-nowrap">
                                {{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="editPayment({{ $p->id }})" class="h-9 px-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-[9px] font-black uppercase tracking-widest text-zinc-600 hover:border-brand-500 hover:text-brand-600">
                                        Editar
                                    </button>
                                    <button wire:click="deletePayment({{ $p->id }})" wire:confirm="Eliminar este registo de pagamento?" class="h-9 px-3 rounded-xl border border-red-200 text-[9px] font-black uppercase tracking-widest text-red-500">
                                        Apagar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-16 text-center text-zinc-400 italic font-black uppercase text-xs tracking-widest">Nenhuma transação encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t dark:border-zinc-800 bg-zinc-50/30">
            {{ $payments->links() }}
        </div>
    </div>

    @if($showPaymentForm)
        <div class="fixed inset-0 z-[300] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md" wire:click="resetPaymentForm"></div>

            <div class="relative w-full max-w-xl bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                <div class="p-6 border-b dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black dark:text-white uppercase italic">{{ $editingPaymentId ? 'Editar fatura' : 'Novo registo' }}</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mt-1">Correções manuais e pagamentos fora do Stripe</p>
                    </div>
                    <button wire:click="resetPaymentForm" class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full text-zinc-400"><flux:icon name="x-mark" class="size-6" /></button>
                </div>

                <form wire:submit.prevent="savePayment" class="p-6 space-y-4">
                    <flux:input wire:model="pay_user_email" type="email" label="Email do utilizador" placeholder="cliente@email.com" :disabled="(bool) $editingPaymentId" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model="pay_invoice_id" label="Nº da fatura" />
                        <div>
                            <flux:label>Plano</flux:label>
                            <select wire:model="pay_plan_type" class="mt-1 w-full rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-950">
                                <option value="free">Free</option>
                                @foreach($dbPlans as $plan)
                                    <option value="{{ $plan->slug }}">{{ $plan->name }} ({{ number_format($plan->price, 2) }}€)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model="pay_amount" type="number" step="0.01" min="0" label="Valor (€)" />
                        <div>
                            <flux:label>Estado</flux:label>
                            <select wire:model="pay_status" class="mt-1 w-full rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-950">
                                <option value="paid">Liquidado</option>
                                <option value="pending">Pendente</option>
                                <option value="failed">Falhou</option>
                                <option value="refunded">Reembolsado</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:label>Método</flux:label>
                            <select wire:model="pay_method" class="mt-1 w-full rounded-xl border-zinc-200 dark:border-zinc-700 dark:bg-zinc-950">
                                <option value="stripe">Stripe</option>
                                <option value="mbway">MB Way</option>
                                <option value="multibanco">Multibanco</option>
                                <option value="transfer">Transferência</option>
                                <option value="system">Manual / sistema</option>
                            </select>
                        </div>
                        <flux:input wire:model="pay_paid_at" type="datetime-local" label="Data do pagamento" />
                    </div>

                    <div class="flex gap-3 pt-2">
                        <flux:button type="submit" variant="primary" class="flex-1 h-12 rounded-2xl font-black uppercase tracking-widest">
                            Guardar
                        </flux:button>
                        <flux:button type="button" wire:click="resetPaymentForm" variant="ghost" class="h-12 rounded-2xl font-black uppercase">
                            Cancelar
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

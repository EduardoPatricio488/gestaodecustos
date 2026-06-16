<div class="space-y-10 pb-20">
    {{-- 1. HEADER PREMIUM --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full transition-all duration-700 shadow-brand-500/20"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="credit-card" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Assinaturas</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Custos Fixos</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Monitorização de <span class="text-brand-600 font-bold uppercase tracking-tighter">Débitos Diretos e Recorrência</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:button
                    @click="$dispatch('modal-show-add-sub')"
                    variant="primary"
                    icon="plus"
                    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                    Nova Assinatura
                </flux:button>
            </div>
        </header>
    </div>

    {{-- 2. KPIs DE PERFORMANCE FINANCEIRA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Mensal --}}
        <div class="stat-card bg-zinc-950 text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
            <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-400 mb-2">Total Mensal Fixo</p>
                <h3 class="text-5xl font-black tracking-tighter italic text-white">{{ number_format($totalMonthly, 2, ',', ' ') }} <small class="text-xl not-italic ml-1">€</small></h3>
                <div class="mt-6 h-1 w-full bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full bg-brand-500 shadow-[0_0_15px_#3b82f6]" style="width: 70%"></div>
                </div>
            </div>
            <flux:icon name="banknotes" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>

        {{-- Próximos 30 dias --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Previsão 30 Dias</p>
                <h3 class="text-4xl font-black text-orange-500 tracking-tighter italic">{{ number_format($upcoming, 2, ',', ' ') }} €</h3>
            </div>
            <p class="mt-4 text-[9px] font-bold text-zinc-500 uppercase tracking-widest italic flex items-center gap-2">
                <span class="size-2 bg-orange-500 rounded-full animate-ping"></span>
                Pagamentos agendados
            </p>
        </div>

        {{-- Média Diária --}}
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group">
            <div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Custo Médio Diário</p>
                <h3 class="text-4xl font-black dark:text-white tracking-tighter italic">{{ number_format($totalMonthly / 30, 2, ',', ' ') }} €</h3>
            </div>
            <p class="mt-4 text-[9px] font-black text-emerald-600 uppercase italic">Eficiência de Recorrência</p>
        </div>
    </div>

    {{-- 3. PIPELINE DE CUSTOS RECORRENTES (LISTA) --}}
    <div class="space-y-6">
        <div class="flex items-center gap-3 px-2">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="queue-list" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Contratos & Subscrições Ativas</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($subscriptions as $sub)
                <div class="glass-card p-6 flex justify-between items-center bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] shadow-sm hover:border-brand-500/40 transition-all duration-300 group">
                    <div class="flex items-center gap-5">
                        {{-- Indicador de Dia de Cobrança --}}
                        <div class="relative shrink-0">
                            <div class="size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 flex flex-col items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                                <span class="text-[8px] font-black text-zinc-400 uppercase leading-none mb-1">Dia</span>
                                <span class="text-xl font-black text-brand-600 leading-none tracking-tighter">{{ str_pad($sub->billing_day, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <h4 class="font-black dark:text-white uppercase text-sm tracking-tight group-hover:text-brand-600 transition-colors">
                                {{ $sub->name }}
                            </h4>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-[0.2em] rounded-md border border-zinc-200 dark:border-zinc-700">
                                    {{ $sub->category->name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right space-y-2">
                        <p class="text-2xl font-black dark:text-white tracking-tighter italic">
                            {{ number_format($sub->amount, 2, ',', ' ') }} <small class="text-xs">€</small>
                        </p>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="rounded-xl opacity-0 group-hover:opacity-100 transition-opacity" />

                            <flux:menu>
                                <flux:menu.item wire:click="delete({{ $sub->id }})" wire:confirm="Tens a certeza que desejas cancelar esta monitorização?" variant="danger" icon="trash">
                                    Remover Assinatura
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            @endforeach

            @if($subscriptions->isEmpty())
                <div class="lg:col-span-2 py-20 text-center glass-card rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                    <flux:icon name="credit-card" class="size-12 text-zinc-300 mx-auto mb-4" />
                    <p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">Sem débitos registados</p>
                </div>
            @endif
        </div>
    </div>
    {{-- 4. MODAL: CONFIGURAR NOVA ASSINATURA --}}
    <div
        x-data="{
            open: false,
            show() {
                this.open = true;
                document.documentElement.classList.add('overflow-hidden');
            },
            close() {
                this.open = false;
                document.documentElement.classList.remove('overflow-hidden');
            }
        }"
        x-on:modal-show-add-sub.window="show()"
        x-on:modal-close-add-sub.window="close()"
        x-on:keydown.escape.window="close()">

        {{-- Backdrop --}}
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition-opacity ease-out duration-75"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-75"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close()"
            class="fixed inset-0 z-50 bg-zinc-950/55 backdrop-blur-[2px] will-change-opacity">
        </div>

        {{-- Wrapper do modal --}}
        <div
            x-show="open"
            x-cloak
            @click.self="close()"
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">

            {{-- Caixa do Modal --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100 transform-gpu"
                x-transition:enter-start="opacity-0 scale-[0.98] translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75 transform-gpu"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-[0.98] translate-y-1"
                class="relative z-10 w-full max-w-xl bg-white dark:bg-zinc-950 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden transform-gpu will-change-transform"
                @click.stop>

                <form wire:submit.prevent="save" class="flex max-h-[86vh] flex-col">
                    {{-- Cabeçalho --}}
                    <div class="shrink-0 p-5 sm:p-6 pb-4 flex items-center gap-4 border-b border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                        <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-md shadow-brand-500/20">
                            <flux:icon name="credit-card" class="size-5" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <flux:heading size="lg" class="font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                                Nova Assinatura
                            </flux:heading>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                                Controlo de custo recorrente
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="close()"
                            class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors duration-100">
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </div>

                    {{-- Corpo do Formulário --}}
                    <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-5">
                        {{-- Identificação --}}
                        <flux:input
                            wire:model.defer="name"
                            label="NOME DA ASSINATURA"
                            placeholder="Ex: Netflix, Spotify, Renda, Ginásio..."
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner" />

                        {{-- Valor + data --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <flux:input
                                wire:model.defer="amount"
                                type="number"
                                step="0.01"
                                min="0"
                                label="VALOR"
                                placeholder="12.99"
                                class="font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner text-brand-600" />

                            <flux:input
                                wire:model.defer="billing_day"
                                type="number"
                                min="1"
                                max="31"
                                label="DIA DE DÉBITO"
                                placeholder="Ex: 15"
                                class="font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner" />
                        </div>

                        {{-- Categoria + ciclo --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <flux:select
                                wire:model.defer="category_id"
                                label="CATEGORIA"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner">
                                <option value="">Selecionar...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </flux:select>

                            <flux:select
                                wire:model.defer="billing_cycle"
                                label="CICLO"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner">
                                <option value="monthly">Mensal</option>
                                <option value="quarterly">Trimestral</option>
                                <option value="semiannual">Semestral</option>
                                <option value="annual">Anual</option>
                            </flux:select>
                        </div>

                        {{-- Método + estado --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <flux:select
                                wire:model.defer="payment_method"
                                label="PAGAMENTO"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner">
                                <option value="">Selecionar...</option>
                                <option value="card">Cartão</option>
                                <option value="direct_debit">Débito direto</option>
                                <option value="bank_transfer">Transferência</option>
                                <option value="paypal">PayPal</option>
                                <option value="cash">Numerário</option>
                            </flux:select>

                            <flux:select
                                wire:model.defer="status"
                                label="ESTADO"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner">
                                <option value="active">Ativa</option>
                                <option value="paused">Pausada</option>
                                <option value="cancelled">Cancelada</option>
                            </flux:select>
                        </div>

                        {{-- Datas importantes --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <flux:input
                                wire:model.defer="started_at"
                                type="date"
                                label="INÍCIO"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner" />

                            <flux:input
                                wire:model.defer="renewal_date"
                                type="date"
                                label="RENOVAÇÃO"
                                class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 border-none shadow-inner" />
                        </div>

                        {{-- Notas --}}
                        <flux:textarea
                            wire:model.defer="notes"
                            label="NOTAS"
                            placeholder="Ex: plano familiar, contrato anual, promoção termina em breve..."
                            rows="3"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl border-none shadow-inner" />

                        {{-- Alertas --}}
                        <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 p-4 space-y-3">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                        Lembrete de cobrança
                                    </p>
                                    <p class="text-xs font-bold text-zinc-400 mt-1">
                                        Avisar antes da renovação ou débito.
                                    </p>
                                </div>

                                <input
                                    type="checkbox"
                                    wire:model.defer="notify_before_billing"
                                    class="rounded border-zinc-300 text-brand-600 focus:ring-brand-600">
                            </div>

                            <flux:input
                                wire:model.defer="notify_days_before"
                                type="number"
                                min="1"
                                max="30"
                                label="DIAS ANTES"
                                placeholder="3"
                                class="font-black !bg-white dark:!bg-zinc-950 rounded-2xl h-12 border-none shadow-inner" />
                        </div>
                    </div>

                    {{-- Rodapé / Acções --}}
                    <div class="shrink-0 p-5 sm:p-6 pt-4 flex gap-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                        <button
                            type="button"
                            @click="close()"
                            class="flex-1 uppercase font-black text-[10px] h-12 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors">
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="flex-[2] bg-brand-600 h-12 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 border-none text-white hover:bg-brand-500 active:scale-95 transition-all">
                            <span wire:loading.remove wire:target="save">Ativar Assinatura</span>
                            <span wire:loading wire:target="save">A guardar...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 5. RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Sistema de Monitorização
        </p>
    </footer>
</div> {{-- FIM DA DIV RAIZ PRINCIPAL --}}

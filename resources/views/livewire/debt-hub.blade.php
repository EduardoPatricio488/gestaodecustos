<div class="space-y-10 pb-24">
    {{-- 1. HEADER DE TESOURARIA --}}
    <div class="relative">
        {{-- Glow decorativo --}}
        <div class="absolute -top-10 left-0 size-72 bg-brand-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-orange-500/20 blur-2xl rounded-full transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.2rem] shadow-2xl">
                        <flux:icon name="hand-raised" class="w-10 h-10 text-orange-500" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Dívidas & Créditos</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Fluxo de Terceiros</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Controlo de <span class="text-orange-600 font-bold uppercase tracking-tighter">Passivos</span> e <span class="text-emerald-600 font-bold uppercase tracking-tighter">Ativos Circulantes</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="add-debt-modal">
                    <flux:button variant="primary" icon="plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 bg-brand-600 text-white border-none">
                        Novo Registo
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </header>
    </div>

    {{-- 2. KPIs DE EXPOSIÇÃO FINANCEIRA --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Total que Eu Devo (Passivo) --}}
        <div class="stat-card bg-zinc-950 text-white p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 group">
            <div class="absolute -right-10 -top-10 size-48 bg-red-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-red-500 mb-2">Total em Dívida (Passivo)</p>
                <h3 class="text-6xl font-black tracking-tighter italic text-white">{{ number_format($totalIOwe, 2, ',', ' ') }} <small class="text-xl not-italic ml-1">€</small></h3>
                <div class="mt-6 flex items-center gap-2">
                    <div class="size-2 bg-red-500 rounded-full animate-pulse"></div>
                    <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest italic">Capital a ser liquidado</p>
                </div>
            </div>
            <flux:icon name="arrow-trending-down" class="absolute -right-4 -bottom-4 size-28 text-white/5 -rotate-12" />
        </div>

        {{-- Total a Receber (Ativo) --}}
        <div class="glass-card p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-between group relative overflow-hidden">
             <div class="absolute -right-10 -top-10 size-48 bg-emerald-500/5 blur-3xl rounded-full"></div>
             <div class="relative z-10">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-2">Total a Receber (Ativo)</p>
                <h3 class="text-6xl font-black text-emerald-600 dark:text-emerald-500 tracking-tighter italic">{{ number_format($totalTheyOweMe, 2, ',', ' ') }} <small class="text-xl not-italic ml-1">€</small></h3>
                <div class="mt-6 flex items-center gap-2">
                    <div class="size-2 bg-emerald-500 rounded-full"></div>
                    <p class="text-[9px] font-black text-emerald-600 uppercase italic">Expectativa de cash-in</p>
                </div>
            </div>
            <flux:icon name="arrow-trending-up" class="absolute -right-4 -bottom-4 size-28 text-zinc-100 dark:text-zinc-800/50 -rotate-12" />
        </div>
    </div>

    {{-- 3. LEDGER DE PENDENTES (VISTA DETALHADA) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- COLUNA: PASSIVO (EU DEVO) --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-2">
                <div class="p-2 bg-orange-500/10 rounded-lg text-orange-600">
                    <flux:icon name="arrow-down-circle" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Mapa de Passivos (Owe)</h2>
            </div>

            <div class="space-y-4">
                @forelse($iOwe as $debt)
                    <div class="glass-card p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] flex justify-between items-center group hover:border-orange-500/40 transition-all duration-300 shadow-sm">
                        <div class="flex items-center gap-5">
                            <div class="size-12 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 flex items-center justify-center text-orange-600 font-black text-xs shadow-inner">
                                {{ substr($debt->person_name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $debt->person_name }}</p>
                                <p class="text-[10px] text-zinc-500 mt-1 italic font-medium leading-relaxed max-w-[180px] truncate">
                                    {{ $debt->description ?: 'Sem observações técnicas' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-xl font-black text-orange-600 tracking-tighter italic">
                                    {{ number_format($debt->amount, 2, ',', ' ') }} €
                                </p>
                                @if($debt->due_at)
                                    <span class="text-[8px] font-black bg-orange-100 dark:bg-orange-900/30 text-orange-600 px-2 py-0.5 rounded uppercase tracking-widest border border-orange-200 dark:border-orange-800/50">
                                        Vence {{ $debt->due_at->format('d M') }}
                                    </span>
                                @endif
                            </div>
                            <flux:button wire:click="togglePaid({{ $debt->id }})" variant="ghost" icon="check-circle" size="sm" color="emerald" class="opacity-0 group-hover:opacity-100 transition-all hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-xl" title="Liquidar Dívida" />
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center glass-card rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                        <p class="text-zinc-400 font-black uppercase tracking-widest text-[9px]">Passivo totalmente liquidado</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- COLUNA: ATIVO (DEVEM-ME) --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-2">
                <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
                    <flux:icon name="arrow-up-circle" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Mapa de Ativos (Owed)</h2>
            </div>

            <div class="space-y-4">
                @forelse($theyOweMe as $debt)
                    <div class="glass-card p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] flex justify-between items-center group hover:border-emerald-500/40 transition-all duration-300 shadow-sm border-l-4 border-l-emerald-500/20">
                        <div class="flex items-center gap-5">
                            <div class="size-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/10 flex items-center justify-center text-emerald-600 font-black text-xs shadow-inner uppercase">
                                {{ substr($debt->person_name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-black dark:text-white uppercase tracking-tight">{{ $debt->person_name }}</p>
                                <p class="text-[10px] text-zinc-500 mt-1 italic font-medium leading-relaxed max-w-[180px] truncate">
                                    {{ $debt->description ?: 'Crédito sem nota' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-xl font-black text-emerald-600 tracking-tighter italic">
                                    {{ number_format($debt->amount, 2, ',', ' ') }} €
                                </p>
                                @if($debt->due_at)
                                    <span class="text-[8px] font-black bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 px-2 py-0.5 rounded uppercase tracking-widest border border-emerald-200 dark:border-emerald-800/50">
                                        Expectativa {{ $debt->due_at->format('d M') }}
                                    </span>
                                @endif
                            </div>
                            <flux:button wire:click="togglePaid({{ $debt->id }})" variant="ghost" icon="check-circle" size="sm" color="emerald" class="opacity-0 group-hover:opacity-100 transition-all hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-xl" title="Confirmar Recebimento" />
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center glass-card rounded-[2.5rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                        <p class="text-zinc-400 font-black uppercase tracking-widest text-[9px]">Sem créditos pendentes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 4. PROTOCOLOS DE LIQUIDAÇÃO (HISTÓRICO RECENTE) --}}
    <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden">
        <div class="absolute right-0 top-0 p-8 opacity-5">
            <flux:icon name="check-badge" class="size-24 text-zinc-900 dark:text-white" />
        </div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-8">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="clock" variant="outline" class="size-4" />
                </div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400">Auditoria de Liquidações Recentes</h2>
            </div>

            <div class="space-y-2">
                @foreach($history as $item)
                    <div class="flex justify-between items-center py-4 border-b border-zinc-50 dark:border-zinc-800/50 last:border-0 transition-all hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 px-2 rounded-xl group">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg group-hover:scale-110 transition-transform">
                                <flux:icon name="check-badge" variant="solid" class="text-zinc-400 size-4" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-black dark:text-zinc-200 uppercase tracking-tight">{{ $item->person_name }}</span>
                                <span class="text-[9px] text-zinc-400 font-bold uppercase italic mt-0.5">{{ $item->updated_at->format('d/m/Y · H:i') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="text-[9px] font-black {{ $item->type === 'owe' ? 'text-orange-500 bg-orange-500/5' : 'text-emerald-500 bg-emerald-500/5' }} px-3 py-1 rounded-full uppercase tracking-widest border border-current opacity-70">
                                {{ $item->type === 'owe' ? 'Pago' : 'Recebido' }}
                            </span>
                            <span class="text-sm font-black dark:text-zinc-300 tracking-tighter">{{ number_format($item->amount, 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                @endforeach

                @if(count($history) === 0)
                    <p class="py-10 text-center text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">Sem registos de liquidação no período atual.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- 5. MODAL: REGISTAR OPERAÇÃO DE CAPITAL (DESIGN SaaS PRO) --}}
    <flux:modal name="add-debt-modal" position="center" class="md:w-[500px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            <div class="text-center space-y-2">
                <div class="inline-flex p-3 bg-orange-500/10 rounded-2xl mb-2 text-orange-600">
                    <flux:icon name="hand-raised" class="size-6" />
                </div>
                <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">Registar Operação</flux:heading>
                <p class="text-xs text-zinc-400 font-medium italic">Documente a transferência de capital entre partes.</p>
            </div>

            <form wire:submit="save" class="space-y-6">
                {{-- Toggle de Tipo --}}
                <div class="grid grid-cols-2 gap-2 p-1.5 bg-zinc-100 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-inner">
                    <button type="button" wire:click="$set('type', 'owe')"
                        class="py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $type === 'owe' ? 'bg-zinc-950 text-white shadow-lg' : 'text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-800' }}">
                        EU DEVO
                    </button>
                    <button type="button" wire:click="$set('type', 'owed')"
                        class="py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $type === 'owed' ? 'bg-emerald-600 text-white shadow-lg' : 'text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-800' }}">
                        DEVEM-ME
                    </button>
                </div>

                {{-- Entidade --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Entidade / Pessoa</flux:label>
                    <flux:input
                        wire:model="person_name"
                        placeholder="Ex: João Silva ou Banco Millennium..."
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl h-14 shadow-inner"
                    />
                </div>

                <div class="grid grid-cols-2 gap-6">
                    {{-- Valor --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Montante (€)</flux:label>
                        <flux:input
                            wire:model="amount"
                            type="number"
                            step="0.01"
                            placeholder="0,00"
                            class="font-black !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl h-14 shadow-inner text-brand-600"
                        />
                    </div>
                    {{-- Data --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Vencimento (Opcional)</flux:label>
                        <flux:input
                            wire:model="due_at"
                            type="date"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                </div>

                {{-- Descrição --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest px-1">Notas Internas</flux:label>
                    <flux:textarea
                        wire:model="description"
                        placeholder="Ex: Referente ao jantar de equipa ou empréstimo para carro..."
                        rows="2"
                        class="!bg-zinc-50 dark:!bg-zinc-950 border-none rounded-2xl p-4 shadow-inner text-sm"
                    />
                </div>

                <div class="flex gap-4 pt-4">
                    <flux:modal.close class="flex-1"><flux:button variant="ghost" class="w-full font-black uppercase text-[10px] text-zinc-400 h-14 rounded-2xl">Descartar</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl bg-brand-600 border-none text-white">Validar Operação</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- 6. RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Controlo de Tesouraria e Terceiros
        </p>
    </footer>
</div> {{-- FECHO DA DIV RAIZ PRINCIPAL QUE ABRIU NA PARTE 1 --}}

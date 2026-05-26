<div class="space-y-10 pb-24">
    {{-- 1. HEADER DE GESTÃO DE TALENTOS (ESTILO SaaS PREMIUM) --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="users" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Equipa & RH</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">Capital Humano</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Gestão de competências, vencimentos e estrutura organizacional</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="add-employee-modal">
                    <flux:button variant="primary" icon="user-plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Novo Colaborador
                    </flux:button>
                </flux:modal.trigger>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
            </div>
        </header>
    </div>

    {{-- 2. KPIs DE RECURSOS HUMANOS (PAYROLL ANALYTICS COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Payroll Total (Black Glass) --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400 shadow-inner">
                        <flux:icon name="banknotes" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest italic text-zinc-400">Custo RH</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Payroll Mensal Estimado</p>
                <h3 class="text-4xl font-black text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        {{ number_format($totalPayroll, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
        </div>

        {{-- Contagem de Equipa --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-brand-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-2xl text-zinc-500">
                    <flux:icon name="user-group" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Efetivos em Funções</p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $employeeCount }}
                </span>
                <span class="text-xs text-zinc-400 uppercase font-bold ml-2 tracking-widest italic">Pessoas</span>
            </h3>
        </div>

        {{-- Calendário de Pagamento --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-orange-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-orange-50 dark:bg-orange-500/10 rounded-2xl text-orange-600">
                    <flux:icon name="calendar-days" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Ciclo de Tesouraria</p>
            <h3 class="text-2xl font-black text-orange-500 tracking-tighter uppercase italic leading-none mt-1">
                Dia 25 de {{ now()->translatedFormat('M') }}
            </h3>
            <p class="mt-4 text-[9px] text-zinc-400 font-bold uppercase tracking-widest italic">Próximo Vencimento</p>
        </div>
    </div>

    {{-- 3. DIRETÓRIO DE EQUIPA (ESTILO TALENT CARDS) --}}
    <div class="space-y-6">
        <div class="flex items-center gap-3 px-2">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <flux:icon name="user-group" variant="outline" class="size-4" />
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Diretório de Colaboradores</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($employees as $emp)
                <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-brand-500/30 transition-all duration-300 group overflow-hidden flex flex-col h-full">

                    {{-- Topo do Card: Identidade e Função --}}
                    <div class="p-7 flex justify-between items-start">
                        <div class="flex items-center gap-5">
                            {{-- Avatar com Iniciais SaaS Style --}}
                            <div class="size-16 rounded-[1.5rem] bg-brand-500/10 text-brand-600 flex items-center justify-center font-black text-2xl shadow-inner border border-brand-500/20 uppercase">
                                {{ substr($emp->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-black dark:text-white uppercase text-base tracking-tight leading-none group-hover:text-brand-600 transition-colors">
                                    {{ $emp->name }}
                                </h4>
                                <span class="inline-flex mt-2 px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">
                                    {{ $emp->role }}
                                </span>
                            </div>
                        </div>

                        <flux:dropdown>
                            <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" class="rounded-xl opacity-0 group-hover:opacity-100 transition-opacity" />
                            <flux:menu class="min-w-[180px] p-2">
                                <flux:menu.item wire:click="edit({{ $emp->id }})" icon="pencil-square" class="font-bold text-xs uppercase">Editar Ficha</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item wire:click="delete({{ $emp->id }})" wire:confirm="Remover colaborador e dados contratuais?" variant="danger" icon="trash" class="font-bold text-xs uppercase text-red-500">Remover da Equipa</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    {{-- Meio do Card: Detalhes de Ciclo --}}
                    <div class="px-7 space-y-3 mb-8">
                        <div class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400 group/info">
                            <flux:icon name="calendar" variant="micro" class="size-3.5 group-hover/info:text-brand-500 transition-colors" />
                            <span class="text-[10px] font-black uppercase tracking-widest">Pagamento: Dia {{ sprintf('%02d', $emp->pay_day) }}</span>
                        </div>
                    </div>

                    {{-- Rodapé do Card: Vencimento (Ledger Style) --}}
                    <div class="mt-auto p-7 bg-zinc-50/50 dark:bg-zinc-800/40 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-end">
                        <div>
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Vencimento Mensal</p>
                            <h3 class="text-xl font-black dark:text-zinc-100 tracking-tighter italic leading-none">
                                {{-- APLICAÇÃO DO BLUR --}}
                                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                    {{ number_format($emp->salary, 2, ',', ' ') }}
                                </span> €
                            </h3>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl text-[9px] font-black uppercase tracking-widest">
                                Ativo
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 text-center">
                    <div class="flex flex-col items-center justify-center gap-4">
                        <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                            <flux:icon name="users" class="size-12 text-zinc-200 dark:text-zinc-700" />
                        </div>
                        <div class="space-y-1 text-center">
                            <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Equipa Vazia</p>
                            <p class="text-zinc-400 text-xs italic font-medium">Ainda não registaste nenhum talento no sistema.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 4. MODAL: REGISTO DE TALENTO (DESIGN SaaS PRO) --}}
    <flux:modal name="add-employee-modal" position="center" class="md:w-[600px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="user-plus" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        {{ $editingId ? 'Editar Ficha Técnica' : 'Contratar Colaborador' }}
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium italic">Define os parâmetros contratuais e financeiros do talento.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- SECÇÃO: IDENTIDADE E FUNÇÃO --}}
                <div class="space-y-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Nome Completo do Colaborador</flux:label>
                        <flux:input
                            wire:model="name"
                            placeholder="Ex: João Pedro Silva"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Cargo / Função na Empresa</flux:label>
                        <flux:input
                            wire:model="role"
                            placeholder="Ex: Lead Software Engineer"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                        />
                    </div>
                </div>

                {{-- SECÇÃO: PARÂMETROS CONTRATUAIS (PAINEL DE DESTAQUE) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="flex items-center gap-2 px-1">
                        <flux:icon name="credit-card" class="size-3 text-brand-500" />
                        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">Condições de Vencimento</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Salário Bruto Mensal (€)</flux:label>
                            <flux:input
                                wire:model="salary"
                                type="number"
                                step="0.01"
                                class="font-black text-xl text-zinc-900 dark:text-white !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                                placeholder="0,00"
                            />
                        </div>

                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Dia de Processamento</flux:label>
                            <flux:input
                                wire:model="pay_day"
                                type="number"
                                min="1"
                                max="31"
                                class="font-black text-xl text-center !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                    {{ $editingId ? 'Atualizar Colaborador' : 'Confirmar Contratação' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Gestão de Capital Humano
        </p>
    </footer>
</div>

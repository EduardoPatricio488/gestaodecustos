{{-- MODAL: NOVA RENDA / IMOBILIÁRIO                               --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-renda.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-renda.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveRental" class="flex flex-col {{ !empty($onboardingModal) ? 'max-h-[90vh]' : '' }}">
                @include('livewire.partials.onboarding-validation-errors')
                {{-- HEADER --}}
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-blue-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="home-modern" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Rendimento Imobiliário</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Registo de Rendas e Exploração</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    {{-- TIPO DE EXPLORAÇÃO (Estilo Wizard) --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-blue-600 z-10">Tipo de Gestão</label>
                        <select wire:model="recPropertyType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                            <option value="arrendamento">🏠 Arrendamento de Longa Duração</option>
                            <option value="alojamento_local">🏨 Alojamento Local (Turismo)</option>
                            <option value="outro">🏢 Comercial / Outro</option>
                        </select>
                    </div>

                    {{-- DESCRIÇÃO --}}
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Identificação do Ativo</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Apartamento em Lisboa..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-blue-500 transition-all">
                    </div>

                    {{-- RENDIMENTO BRUTO VS DESPESAS --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Renda Bruta (€)</label>
                            <input type="number" step="0.01" wire:model.live="recRentalGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-emerald-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Despesas Totais (€)</label>
                            <input type="number" step="0.01" wire:model.live="recRentalExpenses" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                        </div>
                    </div>

                    {{-- QUADRO DE RESULTADO (IDENTICO AO WIZARD) --}}
                    <div class="p-6 bg-blue-50 dark:bg-blue-900/10 rounded-3xl border border-blue-100 dark:border-blue-900/30 text-center animate-in fade-in duration-700">
                        <p class="text-[10px] font-black text-blue-700 dark:text-blue-300 uppercase tracking-wider">🏦 Rendimento estimado mensal</p>
                        <p class="text-4xl font-black text-blue-700 dark:text-blue-300 mt-2 tracking-tighter italic">
                            {{ number_format((float)$recAmount, 2, ',', ' ') }} €
                        </p>
                        <p class="text-[9px] text-blue-600/60 dark:text-blue-400/50 mt-1 uppercase font-bold tracking-widest">
                            Valor livre após custos operacionais
                        </p>
                    </div>

                    {{-- DIA DE RECOLHA + DESTINO --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia da Cobrança</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-blue-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-blue-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-blue-500/20 transition-all">
                        Ativar Rendimento 🏢
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TRABALHO INDEPENDENTE / RECIBOS VERDES                 --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-freelance.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-freelance.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveFreelance" class="flex flex-col {{ !empty($onboardingModal) ? 'max-h-[90vh]' : '' }}">
                @include('livewire.partials.onboarding-validation-errors')
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-sky-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="computer-desktop" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Trabalho Independente</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Recibos Verdes / Prestação de Serviços</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="min-h-0 flex-1 p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Recibos Verdes - Cliente X" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-sky-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-sky-600 z-10">Atividade</label>
                            <input type="text" wire:model="recFreelanceActivity" placeholder="Ex: Designer, Programador..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-sky-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Tipo de Rendimento</label>
                            <select wire:model="recFreelanceType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="prestacao_servicos">Prestação de Serviços</option>
                                <option value="vendas">Vendas</option>
                                <option value="consultoria">Consultoria</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Faturação Média (€)</label>
                            <input type="number" step="0.01" wire:model.live="recFreelanceGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none focus:border-emerald-500 transition-all">
                        </div>                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Despesas Médias (€)</label>
                            <input type="number" step="0.01" wire:model.live="recFreelanceExpenses" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Retenção na Fonte (€)</label>
                            <input type="number" step="0.01" wire:model.live="recFreelanceWithholding" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recFreelanceFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                                <option value="variavel">Variável</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-6 bg-sky-50 dark:bg-sky-900/10 rounded-3xl border border-sky-100 dark:border-sky-900/30 text-center">
                        <p class="text-[10px] font-black text-sky-700 dark:text-sky-300 uppercase tracking-wider">💡 Rendimento estimado</p>
                        <p class="text-4xl font-black text-sky-700 dark:text-sky-300 mt-2 tracking-tighter italic">
                            {{ number_format((float)$recAmount, 2, ',', ' ') }} €
                        </p>
                        <p class="text-[9px] text-sky-600/60 dark:text-sky-400/50 mt-1 uppercase font-bold tracking-widest">
                            Faturação − despesas − retenção
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-sky-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-sky-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-sky-500/20 transition-all">
                        Confirmar Rendimento 💻
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: RENDIMENTOS DE INVESTIMENTOS / DIVIDENDOS              --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-investimento.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-investimento.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveInvestment" class="flex flex-col {{ !empty($onboardingModal) ? 'max-h-[90vh]' : '' }}">
                @include('livewire.partials.onboarding-validation-errors')
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-violet-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="chart-bar" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Rendimento de Investimento</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Dividendos, Juros e Mais-Valias</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="min-h-0 flex-1 p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Dividendos - ETF S&P 500" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-violet-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-violet-600 z-10">Tipo de Rendimento</label>
                            <select wire:model="recInvestmentType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="dividendos">💰 Dividendos</option>
                                <option value="juros">🏦 Juros</option>
                                <option value="mais_valias">📊 Mais-valias</option>
                                <option value="fundos">📈 Fundos / ETFs</option>
                                <option value="cripto">₿ Criptoativos</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Investimento / Entidade</label>
                            <input type="text" wire:model="recInvestmentName" placeholder="Ex: ETF S&P 500, Banco X..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Rendimento (€)</label>
                            <input type="number" step="0.01" wire:model.live="recInvestmentAmount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recInvestmentFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="semestral">Semestral</option>
                                <option value="anual">Anual</option>
                                <option value="variavel">Variável</option>
                            </select>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-red-400 z-10">Custos / Comissões (€)</label>
                        <input type="number" step="0.01" wire:model.live="recInvestmentExpenses" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none focus:border-red-500 transition-all">
                    </div>

                    <div class="p-6 bg-violet-50 dark:bg-violet-900/10 rounded-3xl border border-violet-100 dark:border-violet-900/30 text-center">
                        <p class="text-[10px] font-black text-violet-700 dark:text-violet-300 uppercase tracking-wider">📈 Rendimento líquido estimado</p>
                        <p class="text-4xl font-black text-violet-700 dark:text-violet-300 mt-2 tracking-tighter italic">
                            {{ number_format((float)$recAmount, 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-violet-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-violet-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-violet-600 hover:bg-violet-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-violet-500/20 transition-all">
                        Confirmar Investimento 📈
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: REFORMA / PENSÃO DE VELHICE                            --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-reforma.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-reforma.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="savePension" class="flex flex-col {{ !empty($onboardingModal) ? 'max-h-[90vh]' : '' }}">
                @include('livewire.partials.onboarding-validation-errors')
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-rose-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="user-circle" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Reforma / Pensão</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Registo de Pensão de Velhice ou Invalidez</p>                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="min-h-0 flex-1 p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Pensão de Velhice - Segurança Social" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-rose-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-rose-600 z-10">Tipo de Pensão</label>
                            <select wire:model="recPensionType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="velhice">👴 Pensão de Velhice</option>
                                <option value="invalidez">♿ Pensão de Invalidez</option>
                                <option value="sobrevivencia">❤️ Pensão de Sobrevivência</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Entidade Pagadora</label>
                            <input type="text" wire:model="recPensionEntity" placeholder="Ex: Segurança Social" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Valor Bruto (€)</label>
                            <input type="number" step="0.01" wire:model="recPensionGross" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-zinc-900 dark:text-white outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor Líquido (€)</label>
                            <input type="number" step="0.01" wire:model.live="recPensionAmount" placeholder="0,00" class="w-full h-14 bg-emerald-50 dark:bg-emerald-900/10 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-rose-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-rose-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-rose-500/20 transition-all">
                        Confirmar Pensão 👴
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: BOLSA DE ESTUDO / APOIO À FORMAÇÃO                     --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-bolsa.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-bolsa.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveScholarship" class="flex flex-col {{ !empty($onboardingModal) ? 'max-h-[90vh]' : '' }}">
                @include('livewire.partials.onboarding-validation-errors')
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-amber-600 rounded-2xl text-white shadow-lg">
                        <flux:icon name="academic-cap" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Bolsa de Estudo</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Apoio à Formação / Investigação</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>

                <div class="min-h-0 flex-1 p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Descrição</label>
                        <input type="text" wire:model="recDescription" placeholder="Ex: Bolsa de Investigação - FCT" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-amber-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-amber-600 z-10">Tipo de Bolsa</label>
                            <select wire:model="recScholarshipType" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="estudo">🎓 Bolsa de Estudo</option>
                                <option value="formacao">📚 Apoio à Formação</option>
                                <option value="investigacao">🔬 Bolsa de Investigação</option>
                                <option value="estagio">💼 Bolsa de Estágio</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Entidade</label>
                            <input type="text" wire:model="recScholarshipEntity" placeholder="Ex: Universidade, IEFP..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor (€)</label>
                            <input type="number" step="0.01" wire:model.live="recScholarshipAmount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recScholarshipFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Data de Fim (opcional)</label>
                            <input type="date" wire:model="recScholarshipEndDate" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-lg font-black text-center text-zinc-900 dark:text-white">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-amber-600 z-10">Onde entra este valor?</label>
                        <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-amber-500 transition-all">
                            <option value="">💵 Dinheiro Físico</option>
                            @foreach($this->bankAccounts as $acc)
                                <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-amber-500/20 transition-all">
                        Confirmar Bolsa 🎓
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL: OUTRA FONTE DE RENDIMENTO                              --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-show-outro.window="show()"
    x-on:{{ !empty($onboardingModal) ? 'onboarding-' : '' }}modal-close-outro.window="close()"
    x-on:keydown.escape.window="close()"
>
    <div x-show="open" x-cloak x-transition.opacity @click="close()" class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"></div>

    <div x-show="open" x-cloak @click.self="close()" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">
        <div x-show="open" x-transition.scale class="relative z-10 w-full max-w-2xl rounded-[2.5rem] shadow-xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">

            <form wire:submit.prevent="saveOther" class="flex flex-col {{ !empty($onboardingModal) ? 'max-h-[90vh]' : '' }}">
                @include('livewire.partials.onboarding-validation-errors')
                <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
                    <div class="p-3 bg-zinc-900 dark:bg-white rounded-2xl text-white dark:text-zinc-900 shadow-lg">
                        <flux:icon name="sparkles" class="size-6" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-black uppercase italic text-zinc-900 dark:text-white leading-none">Outra Fonte</h2>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">Subsídio, Mesada, Comissão...</p>
                    </div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-600"><flux:icon name="x-mark" /></button>
                </div>
                <div class="min-h-0 flex-1 p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar text-left">

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-600 z-10">Nome da Fonte</label>
                        <input type="text" wire:model="recOtherSourceDetail" placeholder="Ex: Subsídio, Mesada, Pensão, Comissão..." class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:border-zinc-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-emerald-600 z-10">Valor (€)</label>
                            <input type="number" step="0.01" wire:model.live="recOtherAmount" placeholder="0,00" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-emerald-500/30 rounded-2xl px-5 text-lg font-black text-emerald-600 outline-none">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Periodicidade</label>
                            <select wire:model="recOtherFrequency" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none">
                                <option value="mensal">Mensal</option>
                                <option value="semanal">Semanal</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                                <option value="variavel">Variável</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="recDay" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-xl font-black text-center text-zinc-900 dark:text-white">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-600 z-10">Onde entra este valor?</label>
                            <select wire:model="recBankAccountId" class="w-full h-14 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl px-5 text-sm font-bold dark:text-white outline-none focus:border-zinc-500 transition-all">
                                <option value="">💵 Dinheiro Físico</option>
                                @foreach($this->bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">🏦 {{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                    <button type="button" @click="close()" class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px] text-zinc-400 hover:bg-zinc-50 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] h-14 bg-zinc-900 dark:bg-white hover:opacity-90 text-white dark:text-zinc-900 rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl transition-all">
                        Confirmar Rendimento ✨
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



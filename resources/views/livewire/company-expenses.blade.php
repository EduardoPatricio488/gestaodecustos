<div class="space-y-10 pb-20">
    {{-- 1. HEADER CORPORATIVO (ESTILO SaaS PREMIUM) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="building-office-2" class="w-10 h-10 text-brand-600" />
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">Custos de Empresa</h1>
                    <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Operações & OpEx</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Gestão de faturação de fornecedores e otimização fiscal</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            {{-- BOTÃO NOVA DESPESA --}}
            <flux:modal.trigger name="add-company-expense-modal">
                <flux:button variant="primary" icon="plus" wire:click="resetForm" x-on:click="$dispatch('modal-show', { name: 'add-company-expense-modal' })" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
    Nova Despesa
</flux:button>
            </flux:modal.trigger>

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
            <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
        </div>
    </div>

    {{-- 2. KPI CARDS (VISUAL ANALYTICS COM PRIVACIDADE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Gasto Total --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-red-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-2xl text-red-600">
                    <flux:icon name="banknotes" variant="outline" class="size-6" />
                </div>
                <span class="text-[9px] font-black text-red-500 bg-red-50 dark:bg-red-500/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">Saída Real</span>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Gasto Bruto (Mês)</p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($totalMonth, 2, ',', ' ') }} €
                </span>
            </h3>
            <flux:icon name="chart-bar" class="absolute -right-4 -bottom-4 size-24 text-zinc-50 dark:text-zinc-800 group-hover:scale-110 transition-transform opacity-50" />
        </div>

        {{-- IVA Dedutível --}}
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group transition-all hover:border-emerald-500/30">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-600">
                    <flux:icon name="receipt-percent" variant="outline" class="size-6" />
                </div>
                <span class="text-[9px] font-black text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">Recuperável</span>
            </div>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">IVA Dedutível</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ number_format($totalVat, 2, ',', ' ') }} €
                </span>
            </h3>
        </div>

        {{-- Volume de Docs --}}
        <div class="glass-card relative overflow-hidden bg-zinc-50 dark:bg-zinc-800/50 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-700 shadow-sm group transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-zinc-200 dark:bg-zinc-700 rounded-2xl text-zinc-500">
                    <flux:icon name="folder-open" variant="outline" class="size-6" />
                </div>
            </div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Audit Fiscal</p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                    {{ $expenses->total() }}
                </span>
                <span class="text-sm text-zinc-400 font-bold uppercase ml-2 tracking-widest">Docs</span>
            </h3>
        </div>
    </div>

    {{-- 3. LEDGER DE FATURAÇÃO (ESTILO SaaS LEDGER) --}}
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-900/30">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Fluxo de Saída de Capital</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter text-zinc-800 dark:text-zinc-200">Histórico de Custos Operacionais</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm">{{ $expenses->total() }} Registos</flux:badge>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-widest">
                        <th class="p-6">Data de Emissão</th>
                        <th class="p-6">Fornecedor / Descrição</th>
                        <th class="p-6 text-center">Classificação</th>
                        <th class="p-6 text-right">IVA (Audit)</th>
                        <th class="p-6 text-right px-10">Total do Custo</th>
                        <th class="p-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($expenses as $exp)
                       <tr wire:key="exp-row-{{ $exp->id }}"
    class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row relative hover:z-50">

    <td class="p-6">
        <div class="flex flex-col text-left">
            <span class="text-lg font-black dark:text-white leading-none tracking-tighter">{{ \Carbon\Carbon::parse($exp->spent_at)->format('d') }}</span>
            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mt-1">{{ \Carbon\Carbon::parse($exp->spent_at)->translatedFormat('M, Y') }}</span>
        </div>
    </td>

    <td class="p-6 text-left">
        <div class="flex flex-col">
            <span class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-tight">{{ $exp->title }}</span>
            @if($exp->description)
                <span class="text-[10px] text-zinc-500 font-bold italic mt-0.5 max-w-[200px] truncate">"{{ $exp->description }}"</span>
            @endif
        </div>
    </td>

    <td class="p-6 text-center text-xs">
        <span class="inline-flex px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-black uppercase text-[8px] tracking-widest rounded-xl border border-zinc-200 dark:border-zinc-700">
            {{ $exp->category->name }}
        </span>
    </td>

    <td class="p-6 text-right font-bold text-xs text-zinc-500 italic">
        <span :class="privacyMode ? 'blur-sm select-none' : ''">
            {{ number_format($exp->vat_amount, 2, ',', ' ') }} €
        </span>
    </td>

    <td class="p-6 text-right px-10 align-middle">
        <span class="text-xl font-black text-red-500 tracking-tighter italic">
            <span :class="privacyMode ? 'blur-md select-none' : ''">
                -{{ number_format($exp->amount, 2, ',', ' ') }} €
            </span>
        </span>
    </td>

    {{-- COLUNA DE AÇÕES CORRIGIDA --}}
    <td class="p-6 text-right pr-8" x-data="{ optionsOpen: false }">
        <div class="relative inline-block text-left">
            <button type="button" @click.stop="optionsOpen = !optionsOpen"
                    class="p-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 opacity-0 group-hover/row:opacity-100">
                <flux:icon name="ellipsis-horizontal" class="size-5" />
            </button>

            {{-- MENU COM Z-INDEX 100 --}}
            <div x-show="optionsOpen"
                 x-cloak
                 @click.outside="optionsOpen = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 {{-- O z-[100] garante que fica à frente de tudo --}}
                 class="absolute right-0 top-10 w-48 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-2xl z-[100] overflow-hidden text-left ring-1 ring-black/5">

                <div class="p-1.5 space-y-0.5">
                    <button type="button" wire:click="edit({{ $exp->id }})" @click="optionsOpen = false"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-brand-50 hover:text-brand-600 transition-all">
                        <flux:icon name="pencil-square" class="size-4 text-brand-500" /> Editar
                    </button>

                    <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>

                    <button type="button" wire:click="delete({{ $exp->id }})" wire:confirm="Eliminar registo?" @click="optionsOpen = false"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 transition-all">
                        <flux:icon name="trash" class="size-4 text-red-500" /> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </td>
</tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-24 text-center">
                                <div class="flex flex-col items-center justify-center gap-4">
                                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                                        <flux:icon name="magnifying-glass" class="size-12 text-zinc-200 dark:text-zinc-700" />
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">Arquivo Vazio</p>
                                        <p class="text-zinc-400 text-xs italic font-medium">Sem despesas empresariais registadas.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/20">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    {{-- 4. MODAL: REGISTO DE CUSTO (DESIGN ENTERPRISE) --}}
    <flux:modal name="add-company-expense-modal" position="center" class="md:w-[550px] !p-0 overflow-visible">
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-900 dark:bg-brand-600 rounded-2xl text-white shadow-lg">
                    <flux:icon name="document-text" class="size-6" />
                </div>
                <div>
                   <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
    {{ $editingId ? 'Editar Custo' : 'Registo de Custo' }}
</flux:heading>
                    <p class="text-xs text-zinc-400 font-medium">Introduz os dados da fatura para controlo de OpEx.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Campo: Fornecedor --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Identificação do Fornecedor</flux:label>
                    <flux:input
                        wire:model="title"
                        placeholder="Ex: Staples, Galp, Amazon Business..."
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner"
                    />
                </div>

                {{-- Linha: Valor e Data --}}
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Valor Total c/ IVA (€)</flux:label>
                        <flux:input
                            wire:model.live="amount"
                            type="number"
                            step="0.01"
                            class="font-black text-2xl text-red-500 !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-16 shadow-inner"
                            placeholder="0,00"
                        />
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Data de Emissão</flux:label>
                        <flux:input wire:model="spent_at" type="date" class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-16 shadow-inner" />
                    </div>
                </div>

                {{-- Área de Classificação (Metadata e Categoria) --}}
                <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Classificação</flux:label>
                            <flux:select wire:model="category_id" class="font-black uppercase text-[10px] !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                                <option value="">Escolha...</option>
                                @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                            </flux:select>
                        </div>
                        <div class="space-y-2">
                            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">IVA Dedutível (€)</flux:label>
                            <flux:input
                                wire:model="vat_amount"
                                type="number"
                                step="0.01"
                                placeholder="0,00"
                                class="font-black text-emerald-600 !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Notas do Documento</flux:label>
                        <flux:textarea wire:model="description" rows="2" placeholder="Opcional: Detalhes do serviço ou produto..." class="rounded-2xl shadow-sm border-none !bg-white dark:!bg-zinc-950" />
                    </div>
                </div>
            </div>

            {{-- Ações Finais --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="saveExpense" variant="primary" class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
    {{ $editingId ? 'Atualizar Lançamento' : 'Confirmar Lançamento' }}
</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Custos Operacionais
        </p>
    </footer>
</div>

<div
    class="fixed inset-0 z-[999] flex items-center justify-center p-4"
    x-data="{ open: @entangle('show') }"
    x-show="open"
    x-cloak
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Painel --}}
    <div class="relative w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden"
         style="max-height: 90vh;">

        {{-- Barra de progresso no topo --}}
        <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800">
            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-700 ease-out rounded-full"
                 style="width: {{ (($step - 1) / ($totalSteps - 1)) * 100 }}%"></div>
        </div>

        <div class="overflow-y-auto custom-scrollbar" style="max-height: calc(90vh - 6px)">

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 1: BOAS-VINDAS                --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 1)
            <div class="p-10 space-y-8">
                <div class="text-center space-y-4">
                    <div class="size-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl shadow-indigo-500/30">
                        <flux:icon name="sparkles" class="size-10 text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-black italic tracking-tighter dark:text-white">
                            Bem-vindo, {{ auth()->user()->name }}! 👋
                        </h1>
                        <p class="text-zinc-500 font-medium mt-2">Vamos configurar o teu espaço financeiro em menos de 2 minutos.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/30 rounded-2xl p-5">
                        <flux:icon name="chart-bar-square" class="size-7 text-indigo-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Dashboard Financeiro</p>
                        <p class="text-xs text-zinc-500 mt-1">Visão completa das tuas receitas, despesas e investimentos.</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl p-5">
                        <flux:icon name="arrow-trending-up" class="size-7 text-emerald-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Controlo de Receitas</p>
                        <p class="text-xs text-zinc-500 mt-1">Regista salários, rendimentos extra e acompanha tudo.</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 rounded-2xl p-5">
                        <flux:icon name="sparkles" class="size-7 text-amber-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Scanner IA de Faturas</p>
                        <p class="text-xs text-zinc-500 mt-1">Fotografa qualquer fatura e a IA extrai os dados automaticamente.</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/30 rounded-2xl p-5">
                        <flux:icon name="globe-alt" class="size-7 text-purple-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Finance Connect</p>
                        <p class="text-xs text-zinc-500 mt-1">Rede social financeira — partilha conquistas e segue amigos.</p>
                    </div>
                    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800/30 rounded-2xl p-5">
                        <flux:icon name="trophy" class="size-7 text-rose-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Metas & Gamificação</p>
                        <p class="text-xs text-zinc-500 mt-1">Define objetivos, ganha XP e sobe de nível financeiro.</p>
                    </div>
                    <div class="bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800/30 rounded-2xl p-5">
                        <flux:icon name="building-office" class="size-7 text-sky-500 mb-3" />
                        <p class="text-sm font-black dark:text-white">Área Empresarial</p>
                        <p class="text-xs text-zinc-500 mt-1">Faturação, clientes, P&L e gestão completa de negócio.</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Passo 1 de {{ $totalSteps }}</p>
                    <button wire:click="nextStep"
                        class="flex items-center gap-2 px-8 h-12 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-indigo-500/20 transition-all hover:scale-[1.02]">
                        Começar
                        <flux:icon name="arrow-right" class="size-4" />
                    </button>
                </div>
            </div>
            @endif

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 2: ORDENADO                   --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 2)
            <div class="p-10 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                        <flux:icon name="banknotes" class="size-7 text-white" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">Qual é o teu ordenado?</h2>
                        <p class="text-sm text-zinc-500 mt-0.5">Vamos registar o teu rendimento fixo mensal para calcular o teu orçamento.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">Descrição</label>
                        <input type="text" wire:model="salaryDescription" placeholder="Ex: Salário Mensal - Empresa X"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">Valor Líquido (€)</label>
                            <input type="number" step="0.01" wire:model="salaryAmount" placeholder="0,00"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600 outline-none transition-all">
                        </div>
                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">Dia de Recebimento</label>
                            <input type="number" min="1" max="31" wire:model="salaryDay"
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-xl font-black text-center dark:text-white outline-none transition-all">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">Fonte de Rendimento</label>
                        <select wire:model="salarySource"
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none">
                            <option value="emprego">💼 Emprego por conta de outrem</option>
                            <option value="freelance">💻 Trabalho Independente / Freelance</option>
                            <option value="investimento">📈 Rendimentos de Investimentos</option>
                            <option value="outro">✨ Outro</option>
                        </select>
                    </div>
                </div>

                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl p-4 flex items-start gap-3">
                    <flux:icon name="shield-check" class="size-5 text-emerald-500 shrink-0 mt-0.5" />
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">Os teus dados são privados e só tu (e quem convidares para o teu workspace) os pode ver.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Passo 2 de {{ $totalSteps }}</p>
                    <div class="flex items-center gap-3">
                        <button wire:click="skipStep"
                            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors">
                            Saltar
                        </button>
                        <button wire:click="nextStep"
                            class="flex items-center gap-2 px-8 h-12 bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02]">
                            Guardar e Continuar
                            <flux:icon name="arrow-right" class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 3: WORKSPACE                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 3)
            <div class="p-10 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
                        <flux:icon name="user-group" class="size-7 text-white" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">O teu espaço financeiro</h2>
                        <p class="text-sm text-zinc-500 mt-0.5">Dá um nome ao teu workspace — pode ser só para ti ou partilhado com família/sócios.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-indigo-600 z-10">Nome do Workspace</label>
                        <input type="text" wire:model="workspaceName"
                            placeholder="Ex: Família Silva, As Minhas Finanças, Startup XYZ..."
                            class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-indigo-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button type="button" wire:click="$set('workspaceName', 'As Minhas Finanças')"
                            class="p-4 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 rounded-2xl text-left transition-all group">
                            <p class="text-lg mb-1">👤</p>
                            <p class="text-xs font-black dark:text-white group-hover:text-indigo-600 transition-colors">Pessoal</p>
                            <p class="text-[10px] text-zinc-400">Só para mim</p>
                        </button>
                        <button type="button" wire:click="$set('workspaceName', 'Família')"
                            class="p-4 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 rounded-2xl text-left transition-all group">
                            <p class="text-lg mb-1">👨‍👩‍👧</p>
                            <p class="text-xs font-black dark:text-white group-hover:text-indigo-600 transition-colors">Familiar</p>
                            <p class="text-[10px] text-zinc-400">Partilhado com família</p>
                        </button>
                        <button type="button" wire:click="$set('workspaceName', 'O Meu Negócio')"
                            class="p-4 bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 rounded-2xl text-left transition-all group">
                            <p class="text-lg mb-1">🏢</p>
                            <p class="text-xs font-black dark:text-white group-hover:text-indigo-600 transition-colors">Empresarial</p>
                            <p class="text-[10px] text-zinc-400">Para o meu negócio</p>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Passo 3 de {{ $totalSteps }}</p>
                    <div class="flex items-center gap-3">
                        <button wire:click="skipStep"
                            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors">
                            Saltar
                        </button>
                        <button wire:click="nextStep"
                            class="flex items-center gap-2 px-8 h-12 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-indigo-500/20 transition-all hover:scale-[1.02]">
                            Continuar
                            <flux:icon name="arrow-right" class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 4: CATEGORIA                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 4)
            <div class="p-10 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="size-14 bg-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/20 shrink-0">
                        <flux:icon name="tag" class="size-7 text-white" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">Primeira categoria de despesa</h2>
                        <p class="text-sm text-zinc-500 mt-0.5">Cria uma categoria para organizar os teus gastos. Podes adicionar mais depois.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach([
                            ['name' => 'Alimentação', 'color' => '#f97316', 'emoji' => '🛒'],
                            ['name' => 'Transporte', 'color' => '#3b82f6', 'emoji' => '🚗'],
                            ['name' => 'Saúde', 'color' => '#ef4444', 'emoji' => '❤️'],
                            ['name' => 'Lazer', 'color' => '#8b5cf6', 'emoji' => '🎉'],
                            ['name' => 'Educação', 'color' => '#10b981', 'emoji' => '📚'],
                            ['name' => 'Casa', 'color' => '#0ea5e9', 'emoji' => '🏠'],
                        ] as $suggestion)
                            <button type="button"
                                wire:click="$set('categoryName', '{{ $suggestion['name'] }}'); $set('categoryColor', '{{ $suggestion['color'] }}')"
                                class="p-3 bg-zinc-50 dark:bg-zinc-900 border-2 {{ $categoryName === $suggestion['name'] ? 'border-purple-500' : 'border-zinc-200 dark:border-zinc-800' }} hover:border-purple-400 rounded-2xl text-center transition-all">
                                <p class="text-xl mb-1">{{ $suggestion['emoji'] }}</p>
                                <p class="text-xs font-black dark:text-white">{{ $suggestion['name'] }}</p>
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="h-px flex-1 bg-zinc-100 dark:bg-zinc-800"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">ou cria a tua</span>
                        <div class="h-px flex-1 bg-zinc-100 dark:bg-zinc-800"></div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="relative flex-1">
                            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-purple-600 z-10">Nome da Categoria</label>
                            <input type="text" wire:model="categoryName" placeholder="Ex: Subscriçõess, Pets, Viagens..."
                                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-purple-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                        </div>
                        <div class="relative shrink-0">
                            <label class="absolute left-1/2 -translate-x-1/2 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10 whitespace-nowrap">Cor</label>
                            <input type="color" wire:model="categoryColor"
                                class="size-16 rounded-2xl border-2 border-zinc-200 dark:border-zinc-800 cursor-pointer p-1 bg-zinc-50 dark:bg-zinc-900">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Passo 4 de {{ $totalSteps }}</p>
                    <div class="flex items-center gap-3">
                        <button wire:click="skipStep"
                            class="px-5 h-12 text-zinc-400 hover:text-zinc-600 font-bold uppercase text-xs tracking-widest transition-colors">
                            Saltar
                        </button>
                        <button wire:click="nextStep"
                            class="flex items-center gap-2 px-8 h-12 bg-purple-600 hover:bg-purple-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-lg shadow-purple-500/20 transition-all hover:scale-[1.02]">
                            Continuar
                            <flux:icon name="arrow-right" class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 5: CONCLUÍDO                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 5)
            <div class="p-10 space-y-8 text-center">
                <div class="space-y-4">
                    <div class="size-24 bg-gradient-to-br from-emerald-400 to-indigo-600 rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl shadow-indigo-500/30 animate-bounce">
                        <flux:icon name="check-circle" class="size-12 text-white" />
                    </div>
                    <div>
                        <h2 class="text-3xl font-black italic tracking-tighter dark:text-white">Tudo pronto! 🎉</h2>
                        <p class="text-zinc-500 font-medium mt-2">O teu espaço financeiro está configurado. Agora é hora de explorar!</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-left">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="p-5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/30 rounded-2xl hover:border-indigo-400 transition-all group">
                        <flux:icon name="squares-2x2" class="size-6 text-indigo-500 mb-2" />
                        <p class="text-sm font-black dark:text-white group-hover:text-indigo-600 transition-colors">Dashboard</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Ver resumo financeiro</p>
                    </a>
                    <a href="{{ route('hub.incomes') }}" wire:navigate
                        class="p-5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl hover:border-emerald-400 transition-all group">
                        <flux:icon name="arrow-trending-up" class="size-6 text-emerald-500 mb-2" />
                        <p class="text-sm font-black dark:text-white group-hover:text-emerald-600 transition-colors">Receitas</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Gerir rendimentos</p>
                    </a>
                    <a href="{{ route('social.hub') }}" wire:navigate
                        class="p-5 bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/30 rounded-2xl hover:border-purple-400 transition-all group">
                        <flux:icon name="globe-alt" class="size-6 text-purple-500 mb-2" />
                        <p class="text-sm font-black dark:text-white group-hover:text-purple-600 transition-colors">Finance Connect</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Rede social financeira</p>
                    </a>
                </div>

                <div class="pt-2">
                    <button wire:click="completeOnboarding"
                        class="w-full h-14 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black uppercase tracking-widest text-sm rounded-2xl shadow-2xl shadow-indigo-500/20 transition-all hover:scale-[1.02]">
                        🚀 Entrar na Plataforma
                    </button>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

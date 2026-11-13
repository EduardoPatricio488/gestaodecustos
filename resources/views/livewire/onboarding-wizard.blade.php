<div
    class="fixed inset-0 z-[999] flex items-center justify-center p-4"
    x-data="{ open: @entangle('show'), wide: false }"
    x-on:onboarding-statement-import-opened.window="wide = true"
    x-show="open"
    x-cloak
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Painel --}}
    <div class="relative w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden"
         :class="wide ? '!max-w-5xl' : ''"
         style="max-height: 90vh;">

        {{-- Barra de progresso no topo --}}
        <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800">
            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-700 ease-out rounded-full"
                 style="width: {{ (($step - 1) / ($totalSteps - 1)) * 100 }}%"></div>
        </div>

        <div class="overflow-y-auto custom-scrollbar" style="max-height: calc(90vh - 6px)">
            @php
                $stepEncouragement = [
                    1 => [
                        'Grandes planos começam com um pequeno passo — e, felizmente, sem calculadora olímpica.',
                        'Preparado para pôr as finanças em ordem? Prometemos não pedir flexões!',
                        'Vamos dar um GPS ao teu dinheiro. Sem recalcular a rota a cada café.',
                    ],
                    2 => [
                        'Um bom nome para o teu espaço: mais fácil de encontrar do que as chaves de manhã.',
                        'Dá um nome ao teu espaço financeiro. «Fortuna secreta» também é uma opção!',
                        'O teu dinheiro merece uma morada com estilo — sem renda nem condomínio.',
                    ],
                    3 => [
                        'Dá uma casa ao teu dinheiro. Ele promete não deixar as meias espalhadas!',
                        'Contas organizadas, dinheiro localizado — as moedas do sofá continuam por tua conta.',
                        'Adiciona as tuas contas: até o mealheiro merece um lugar na equipa.',
                    ],
                    4 => [
                        'Vamos conhecer os teus rendimentos — os euros também gostam de se apresentar.',
                        'Configura o teu salário: fazer contas aos impostos sem calculadora é que era castigo.',
                        'Dá os detalhes do salário e deixa as contas connosco. O café continua a ser contigo.',
                    ],
                    5 => [
                        'Toda a ajuda conta — até os euros gostam de ter mais do que uma porta de entrada.',
                        'Rendas, trabalhos extra ou pensões: os teus rendimentos querem fazer parte da festa.',
                        'Cada fonte de rendimento conta. Até os euros gostam de trabalhar em equipa.',
                    ],
                    6 => [
                        'O dinheiro não cresce em árvores, mas um portefólio organizado já é um bom começo.',
                        'Regista os teus investimentos: acompanhar o crescimento é melhor do que olhar para um vaso vazio.',
                        'A tua carteira de investimentos merece atenção — e não ocupa espaço no bolso.',
                    ],
                    7 => [
                        'Traz o extrato: hoje os teus movimentos não precisam de jogar às escondidas.',
                        'Importa o extrato e deixa os movimentos aparecerem. Os recibos já se podem reformar.',
                        'Um extrato de cada vez: até os movimentos bancários precisam de ser apresentados.',
                    ],
                    8 => [
                        'Vamos conhecer as assinaturas antes que uma delas se renove em modo ninja.',
                        'Regista as tuas assinaturas: até o streaming merece um papel nos créditos.',
                        'Descobre para onde vão os pagamentos recorrentes — sem caça ao tesouro no extrato.',
                    ],
                    9 => [
                        'Explora os teus hubs: organizar despesas é menos assustador do que procurar aquele recibo.',
                        'Há um hub para cada plano. Não precisas de um mapa do tesouro para os encontrar.',
                        'Explora as ferramentas ao teu ritmo: nenhuma foge se fizeres uma pausa para café.',
                    ],
                    10 => [
                        'Missão cumprida! O teu dinheiro já tem mapa — agora falta-lhe só não fugir para os saldos.',
                        'Tudo pronto! As tuas finanças já têm um ponto de partida e tu mereces um aplauso.',
                        'Chegaste ao fim da configuração. A calculadora agradece o trabalho em equipa!',
                    ],
                ];
            @endphp
            @if(isset($stepEncouragement[$step], $encouragementIndexes[$step]))
                <p class="mx-5 mt-5 flex items-start gap-3 rounded-2xl bg-indigo-50 px-4 py-3 text-sm font-semibold leading-6 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 sm:mx-9">
                    <flux:icon name="sparkles" class="mt-0.5 size-5 shrink-0" aria-hidden="true" />
                    <span data-onboarding-encouragement>{{ $stepEncouragement[$step][$encouragementIndexes[$step]] }}</span>
                </p>
            @endif

            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 1: BOAS-VINDAS                --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 1)
            <div class="p-6 sm:p-9 space-y-6">
                <div class="flex items-center justify-between gap-4">
                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-indigo-700 dark:text-indigo-300">
                        <flux:icon name="sparkles" class="size-4" />
                        Primeiros passos
                    </span>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Passo 1 de {{ $totalSteps }}</span>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                        Bem-vindo, {{ auth()->user()->name }}! 👋
                    </h1>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Vamos preparar o teu espaço financeiro. Podes saltar alguns passos e completar os dados mais tarde.
                    </p>
                </div>

                <div>
                    <h2 class="mb-3 text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">O que podes explorar</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div class="flex items-start gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-3.5">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400"><flux:icon name="chart-bar-square" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Visão financeira</p><p class="mt-0.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">Acompanha receitas, despesas e investimentos.</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-3.5">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400"><flux:icon name="arrow-trending-up" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Receitas e despesas</p><p class="mt-0.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">Organiza salários, rendimentos e gastos.</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-3.5">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400"><flux:icon name="sparkles" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Ferramentas de IA</p><p class="mt-0.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">Analisa faturas e consulta o CFO Inteligente.</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-3.5">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400"><flux:icon name="trophy" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Metas e progresso</p><p class="mt-0.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">Define objetivos e acompanha as tuas conquistas.</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-3.5">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-500/15 text-purple-600 dark:text-purple-400"><flux:icon name="globe-alt" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Finance Connect</p><p class="mt-0.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">Partilha conquistas e segue outros membros.</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-3.5">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-500/15 text-sky-700 dark:text-sky-400"><flux:icon name="building-office" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Área empresarial</p><p class="mt-0.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">Gere o teu negócio com o plano Business.</p></div>
                        </div>
                    </div>
                </div>

                @include('livewire.partials.onboarding-footer', [
                    'footerStep' => $step, 'footerTotal' => $totalSteps,
                    'footerPrevious' => null, 'footerSkip' => null,
                    'footerNext' => 'nextStep', 'footerSubmit' => false,
                    'footerNextLabel' => 'Começar configuração', 'footerLoadingTarget' => 'nextStep',
                ])
            </div>
            @endif










{{-- ─────────────────────────────────── --}}
{{-- PASSO 3: CONTAS BANCÁRIAS           --}}
{{-- ─────────────────────────────────── --}}
@if($step === 3)
<div class="p-6 sm:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <div class="size-14 bg-sky-600 rounded-2xl flex items-center justify-center shadow-lg shadow-sky-500/20 shrink-0">
            <flux:icon name="building-library" class="size-7 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">Onde guardas o teu dinheiro?</h2>
            <p class="text-sm text-zinc-500 mt-0.5">Adiciona uma ou várias contas bancárias, carteiras ou cartões para começares com o saldo certo.</p>
        </div>
    </div>

    @if($savedBankAccounts->isNotEmpty())
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30" role="status">
            <p class="text-sm font-black text-emerald-800 dark:text-emerald-300">
                Já tens {{ $savedBankAccounts->count() }} {{ $savedBankAccounts->count() === 1 ? 'conta guardada' : 'contas guardadas' }}.
            </p>
            <div class="mt-3 space-y-2">
                @foreach($savedBankAccounts as $savedAccount)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white/80 px-3 py-3 text-xs dark:bg-zinc-900/70">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-bold text-zinc-800 dark:text-zinc-100">{{ $savedAccount->name }}</p>
                            <p class="text-zinc-500">{{ $savedAccount->bank_name ?: ucfirst(str_replace('_', ' ', $savedAccount->type)) }}</p>
                            <p class="mt-1 font-black tabular-nums text-zinc-700 dark:text-zinc-200">{{ number_format((float) $savedAccount->balance, 2, ',', ' ') }} {{ $savedAccount->currency }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <button type="button" wire:click="editSavedBankAccount({{ $savedAccount->id }})" class="rounded-lg border border-sky-200 px-3 py-2 font-bold text-sky-700 transition hover:bg-sky-50 dark:border-sky-900 dark:text-sky-300 dark:hover:bg-sky-950">
                                <flux:icon name="pencil" class="mr-1 inline size-3.5" /> Editar
                            </button>
                            <button type="button" wire:click="deleteSavedBankAccount({{ $savedAccount->id }})" wire:confirm="Tens a certeza de que pretendes eliminar esta conta bancária?" class="rounded-lg border border-red-200 px-3 py-2 font-bold text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950">
                                <flux:icon name="trash" class="mr-1 inline size-3.5" /> Eliminar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @foreach($bankAccounts as $index => $account)
            <div wire:key="onboarding-bank-account-{{ $index }}" class="p-5 rounded-2xl border-2 border-zinc-100 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/60 space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black uppercase tracking-widest text-sky-600">Conta {{ $index + 1 }}</p>
                    @if(count($bankAccounts) > 1)
                        <button type="button" wire:click="removeBankAccount({{ $index }})" class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-600">Remover</button>
                    @endif
                </div>
                @php
                    $accountFields = [];
                    foreach (['name', 'type', 'balance', 'bank_name', 'currency', 'account_number', 'iban', 'holder_name', 'swift', 'color', 'alert_below', 'is_business', 'include_in_total'] as $field) {
                        $accountFields[$field] = "bankAccounts.$index.$field";
                    }
                @endphp
                @include('livewire.partials.bank-account-form', [
                    'accountFields' => $accountFields,
                    'modePath' => "bankAccounts.$index.entry_mode", 'entryMode' => $account['entry_mode'],
                    'proofPath' => "ibanProofs.$index", 'proofInputId' => "onboarding-iban-proof-$index",
                    'proofMessage' => $account['proof_message'], 'formColor' => $account['color'],
                ])
            </div>
        @endforeach
        <button type="button" wire:click="addBankAccount" class="w-full h-12 border-2 border-dashed border-sky-200 dark:border-sky-900 text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/20 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">+ Adicionar outra conta</button>
        @error('bankAccounts') <p role="alert" class="text-xs text-red-600">{{ $message }}</p> @enderror
        @error('ibanProofs') <p role="alert" class="text-xs text-red-600">{{ $message }}</p> @enderror
        @if(collect($savedAccountIds)->contains(fn ($id) => $id !== null))
            <p class="text-xs text-zinc-500">Remover uma conta deste passo não apaga uma conta já guardada no Banco.</p>
        @endif
    </div>

    <div class="bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800/30 rounded-2xl p-4 flex items-start gap-3">
        <flux:icon name="shield-check" class="size-5 text-sky-500 shrink-0 mt-0.5" />
        <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">O saldo inicial serve apenas para começar o teu controlo financeiro. Podes alterar estes dados mais tarde.</p>
    </div>

    @include('livewire.partials.onboarding-footer', [
        'footerStep' => $step, 'footerTotal' => $totalSteps,
        'footerPrevious' => 'previousStep', 'footerSkip' => 'skipStep',
        'footerNext' => 'nextStep', 'footerSubmit' => false,
        'footerNextLabel' => 'Guardar e Continuar',
        'footerLoadingTarget' => 'nextStep,'.implode(',', array_map(fn ($index) => 'ibanProofs.'.$index, array_keys($bankAccounts))),
    ])
</div>
@endif

{{-- PASSO 4: FONTE DE RENDIMENTO        --}}
{{-- ─────────────────────────────────── --}}
@if($step === 4)
<div class="p-5 sm:p-10 space-y-8">

    {{-- HEADER --}}
    <div class="flex items-center gap-4">
        <div class="size-14 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
            <flux:icon name="banknotes" class="size-7 text-white" />
        </div>

        <div>
            <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">
                @switch($salarySource)
                    @case('emprego')
                        Quais são os dados do teu salário?
                        @break
                    @case('freelance')
                        Quais são os dados do teu trabalho independente?
                        @break
                    @case('investimento')
                        Quais são os teus rendimentos de investimento?
                        @break
                    @case('imobiliario')
                        Quais são os teus rendimentos imobiliários?
                        @break
                    @case('reforma')
                        Quais são os dados da tua pensão?
                        @break
                    @case('bolsa')
                        Quais são os dados da tua bolsa?
                        @break
                    @default
                        Vamos configurar o teu rendimento
                @endswitch
            </h2>

            <p class="text-sm text-zinc-500 mt-0.5">
                @switch($salarySource)
                    @case('emprego')
                        Regista o teu salário para calcular automaticamente o teu rendimento líquido.
                        @break
                    @case('freelance')
                        Indica quanto recebes habitualmente através do teu trabalho independente.
                        @break
                    @case('investimento')
                        Regista dividendos, juros ou outros rendimentos provenientes dos teus investimentos.
                        @break
                    @case('imobiliario')
                        Adiciona os rendimentos que recebes através de imóveis ou exploração imobiliária.
                        @break
                    @case('reforma')
                        Regista o valor da tua reforma ou pensão.
                        @break
                    @case('bolsa')
                        Indica o valor e a entidade responsável pela tua bolsa ou apoio à formação.
                        @break
                    @default
                        Adiciona qualquer outra fonte de rendimento que tenhas.
                @endswitch
            </p>
        </div>
    </div>


    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl p-4 flex items-start gap-3">
        <flux:icon name="shield-check" class="size-5 text-emerald-500 shrink-0 mt-0.5" />
        <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
            Os teus dados guardados são privados e só tu (e quem convidares para o teu workspace) os pode ver. Se usares o scanner, a fotografia é enviada ao fornecedor de IA.
        </p>
    </div>

    {{-- FONTE DE RENDIMENTO --}}
    <div class="space-y-5">

        <div class="relative">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                Fonte de Rendimento
            </label>

            @if($salarySource === 'emprego')
                <div class="flex w-full items-center rounded-2xl border-2 border-zinc-200 bg-zinc-50 px-5 py-4 text-sm font-bold text-zinc-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200" aria-readonly="true">
                    💼 Emprego (Contrato de trabalho)
                </div>
            @else
                <select
                    wire:model.live="salarySource"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all appearance-none cursor-pointer"
                >
                    <option value="emprego">💼 Emprego (Contrato de trabalho)</option>
                    <option value="freelance">💻 Trabalho Independente / Recibos Verdes</option>
                    <option value="investimento">📈 Rendimentos de Investimentos / Dividendos</option>
                    <option value="imobiliario">🏠 Rendas e Exploração Imobiliária</option>
                    <option value="reforma">👴 Reforma / Pensão de Velhice</option>
                    <option value="bolsa">🎓 Bolsa de Estudo / Apoio à Formação</option>
                    <option value="outro">✨ Outra Fonte</option>
                </select>
            @endif
        </div>


       {{-- ═══════════════════════════════════ --}}
{{-- 💼 EMPREGO                          --}}
{{-- ═══════════════════════════════════ --}}
@if($salarySource === 'emprego')
            @php
                $user = auth()->user();
                $activeWorkspace = $user->current_workspace_id ? $user->workspaces()->whereKey($user->current_workspace_id)->first() : null;
                $canManageOnboarding = $activeWorkspace && ! $user->isViewer()
                    && (! in_array($activeWorkspace->type, ['business', 'company'], true)
                        || app(\App\Services\BusinessAccessService::class)->can('manage_financials', $user, $activeWorkspace));
            @endphp
            @if($canManageOnboarding)
                <livewire:onboarding-salary :salary-id="$savedSalaryId" :footer-total="$totalSteps" :key="'onboarding-salary-'.($savedSalaryId ?? 'new')" />
            @else
                <p class="rounded-2xl bg-rose-50 p-4 text-sm text-rose-700">Não existe um espaço financeiro ativo com permissão para guardar o salário. Podes saltar este passo.</p>
            @endif

        @elseif($salarySource === 'freelance')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Atividade
                    </label>

                    <input
                        type="text"
                        wire:model="freelanceActivity"
                        placeholder="Ex: Designer, Programador..."
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none focus:border-emerald-500"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Tipo de rendimento
                    </label>

                    <select
                        wire:model="freelanceType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                        <option value="prestacao_servicos">Prestação de Serviços</option>
                        <option value="vendas">Vendas</option>
                        <option value="consultoria">Consultoria</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Faturação média (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="freelanceGross"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Despesas médias (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="freelanceExpenses"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Retenção na fonte (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="freelanceWithholding"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="freelanceFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="anual">Anual</option>
                        <option value="variavel">Variável</option>
                    </select>
                </div>

            </div>

            <div class="p-5 bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800/30 rounded-2xl">

                <p class="text-xs font-black text-sky-700 dark:text-sky-300 uppercase tracking-wider">
                    💡 Rendimento estimado
                </p>

                <p class="text-2xl font-black text-sky-700 dark:text-sky-300 mt-1">
{{ number_format(max(0, (float)$freelanceGross - (float)$freelanceExpenses - (float)$freelanceWithholding), 2, ',', ' ') }} €                </p>

                <p class="text-[10px] text-sky-600/70 mt-1">
                    Faturação − despesas − retenção
                </p>

            </div>


        {{-- ═══════════════════════════════════ --}}
        {{-- 📈 INVESTIMENTOS                     --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'investimento')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Tipo de Rendimento
                    </label>

                    <select wire:model="investmentType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="dividendos">💰 Dividendos</option>
                        <option value="juros">🏦 Juros</option>
                        <option value="mais_valias">📊 Mais-valias</option>
                        <option value="fundos">📈 Fundos / ETFs</option>
                        <option value="cripto">₿ Criptoativos</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Investimento / Entidade
                    </label>

                    <input
                        type="text"
                        wire:model="investmentName"
                        placeholder="Ex: ETF S&P 500, Banco X..."
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Rendimento (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="investmentAmount"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="investmentFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="semestral">Semestral</option>
                        <option value="anual">Anual</option>
                        <option value="variavel">Variável</option>
                    </select>
                </div>

            </div>

            <div class="relative">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                    Custos / Comissões (€)
                </label>

                <input
                    type="number"
                    step="0.01"
                    wire:model="investmentExpenses"
                    placeholder="0,00"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                >
            </div>

{{-- 🏠 RENDIMENTOS IMOBILIÁRIOS --}}
@elseif($salarySource === 'imobiliario')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" wire:key="step-imobiliario-container">
        {{-- IDENTIFICAÇÃO --}}
        <div class="relative sm:col-span-2" wire:key="field-prop-desc">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">Identificação</label>
            <input type="text"
                wire:model="propertyDescription"
                placeholder="Ex: Apartamento Lisboa"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none">
        </div>

        {{-- RENDIMENTO (CAIXA 1) --}}
        <div class="relative group" wire:key="field-rental-gross">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">Rendimento (€)</label>
            <input
                type="number"
                step="0.01"
                wire:model.live="rentalGross"
                placeholder="0,00"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600 outline-none"
            >
        </div>

        {{-- DESPESAS (CAIXA 2) --}}
        <div class="relative group" wire:key="field-rental-expenses">
            <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">Despesas (€)</label>
            <input
                type="number"
                step="0.01"
                wire:model.live="rentalExpenses"
                placeholder="0,00"
                class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500 outline-none"
            >
        </div>
    </div>

    {{-- QUADRO DE RESULTADO --}}
    <div class="p-5 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 rounded-2xl" wire:key="rental-result-box">
        <p class="text-xs font-black text-amber-700 dark:text-amber-300 uppercase tracking-wider">🏠 Rendimento estimado</p>
        <p class="text-2xl font-black text-amber-700 dark:text-amber-300 mt-1 tabular-nums">
            {{ number_format((float)$this->salaryAmount, 2, ',', ' ') }} €
        </p>
    </div>

        {{-- ═══════════════════════════════════ --}}
        {{-- 👴 REFORMA                           --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'reforma')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Tipo de Pensão
                    </label>

                    <select wire:model="pensionType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="velhice">👴 Pensão de Velhice</option>
                        <option value="invalidez">♿ Pensão de Invalidez</option>
                        <option value="sobrevivencia">❤️ Pensão de Sobrevivência</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Entidade Pagadora
                    </label>

                    <input
                        type="text"
                        wire:model="pensionEntity"
                        placeholder="Ex: Segurança Social"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Valor Bruto (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="pensionGross"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-zinc-500"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Valor Líquido (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="pensionAmount"
                        placeholder="0,00"
                        class="w-full bg-white dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

            </div>


        {{-- ═══════════════════════════════════ --}}
        {{-- 🎓 BOLSA                             --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'bolsa')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Tipo de Bolsa
                    </label>

                    <select wire:model="scholarshipType"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="estudo">🎓 Bolsa de Estudo</option>
                        <option value="formacao">📚 Apoio à Formação</option>
                        <option value="investigacao">🔬 Bolsa de Investigação</option>
                        <option value="estagio">💼 Bolsa de Estágio</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Entidade
                    </label>

                    <input
                        type="text"
                        wire:model="scholarshipEntity"
                        placeholder="Ex: Universidade, IEFP..."
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                    >
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Valor (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="scholarshipAmount"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="scholarshipFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>

            </div>

            <div class="relative">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                    Data de fim (opcional)
                </label>

                <input
                    type="date"
                    wire:model="scholarshipEndDate"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white"
                >
            </div>


        {{-- ═══════════════════════════════════ --}}
        {{-- ✨ OUTRO                             --}}
        {{-- ═══════════════════════════════════ --}}
        @elseif($salarySource === 'outro')

            <div class="relative">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                    Nome da Fonte
                </label>

                <input
                    type="text"
                    wire:model="otherSourceDetail"
                    placeholder="Ex: Subsídio, Mesada, Pensão, Comissão..."
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                        Valor (€)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        wire:model="otherAmount"
                        placeholder="0,00"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-emerald-500/30 rounded-2xl py-4 px-5 text-xl font-black text-emerald-600"
                    >
                </div>

                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                        Periodicidade
                    </label>

                    <select wire:model="otherFrequency"
                        class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white">
                        <option value="mensal">Mensal</option>
                        <option value="semanal">Semanal</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="anual">Anual</option>
                        <option value="variavel">Variável</option>
                    </select>
                </div>

            </div>

            <div class="relative w-40">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-zinc-400 z-10">
                    Dia de Recebimento
                </label>

                <input
                    type="number"
                    min="1"
                    max="31"
                    wire:model="salaryDay"
                    class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 px-5 text-xl font-black text-center dark:text-white"
                >
            </div>

            <div class="relative sm:col-span-2">
                <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-emerald-600 z-10">
                    Conta onde recebes este rendimento
                </label>
                <select wire:model="salaryBankAccountId" class="w-full bg-zinc-50 dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-2xl py-4 px-5 text-sm font-bold dark:text-white outline-none transition-all">
                    <option value="">Não associar a uma conta</option>
                    @foreach($this->bankAccountOptions() as $bankAccount)
                        <option value="{{ $bankAccount->id }}">
                            {{ $bankAccount->name }}{{ $bankAccount->bank_name ? ' · '.$bankAccount->bank_name : '' }}
                        </option>
                    @endforeach
                </select>
                @if($this->bankAccountOptions()->isEmpty())
                    <p class="mt-2 text-[10px] font-medium text-zinc-400">Ainda não tens contas cadastradas. Podes adicionar uma no passo anterior ou mais tarde no módulo Banco.</p>
                @endif
            </div>

        @endif

    </div>


    @if($salarySource !== 'emprego' || ! $canManageOnboarding)
        @include('livewire.partials.onboarding-footer', [
            'footerStep' => $step, 'footerTotal' => $totalSteps,
            'footerPrevious' => 'previousStep', 'footerSkip' => 'skipStep',
            'footerNext' => 'nextStep', 'footerSubmit' => false,
            'footerNextLabel' => $salarySource === 'emprego' ? null : 'Guardar e Continuar',
            'footerLoadingTarget' => 'nextStep',
        ])
    @endif

</div>
@endif



















            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 2: WORKSPACE                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 2)
            <form wire:submit.prevent="nextStep" class="space-y-6 p-5 sm:p-9">
                <div class="flex items-center justify-between gap-4">
                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                        <flux:icon name="squares-2x2" class="size-4" />
                        Espaço financeiro
                    </span>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Passo 2 de {{ $totalSteps }}</span>
                </div>

                <div class="flex items-start gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/20">
                        <flux:icon name="user-group" class="size-6" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">O teu espaço financeiro</h2>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">Escolhe um nome para identificares o espaço onde organizas as tuas finanças.</p>
                    </div>
                </div>

                <div class="space-y-4 rounded-3xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900 sm:p-5">
                    <div>
                        <label for="onboarding-workspace-name" class="block text-sm font-bold text-zinc-900 dark:text-white">Nome do espaço</label>
                        <p id="onboarding-workspace-help" class="mt-1 text-xs leading-5 text-zinc-500 dark:text-zinc-400">Usa um nome que seja fácil de reconhecer.</p>
                        <input id="onboarding-workspace-name" type="text" wire:model.blur="workspaceName" aria-describedby="onboarding-workspace-help"
                            placeholder="Ex.: As Minhas Finanças"
                            class="mt-3 h-12 w-full rounded-2xl border-2 border-zinc-200 bg-white px-4 text-sm font-bold text-zinc-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    </div>

                    <div>
                        <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Precisas de inspiração?</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Escolhe uma sugestão ou escreve o teu próprio nome.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                        <button type="button" wire:click="$set('workspaceName', 'As Minhas Finanças')"
                            class="rounded-2xl border p-3 text-left transition hover:border-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 {{ $workspaceName === 'As Minhas Finanças' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950' }}">
                            <span class="block text-lg" aria-hidden="true">👤</span>
                            <span class="mt-1 block text-xs font-bold text-zinc-900 dark:text-white">As Minhas Finanças</span>
                        </button>
                        <button type="button" wire:click="$set('workspaceName', 'Família')"
                            class="rounded-2xl border p-3 text-left transition hover:border-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 {{ $workspaceName === 'Família' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950' }}">
                            <span class="block text-lg" aria-hidden="true">👨‍👩‍👧</span>
                            <span class="mt-1 block text-xs font-bold text-zinc-900 dark:text-white">Família</span>
                        </button>
                        <button type="button" wire:click="$set('workspaceName', 'O Meu Negócio')"
                            class="rounded-2xl border p-3 text-left transition hover:border-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 {{ $workspaceName === 'O Meu Negócio' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950' }}">
                            <span class="block text-lg" aria-hidden="true">🏢</span>
                            <span class="mt-1 block text-xs font-bold text-zinc-900 dark:text-white">O Meu Negócio</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-2xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/30">
                    <flux:icon name="information-circle" class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                    <p class="text-xs leading-5 text-zinc-600 dark:text-zinc-400">As sugestões alteram apenas o nome. Não criam outro espaço nem alteram quem tem acesso.</p>
                </div>

                @include('livewire.partials.onboarding-footer', [
                    'footerStep' => $step, 'footerTotal' => $totalSteps,
                    'footerPrevious' => 'previousStep', 'footerSkip' => 'skipStep',
                    'footerNext' => 'nextStep', 'footerSubmit' => true,
                    'footerNextLabel' => 'Continuar', 'footerLoadingTarget' => 'nextStep',
                ])
            </form>
            @endif













@if($step >= 5 && $step <= 8)
    @php
        $extraSteps = [
            5 => ['title' => 'Adiciona outros rendimentos', 'description' => 'Escolhe uma fonte para abrir o formulário correspondente.', 'icon' => 'banknotes'],
            6 => ['title' => 'Organiza os teus investimentos', 'description' => 'Adiciona os ativos que já tens à tua carteira.', 'icon' => 'chart-bar-square'],
            7 => ['title' => 'Importa um extrato de movimentos bancários', 'description' => 'Analisa e revê os movimentos antes de os importar.', 'icon' => 'document-arrow-up'],
            8 => ['title' => 'Organiza as tuas assinaturas', 'description' => 'Regista serviços recorrentes para acompanhares os seus custos.', 'icon' => 'credit-card'],
        ];
        $extraStep = $extraSteps[$step];
    @endphp
    <div class="space-y-6 p-5 sm:p-9">
        <div class="flex items-start gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/20">
                <flux:icon name="{{ $extraStep['icon'] }}" class="size-6" />
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ $extraStep['title'] }}</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $extraStep['description'] }}</p>
            </div>
        </div>

        @php
            $user = auth()->user();
            $activeWorkspace = $user->current_workspace_id ? $user->workspaces()->whereKey($user->current_workspace_id)->first() : null;
            $canManageOnboarding = $activeWorkspace && ! $user->isViewer()
                && (! in_array($activeWorkspace->type, ['business', 'company'], true)
                    || app(\App\Services\BusinessAccessService::class)->can('manage_financials', $user, $activeWorkspace));
        @endphp
        @if($canManageOnboarding)
            @switch($step)
                @case(5)
                    <livewire:onboarding-income wire:key="onboarding-income" />
                    @break
                @case(6)
                    <livewire:onboarding-investment wire:key="onboarding-investment" />
                    @break
                @case(7)
                    <livewire:onboarding-statement wire:key="onboarding-statement" />
                    @break
                @case(8)
                    <livewire:onboarding-subscription wire:key="onboarding-subscription" />
                    @break
            @endswitch
        @else
            <p class="rounded-2xl bg-rose-50 p-4 text-sm text-rose-700">Não existe um espaço financeiro ativo com permissão para guardar dados. Podes saltar este passo.</p>
        @endif

        @include('livewire.partials.onboarding-footer', [
            'footerStep' => $step, 'footerTotal' => $totalSteps,
            'footerPrevious' => 'previousStep', 'footerSkip' => 'skipStep',
            'footerNext' => 'nextStep', 'footerSubmit' => false,
            'footerNextLabel' => 'Continuar', 'footerLoadingTarget' => 'nextStep',
        ])
    </div>
@endif

{{-- EXPLORAÇÃO DE HUBS --}}
@if($step === 9)
<div class="p-10 space-y-8"
     wire:key="step-4-exploration"
     x-data="{
        active: 'Alimentação',
        hubs: {
            'Alimentação': { desc: 'Gestão de supermercado, restaurantes e refeições diárias.', ex: 'Regista o talão do Continente ou o jantar de ontem com amigos.', color: 'text-red-500', bg: 'bg-red-500/10' },
            'Carro': { desc: 'Controlo total sobre o teu veículo: combustível, manutenção e impostos.', ex: 'Adiciona o último abastecimento de 60€ ou a revisão anual.', color: 'text-amber-500', bg: 'bg-amber-500/10' },
            'Casa': { desc: 'Custos fixos e variáveis da tua habitação e utilidades.', ex: 'Regista a fatura da luz, a renda ou aquele novo candeeiro.', color: 'text-blue-500', bg: 'bg-blue-500/10' },
            'Educação': { desc: 'Investimento em conhecimento, cursos e materiais escolares.', ex: 'Mensalidade da faculdade ou aquele curso online de IA.', color: 'text-emerald-500', bg: 'bg-emerald-500/10' },
            'Empréstimos': { desc: 'Gestão de créditos e amortizações de dívidas ativas.', ex: 'A prestação mensal do teu crédito habitação ou pessoal.', color: 'text-rose-500', bg: 'bg-rose-500/10' },
            'Lazer': { desc: 'Momentos de descontração, hobbies e entretenimento.', ex: 'Bilhetes para o cinema ou aquela saída no fim de semana.', color: 'text-purple-500', bg: 'bg-purple-500/10' },
            'Saúde': { desc: 'Cuidados médicos, farmácia, exames e bem-estar.', ex: 'Consulta de rotina no dentista ou a fatura da farmácia.', color: 'text-pink-500', bg: 'bg-pink-500/10' },
            'Seguros': { desc: 'Proteção para o que mais importa na tua vida e bens.', ex: 'Prémio anual do seguro automóvel ou seguro de vida.', color: 'text-sky-500', bg: 'bg-sky-500/10' },
            'Tecnologia': { desc: 'Equipamentos, gadgets e subscrições de software.', ex: 'Compra de um novo smartphone ou a licença do Office.', color: 'text-indigo-500', bg: 'bg-indigo-500/10' },
            'Transporte': { desc: 'Mobilidade urbana, transportes públicos e viagens curtas.', ex: 'O carregamento do passe mensal ou uma viagem de Uber.', color: 'text-zinc-500', bg: 'bg-zinc-500/10' }
        }
     }">

    <div class="flex items-center gap-4">
        <div class="size-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
            <flux:icon name="magnifying-glass-plus" class="size-7 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-black italic tracking-tighter dark:text-white">Explora os teus Hubs</h2>
            <p class="text-sm text-zinc-500 mt-0.5">Clica nos ícones para entender como cada Hub organiza a tua vida.</p>
        </div>
    </div>

    {{-- GRELHA DE SELEÇÃO --}}
    <div class="grid grid-cols-5 gap-3">
        @php
            $fixedHubs = [
                ['n' => 'Alimentação', 'i' => 'shopping-cart'],
                ['n' => 'Carro',       'i' => 'truck'],
                ['n' => 'Casa',        'i' => 'home'],
                ['n' => 'Educação',    'i' => 'academic-cap'],
                ['n' => 'Empréstimos', 'i' => 'banknotes'],
                ['n' => 'Lazer',       'i' => 'ticket'],
                ['n' => 'Saúde',       'i' => 'heart'],
                ['n' => 'Seguros',     'i' => 'shield-check'],
                ['n' => 'Tecnologia',  'i' => 'cpu-chip'],
                ['n' => 'Transporte',  'i' => 'bolt'],
            ];
        @endphp

        @foreach($fixedHubs as $hub)
            <button type="button"
                @click="active = '{{ $hub['n'] }}'"
                :class="active === '{{ $hub['n'] }}' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 scale-105 shadow-md' : 'border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 opacity-60 hover:opacity-100'"
                class="flex flex-col items-center justify-center p-3 border-2 rounded-2xl transition-all duration-300 group">
                <flux:icon name="{{ $hub['i'] }}"
                    class="size-6 mb-2 transition-colors"
                    ::class="active === '{{ $hub['n'] }}' ? hubs['{{ $hub['n'] }}'].color : 'text-zinc-400 group-hover:text-zinc-600'" />
                <span class="text-[8px] font-black uppercase tracking-tighter text-zinc-500 dark:text-zinc-400 text-center leading-none">{{ $hub['n'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- PAINEL DE DETALHES DINÂMICO --}}
    <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-8 shadow-inner animate-in fade-in slide-in-from-bottom-2 duration-500">
        <div class="flex flex-col sm:flex-row items-start gap-6">
            <div class="size-16 rounded-2xl flex items-center justify-center shrink-0 transition-colors duration-500"
                 :class="hubs[active].bg">
                <template x-if="active === 'Alimentação'"><flux:icon name="shopping-cart" class="size-8 text-red-500" /></template>
                <template x-if="active === 'Carro'"><flux:icon name="truck" class="size-8 text-amber-500" /></template>
                <template x-if="active === 'Casa'"><flux:icon name="home" class="size-8 text-blue-500" /></template>
                <template x-if="active === 'Educação'"><flux:icon name="academic-cap" class="size-8 text-emerald-500" /></template>
                <template x-if="active === 'Empréstimos'"><flux:icon name="banknotes" class="size-8 text-rose-500" /></template>
                <template x-if="active === 'Lazer'"><flux:icon name="ticket" class="size-8 text-purple-500" /></template>
                <template x-if="active === 'Saúde'"><flux:icon name="heart" class="size-8 text-pink-500" /></template>
                <template x-if="active === 'Seguros'"><flux:icon name="shield-check" class="size-8 text-sky-500" /></template>
                <template x-if="active === 'Tecnologia'"><flux:icon name="cpu-chip" class="size-8 text-indigo-500" /></template>
                <template x-if="active === 'Transporte'"><flux:icon name="bolt" class="size-8 text-zinc-500" /></template>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="text-xl font-black uppercase italic tracking-tighter dark:text-white" x-text="active"></h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium leading-relaxed" x-text="hubs[active].desc"></p>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border-l-4 border-indigo-500">
                    <p class="text-[9px] font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-widest mb-1">Exemplo de Lançamento:</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-300 italic font-medium" x-text="hubs[active].ex"></p>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.partials.onboarding-footer', [
        'footerStep' => $step, 'footerTotal' => $totalSteps,
        'footerPrevious' => 'previousStep', 'footerSkip' => null,
        'footerNext' => 'nextStep', 'footerSubmit' => false,
        'footerNextLabel' => 'Concluído, Continuar', 'footerLoadingTarget' => 'nextStep',
    ])
</div>
@endif






















            {{-- ─────────────────────────────────── --}}
            {{-- PASSO 10: CONCLUÍDO                  --}}
            {{-- ─────────────────────────────────── --}}
            @if($step === 10)
            <div class="p-6 sm:p-9 space-y-9">
                {{-- CONCLUSÃO --}}
                <div class="space-y-4 text-center">
                    <div class="relative mx-auto flex size-24 items-center justify-center rounded-[2rem] bg-gradient-to-br from-emerald-400 via-teal-500 to-indigo-600 shadow-2xl shadow-emerald-500/20">
                        <flux:icon name="check-circle" class="size-12 text-white" />
                        <span class="absolute -right-2 -top-2 flex size-9 items-center justify-center rounded-full border-4 border-white bg-amber-400 text-lg dark:border-zinc-950">✦</span>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                            <flux:icon name="check-badge" class="size-4" /> Configuração concluída
                        </span>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-4xl">Está tudo pronto para começares! 🎉</h2>
                        <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                            O teu espaço financeiro está preparado. Agora podes acompanhar o teu dinheiro, perceber para onde vai e tomar decisões com mais confiança — tudo num só lugar.
                        </p>
                    </div>
                </div>

                {{-- O QUE PODES FAZER --}}
                <section class="space-y-4">
                    <div class="text-left">
                        <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white">O teu dinheiro. Todas as ferramentas.</h3>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Descobre o que podes fazer no Finance Pro.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="flex gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left dark:border-zinc-800 dark:bg-zinc-900">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300"><flux:icon name="chart-pie" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Dashboard financeiro</p><p class="mt-1 text-xs leading-relaxed text-zinc-500">Consulta saldos, receitas, despesas, património e indicadores num resumo central.</p></div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left dark:border-zinc-800 dark:bg-zinc-900">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300"><flux:icon name="arrows-right-left" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Receitas e despesas</p><p class="mt-1 text-xs leading-relaxed text-zinc-500">Organiza salários, outros rendimentos, despesas recorrentes e movimentos do dia a dia.</p></div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left dark:border-zinc-800 dark:bg-zinc-900">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300"><flux:icon name="building-library" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Contas e investimentos</p><p class="mt-1 text-xs leading-relaxed text-zinc-500">Centraliza contas bancárias, acompanha investimentos e consulta a evolução do teu património.</p></div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left dark:border-zinc-800 dark:bg-zinc-900">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300"><flux:icon name="file-up" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Importação inteligente</p><p class="mt-1 text-xs leading-relaxed text-zinc-500">Importa extratos e relatórios suportados para reduzir o trabalho de registo manual.</p></div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left dark:border-zinc-800 dark:bg-zinc-900">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-500/15 dark:text-violet-300"><flux:icon name="sparkles" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Inteligência artificial</p><p class="mt-1 text-xs leading-relaxed text-zinc-500">Usa as ferramentas de IA disponíveis no teu plano para analisar informação e apoiar decisões.</p></div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left dark:border-zinc-800 dark:bg-zinc-900">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300"><flux:icon name="target" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Objetivos e controlo</p><p class="mt-1 text-xs leading-relaxed text-zinc-500">Define objetivos, acompanha hábitos financeiros e mantém as tuas subscrições sob controlo.</p></div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-zinc-200 bg-white p-4 text-left dark:border-zinc-800 dark:bg-zinc-900 sm:col-span-2">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-fuchsia-100 text-fuchsia-600 dark:bg-fuchsia-500/15 dark:text-fuchsia-300"><flux:icon name="users" class="size-5" /></span>
                            <div><p class="text-sm font-bold text-zinc-900 dark:text-white">Finance Connect</p><p class="mt-1 text-xs leading-relaxed text-zinc-500">Explora a comunidade financeira, partilha conquistas e acompanha outros membros.</p></div>
                        </div>
                    </div>
                </section>

                {{-- PLANOS --}}
                <section class="space-y-4">
                    <div class="text-center">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500">Escolhe como queres evoluir</span>
                        <h3 class="mt-2 text-xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-2xl">Tira ainda mais partido do Finance Pro</h3>
                        <p class="mx-auto mt-2 max-w-xl text-xs leading-5 text-zinc-500 dark:text-zinc-400">Começa gratuitamente e faz upgrade quando quiseres desbloquear mais ferramentas.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        {{-- FREE --}}
                        <div class="flex flex-col rounded-2xl border border-zinc-200 bg-white p-5 text-left dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-black text-zinc-900 dark:text-white">Free</p>
                                <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-[9px] font-black uppercase text-zinc-500 dark:bg-zinc-800">Para começar</span>
                            </div>
                            <p class="mt-3 text-3xl font-black text-zinc-900 dark:text-white">0€<span class="text-xs font-semibold text-zinc-400"> / mês</span></p>
                            <p class="mt-2 min-h-10 text-xs leading-5 text-zinc-500">O essencial para começares a organizar as tuas finanças pessoais.</p>
                            <ul class="mt-4 flex-1 space-y-2.5 text-xs text-zinc-600 dark:text-zinc-300">
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-500" /> Gestão pessoal simples</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-500" /> Controlo financeiro básico</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-500" /> Sem mensalidade</li>
                            </ul>
                            <p class="mt-5 rounded-xl bg-zinc-100 px-3 py-2.5 text-center text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Continua gratuitamente</p>
                        </div>

                        {{-- PRO --}}
                        <div class="relative flex flex-col rounded-2xl border-2 border-emerald-500 bg-emerald-50/60 p-5 text-left shadow-lg shadow-emerald-500/10 dark:bg-emerald-500/5">
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-emerald-600 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-white">Recomendado</span>
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <p class="text-sm font-black text-zinc-900 dark:text-white">Pro</p>
                                <flux:icon name="sparkles" class="size-5 text-emerald-600" />
                            </div>
                            <p class="mt-3 text-3xl font-black text-zinc-900 dark:text-white">5€<span class="text-xs font-semibold text-zinc-400"> / mês</span></p>
                            <p class="mt-2 min-h-10 text-xs leading-5 text-zinc-600 dark:text-zinc-400">Para quem quer aprofundar a análise e automatizar a gestão pessoal.</p>
                            <ul class="mt-4 flex-1 space-y-2.5 text-xs text-zinc-700 dark:text-zinc-200">
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-600" /> Tudo o que o Free oferece</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-600" /> IA Pilot</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-600" /> Relatórios avançados</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-600" /> Inventário</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-emerald-600" /> Experiência sem anúncios</li>
                            </ul>
                            <a href="{{ route('hub.pricing') }}" wire:navigate class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-xs font-black text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                                Conhecer o plano Pro <flux:icon name="arrow-up-right" class="size-4" />
                            </a>
                        </div>

                        {{-- BUSINESS --}}
                        <div class="flex flex-col rounded-2xl border border-violet-300 bg-zinc-950 p-5 text-left shadow-lg shadow-violet-500/10 dark:border-violet-500/40">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-black text-white">Business</p>
                                <span class="rounded-full bg-violet-500/15 px-2.5 py-1 text-[9px] font-black uppercase text-violet-300">Para empresas</span>
                            </div>
                            <p class="mt-3 text-3xl font-black text-white">10€<span class="text-xs font-semibold text-zinc-400"> / mês</span></p>
                            <p class="mt-2 min-h-10 text-xs leading-5 text-zinc-400">Para gerir a atividade empresarial com ferramentas dedicadas.</p>
                            <ul class="mt-4 flex-1 space-y-2.5 text-xs text-zinc-200">
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-violet-400" /> Funcionalidades do Pro</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-violet-400" /> Área Empresa</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-violet-400" /> Inventário e relatórios avançados</li>
                                <li class="flex gap-2"><flux:icon name="check" class="size-4 shrink-0 text-violet-400" /> Suporte prioritário</li>
                            </ul>
                            <a href="{{ route('hub.pricing') }}" wire:navigate class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-xs font-black text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700">
                                Conhecer o Business <flux:icon name="arrow-up-right" class="size-4" />
                            </a>
                        </div>
                    </div>
                    <p class="text-center text-[10px] leading-5 text-zinc-400">Preços mensais apresentados em euros. Podes consultar os detalhes e aderir a qualquer plano na página de Planos e Mensalidades.</p>
                </section>

                {{-- ATALHOS --}}
                <section class="space-y-3">
                    <div class="text-left">
                        <h3 class="text-base font-black text-zinc-900 dark:text-white">Por onde queres começar?</h3>
                        <p class="mt-1 text-xs text-zinc-500">Os teus principais espaços estão à distância de um clique.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <a href="{{ route('dashboard') }}" wire:navigate class="group rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-left transition hover:border-indigo-400 dark:border-indigo-900/40 dark:bg-indigo-500/10">
                            <flux:icon name="squares-2x2" class="mb-2 size-6 text-indigo-500" />
                            <p class="text-sm font-black text-zinc-900 transition group-hover:text-indigo-600 dark:text-white">Dashboard</p>
                            <p class="mt-1 text-[10px] text-zinc-500">Ver o teu resumo financeiro</p>
                        </a>
                        <a href="{{ route('hub.incomes') }}" wire:navigate class="group rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-left transition hover:border-emerald-400 dark:border-emerald-900/40 dark:bg-emerald-500/10">
                            <flux:icon name="arrow-trending-up" class="mb-2 size-6 text-emerald-500" />
                            <p class="text-sm font-black text-zinc-900 transition group-hover:text-emerald-600 dark:text-white">Receitas</p>
                            <p class="mt-1 text-[10px] text-zinc-500">Gerir salários e rendimentos</p>
                        </a>
                        <a href="{{ route('social.hub') }}" wire:navigate class="group rounded-2xl border border-fuchsia-100 bg-fuchsia-50 p-4 text-left transition hover:border-fuchsia-400 dark:border-fuchsia-900/40 dark:bg-fuchsia-500/10">
                            <flux:icon name="users" class="mb-2 size-6 text-fuchsia-500" />
                            <p class="text-sm font-black text-zinc-900 transition group-hover:text-fuchsia-600 dark:text-white">Finance Connect</p>
                            <p class="mt-1 text-[10px] text-zinc-500">Explorar a comunidade</p>
                        </a>
                    </div>
                </section>

                @include('livewire.partials.onboarding-footer', [
                    'footerStep' => $step, 'footerTotal' => $totalSteps,
                    'footerPrevious' => 'previousStep', 'footerSkip' => null,
                    'footerNext' => 'completeOnboarding', 'footerSubmit' => false,
                    'footerNextLabel' => 'Entrar no Finance Pro', 'footerLoadingTarget' => 'completeOnboarding',
                ])
            </div>
            @endif

        </div>
    </div>
</div>

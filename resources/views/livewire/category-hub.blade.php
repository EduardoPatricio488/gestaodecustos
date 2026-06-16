@php
    $themes = [
        'carro'       => ['color' => 'text-amber-500',  'label' => 'Logística de Veículo',  'budgetLabel' => 'Plafond de Manutenção'],
        'casa'        => ['color' => 'text-blue-500',   'label' => 'Gestão de Habitação',    'budgetLabel' => 'Gestão de Rendas'],
        'alimentacao' => ['color' => 'text-orange-500', 'label' => 'Controlo de Consumo',    'budgetLabel' => 'Budget de Nutrição'],
        'saude'       => ['color' => 'text-red-500',    'label' => 'Bem-estar e Saúde',       'budgetLabel' => 'Reserva Médica'],
        'tecnologia'  => ['color' => 'text-indigo-500', 'label' => 'Infraestrutura Digital', 'budgetLabel' => 'Budget SaaS'],
        'educacao'    => ['color' => 'text-emerald-500', 'label' => 'Educação e Formação',   'budgetLabel' => 'Investimento Educacional'],
        'emprestimos' => ['color' => 'text-rose-500',   'label' => 'Gestão de Empréstimos', 'budgetLabel' => 'Controlo de Dívida'],
        'seguros'     => ['color' => 'text-sky-500',    'label' => 'Proteção e Seguros',    'budgetLabel' => 'Prémios de Seguros'],
    ];
    $hubTheme = $themes[$slug] ?? ['color' => 'text-brand-600', 'label' => 'Gestão Estratégica', 'budgetLabel' => 'Teto Orçamental'];

    // Definição de campos específicos por categoria
    $categoryFields = [
        'carro' => [
            'icon' => 'truck',
            'fields' => [
                ['name' => 'km', 'label' => 'Quilometragem Atual', 'type' => 'number', 'placeholder' => 'Ex: 120000'],
                ['name' => 'local', 'label' => 'Localização do Serviço', 'type' => 'text', 'placeholder' => 'Ex: Galp Lisboa', 'icon' => 'map-pin'],
                ['name' => 'combustivel', 'label' => 'Consumo (L/100km)', 'type' => 'number', 'placeholder' => 'Ex: 6.5', 'step' => '0.1'],
                ['name' => 'urgencia', 'label' => 'Prioridade', 'type' => 'select', 'options' => ['Rotina', 'Urgente', 'Preventiva']],
                ['name' => 'estado', 'label' => 'Estado do Veículo', 'type' => 'select', 'options' => ['Bom', 'Precisa Manutenção', 'Crítico']],
            ]
        ],
        'casa' => [
            'icon' => 'home',
            'fields' => [
                ['name' => 'entidade', 'label' => 'Fornecedor/Proprietário', 'type' => 'text', 'placeholder' => 'Ex: EDP, Lusitaniagás', 'icon' => 'building-library'],
                ['name' => 'piso', 'label' => 'Piso/Apartamento', 'type' => 'text', 'placeholder' => 'Ex: Apartamento 305'],
                ['name' => 'valor_anterior', 'label' => 'Valor Mês Anterior (€)', 'type' => 'number', 'placeholder' => 'Para comparação', 'step' => '0.01'],
                ['name' => 'anomalia', 'label' => 'Tem Anomalia', 'type' => 'checkbox', 'label_alt' => 'Problemas a reportar'],
                ['name' => 'referencia', 'label' => 'Nº de Contrato/Referência', 'type' => 'text', 'placeholder' => 'Ex: 123456789'],
            ]
        ],
        'alimentacao' => [
            'icon' => 'shopping-cart',
            'fields' => [
                ['name' => 'pessoas', 'label' => 'Nº de Pessoas', 'type' => 'number', 'placeholder' => 'Ex: 2'],
                ['name' => 'estabelecimento', 'label' => 'Estabelecimento', 'type' => 'text', 'placeholder' => 'Ex: Continente Lisboa', 'icon' => 'building-storefront'],
                ['name' => 'dieta', 'label' => 'Tipo de Dieta', 'type' => 'select', 'options' => ['Normal', 'Vegetariana', 'Vegana', 'Sem Glúten', 'Sem Lactose', 'Outra']],
                ['name' => 'orcamento_pessoa', 'label' => 'Orçamento por Pessoa (€)', 'type' => 'number', 'placeholder' => 'Ref. para análise', 'step' => '0.01'],
                ['name' => 'frequencia', 'label' => 'Frequência', 'type' => 'select', 'options' => ['Diária', 'Semanal', 'Mensal', 'Ocasional']],
            ]
        ],
        'saude' => [
            'icon' => 'heart',
            'fields' => [
                ['name' => 'profissional', 'label' => 'Profissional/Clínica', 'type' => 'text', 'placeholder' => 'Ex: Dr. João Silva', 'icon' => 'user-md'],
                ['name' => 'especialidade', 'label' => 'Especialidade', 'type' => 'text', 'placeholder' => 'Ex: Cardiologia'],
                ['name' => 'cobertura_seguro', 'label' => 'Cobertura de Seguro', 'type' => 'select', 'options' => ['Sim, 100%', 'Sim, Parcial', 'Não', 'Desconhecido']],
                ['name' => 'urgencia', 'label' => 'Tipo de Atendimento', 'type' => 'select', 'options' => ['Rotina', 'Urgente', 'Emergência', 'Preventivo']],
                ['name' => 'prescricao', 'label' => 'Prescrição Médica', 'type' => 'checkbox', 'label_alt' => 'Tem prescrição médica'],
            ]
        ],
        'tecnologia' => [
            'icon' => 'computer-desktop',
            'fields' => [
                ['name' => 'fornecedor', 'label' => 'Fornecedor/Plataforma', 'type' => 'text', 'placeholder' => 'Ex: Microsoft, Adobe', 'icon' => 'building-storefront'],
                ['name' => 'produtoServico', 'label' => 'Produto/Serviço', 'type' => 'text', 'placeholder' => 'Ex: Office 365 Pro'],
                ['name' => 'duracao', 'label' => 'Duração', 'type' => 'select', 'options' => ['1 Mês', '3 Meses', '6 Meses', '1 Ano', 'Vitalício', 'Único']],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Ativo', 'Inativo', 'Cancelado']],
                ['name' => 'roi_esperado', 'label' => 'ROI Esperado', 'type' => 'text', 'placeholder' => 'Ex: Alto / Médio / Baixo'],
            ]
        ],
        'educacao' => [
            'icon' => 'star',
            'fields' => [
                ['name' => 'instituicao', 'label' => 'Instituição', 'type' => 'text', 'placeholder' => 'Ex: Universidade XYZ', 'icon' => 'building-library'],
                ['name' => 'curso', 'label' => 'Curso/Disciplina', 'type' => 'text', 'placeholder' => 'Ex: Engenharia Informática'],
                ['name' => 'nivel', 'label' => 'Nível', 'type' => 'select', 'options' => ['Primária', 'Secundária', 'Superior', 'Pós-Graduação', 'Formação', 'Outro']],
                ['name' => 'estado_pagamento', 'label' => 'Estado de Pagamento', 'type' => 'select', 'options' => ['Pago', 'Parcial', 'Pendente']],
            ]
        ],
        'emprestimos' => [
            'icon' => 'banknotes',
            'fields' => [
                ['name' => 'credor', 'label' => 'Credor/Banco', 'type' => 'text', 'placeholder' => 'Ex: Banco XYZ', 'icon' => 'building-storefront'],
                ['name' => 'valor_inicial', 'label' => 'Valor Inicial (€)', 'type' => 'number', 'placeholder' => 'Ex: 50000', 'step' => '0.01'],
                ['name' => 'saldo_atual', 'label' => 'Saldo Atual (€)', 'type' => 'number', 'placeholder' => 'Ex: 35000', 'step' => '0.01'],
                ['name' => 'taxa_juros', 'label' => 'Taxa de Juros (%)', 'type' => 'number', 'placeholder' => 'Ex: 2.5', 'step' => '0.01'],
                ['name' => 'prazo_meses', 'label' => 'Prazo Total (meses)', 'type' => 'number', 'placeholder' => 'Ex: 120'],
                ['name' => 'data_termino', 'label' => 'Data de Término', 'type' => 'text', 'placeholder' => 'Ex: Junho 2030'],
            ]
        ],
        'seguros' => [
            'icon' => 'shield-check',
            'fields' => [
                ['name' => 'tipo_seguro', 'label' => 'Tipo de Seguro', 'type' => 'select', 'options' => ['Saúde', 'Vida', 'Automóvel', 'Habitação', 'Viagem', 'Responsabilidade Civil', 'Outro']],
                ['name' => 'seguradora', 'label' => 'Seguradora', 'type' => 'text', 'placeholder' => 'Ex: AXA, Fidelidade', 'icon' => 'building-storefront'],
                ['name' => 'numero_apolice', 'label' => 'Nº da Apólice', 'type' => 'text', 'placeholder' => 'Ex: AP-2024-001234'],
                ['name' => 'cobertura_valor', 'label' => 'Valor de Cobertura (€)', 'type' => 'number', 'placeholder' => 'Ex: 100000', 'step' => '0.01'],
                ['name' => 'data_renovacao', 'label' => 'Data de Renovação', 'type' => 'text', 'placeholder' => 'Ex: Junho 2025'],
                ['name' => 'estado', 'label' => 'Estado da Apólice', 'type' => 'select', 'options' => ['Ativa', 'Cancelada', 'Suspensa', 'Em Revisão']],
            ]
        ]
    ];
@endphp

<div
    x-data="{ scannerPreview: null }"
    x-on:scan-completed.window="Flux.hide('ai-scanner-modal'); setTimeout(() => Flux.show('ai-review-modal'), 400);"
    x-on:open-add-expense-modal.window="Flux.hide('ai-review-modal'); setTimeout(() => Flux.show('add-expense-modal'), 400);"
    class="space-y-10 pb-24"
>

    {{-- ── HEADER ─────────────────────────────────────────────────── --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-72 {{ str_replace('text','bg',$hubTheme['color']) }}/5 blur-[120px] rounded-full pointer-events-none"></div>
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10 px-2 pt-6">
            <div class="flex items-center gap-6">
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <flux:icon name="{{ $icon }}" class="w-10 h-10 {{ $hubTheme['color'] }}" />
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">{{ $title }}</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Hub Inteligente</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">{{ $hubTheme['label'] }} · <span class="text-brand-600 font-bold uppercase tracking-tighter">{{ $currentWs->name }}</span></p>
                </div>
            </div>
            @if($canManage)
            <div class="flex items-center gap-4 bg-white dark:bg-zinc-900 p-3 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="ai-scanner-modal">
                    <flux:button variant="ghost" icon="sparkles" class="text-brand-600 rounded-2xl hover:bg-brand-50 font-black uppercase text-sm px-4">Scanner IA</flux:button>
                </flux:modal.trigger>
                <div class="h-8 w-px bg-zinc-200 dark:bg-zinc-800"></div>
                <flux:modal.trigger name="add-expense-modal">
                    <flux:button variant="primary" icon="plus" class="bg-brand-600 border-none shadow-lg rounded-2xl font-black uppercase text-sm px-8 text-white">Novo Registo</flux:button>
                </flux:modal.trigger>
                <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="rounded-xl" />
            </div>
            @endif
        </header>
    </div>

    {{-- ── PAINEL ORÇAMENTAL ──────────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-zinc-950 p-10 rounded-[3rem] shadow-2xl border border-zinc-800 group">
        <div class="absolute -right-20 -top-20 size-80 {{ str_replace('text','bg',$hubTheme['color']) }}/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
            <div class="space-y-2">
                <p class="text-[10px] font-black text-brand-400 uppercase tracking-[0.4em] mb-4 italic">{{ $hubTheme['budgetLabel'] }} ({{ now()->translatedFormat('F') }})</p>
                <div class="flex flex-wrap items-baseline gap-4">
                    <h3 class="text-6xl font-black text-white tracking-tighter italic leading-none">{{ number_format($spentThisMonth,2,',',' ') }}€</h3>
                    <span class="text-2xl font-black text-zinc-600 uppercase tracking-tighter">/</span>
                    @if($editingBudget && $isOwner)
                        <input type="number" wire:model="budgetLimit" wire:keydown.enter="updateBudget" wire:blur="updateBudget" autofocus
                               class="w-44 bg-white/5 border border-white/10 rounded-2xl px-4 py-1 {{ $hubTheme['color'] }} font-black text-4xl outline-none shadow-inner">
                    @else
                        <button @if($isOwner) wire:click="$set('editingBudget', true)" @endif class="group/btn flex items-center gap-3 outline-none">
                            <span class="text-4xl font-black {{ $budgetLimit > 0 ? 'text-zinc-500' : 'text-zinc-700 animate-pulse' }} tracking-tighter italic uppercase">
                                {{ $budgetLimit > 0 ? number_format($budgetLimit,0).'€' : 'Definir Limite' }}
                            </span>
                            @if($isOwner)<flux:icon name="pencil" class="size-4 text-zinc-600 group-hover/btn:{{ $hubTheme['color'] }} transition-colors" />@endif
                        </button>
                    @endif
                </div>
            </div>
            @if($budgetLimit > 0)
                @php $perc = ($spentThisMonth / $budgetLimit) * 100; @endphp
                <div class="text-right">
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Eficiência de Consumo</p>
                    <p class="text-5xl font-black {{ $perc >= 100 ? 'text-red-500' : ($perc >= 80 ? 'text-orange-500' : 'text-emerald-500') }} tracking-tighter italic">{{ round($perc) }}%</p>
                </div>
            @endif
        </div>
        @if($budgetLimit > 0)
        <div class="mt-10 h-2.5 w-full bg-white/5 rounded-full overflow-hidden p-0.5 border border-white/5 shadow-inner">
            <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $perc >= 100 ? 'bg-red-500 shadow-[0_0_20px_#ef4444]' : str_replace('text','bg',$hubTheme['color']).' shadow-[0_0_20px_rgba(59,130,246,0.6)]' }}" style="width: {{ min($perc,100) }}%"></div>
        </div>
        @endif
    </div>

    {{-- ── WIDGETS TEMÁTICOS ──────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- CARRO: Estatísticas Veículo --}}
        @if($slug === 'carro')
            @php
                $totalGasto = $expenses->sum('amount');
                $mediaGasto = $expenses->count() > 0 ? $totalGasto / $expenses->count() : 0;
                $ultimoGasto = $expenses->first();
            @endphp

            <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="bolt" class="size-8 text-amber-600" />
                    <span class="text-xs font-black text-amber-700 dark:text-amber-400 bg-amber-200/50 dark:bg-amber-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Consumo</span>
                </div>
                <p class="text-xs text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider mb-1">Média por Serviço</p>
                <p class="text-3xl font-black text-amber-700 dark:text-amber-300 tracking-tighter">{{ number_format($mediaGasto, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-950/20 dark:to-yellow-950/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="bars-3" class="size-8 text-yellow-600" />
                    <span class="text-xs font-black text-yellow-700 dark:text-yellow-400 bg-yellow-200/50 dark:bg-yellow-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Manutenção</span>
                </div>
                <p class="text-xs text-yellow-600 dark:text-yellow-400 font-bold uppercase tracking-wider mb-1">Serviços Realizados</p>
                <p class="text-3xl font-black text-yellow-700 dark:text-yellow-300 tracking-tighter">{{ $expenses->count() }}</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-red-50 dark:from-amber-950/20 dark:to-red-950/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="calendar" class="size-8 text-red-600" />
                    <span class="text-xs font-black text-red-700 dark:text-red-400 bg-red-200/50 dark:bg-red-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Agenda</span>
                </div>
                <p class="text-xs text-red-600 dark:text-red-400 font-bold uppercase tracking-wider mb-1">Último Serviço</p>
                <p class="text-2xl font-black text-red-700 dark:text-red-300 tracking-tighter">{{ $ultimoGasto ? $ultimoGasto->spent_at->translatedFormat('d M') : 'N/A' }}</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-green-50 dark:from-amber-950/20 dark:to-green-950/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="star" class="size-8 text-green-600" />
                    <span class="text-xs font-black text-green-700 dark:text-green-400 bg-green-200/50 dark:bg-green-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Saúde</span>
                </div>
                <p class="text-xs text-green-600 dark:text-green-400 font-bold uppercase tracking-wider mb-1">Próxima Inspeção</p>
                <p class="text-2xl font-black text-green-700 dark:text-green-300 tracking-tighter">{{ now()->addMonths(3)->translatedFormat('M Y') }}</p>
            </div>

        {{-- CASA: Comparativo e Utilidades --}}
        @elseif($slug === 'casa')
            @php
                $totalGasto = $expenses->sum('amount');
                $mesAnterior = $expenses->where('spent_at', '<', now()->startOfMonth())->sum('amount');
                $economiaOuGasto = $totalGasto - $mesAnterior;
                $percentualChange = $mesAnterior > 0 ? (($economiaOuGasto / $mesAnterior) * 100) : 0;
            @endphp

            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-950/20 dark:to-cyan-950/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="banknotes" class="size-8 text-blue-600" />
                    <span class="text-xs font-black text-blue-700 dark:text-blue-400 bg-blue-200/50 dark:bg-blue-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Custo</span>
                </div>
                <p class="text-xs text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider mb-1">Este Mês</p>
                <p class="text-3xl font-black text-blue-700 dark:text-blue-300 tracking-tighter">{{ number_format($totalGasto, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="{{ $economiaOuGasto < 0 ? 'arrow-down' : 'arrow-up' }}" class="size-8 {{ $economiaOuGasto < 0 ? 'text-emerald-600' : 'text-red-600' }}" />
                    <span class="text-xs font-black {{ $economiaOuGasto < 0 ? 'text-emerald-700 dark:text-emerald-400 bg-emerald-200/50 dark:bg-emerald-950/50' : 'text-red-700 dark:text-red-400 bg-red-200/50 dark:bg-red-950/50' }} px-3 py-1 rounded-lg uppercase tracking-wide">Variação</span>
                </div>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">vs Mês Anterior</p>
                <p class="text-3xl font-black {{ $economiaOuGasto < 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300' }} tracking-tighter">{{ abs($economiaOuGasto) > 0 ? number_format(abs($economiaOuGasto), 2, ',', '.') : '0' }}€</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-violet-50 dark:from-blue-950/20 dark:to-violet-950/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="hashtag" class="size-8 text-violet-600" />
                    <span class="text-xs font-black text-violet-700 dark:text-violet-400 bg-violet-200/50 dark:bg-violet-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Mudança</span>
                </div>
                <p class="text-xs text-violet-600 dark:text-violet-400 font-bold uppercase tracking-wider mb-1">Percentual</p>
                <p class="text-3xl font-black text-violet-700 dark:text-violet-300 tracking-tighter">{{ $percentualChange > 0 ? '+' : '' }}{{ number_format($percentualChange, 1, ',', '.') }}%</p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-950/20 dark:to-purple-950/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="home" class="size-8 text-purple-600" />
                    <span class="text-xs font-black text-purple-700 dark:text-purple-400 bg-purple-200/50 dark:bg-purple-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Registos</span>
                </div>
                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold uppercase tracking-wider mb-1">Despesas Ativas</p>
                <p class="text-3xl font-black text-purple-700 dark:text-purple-300 tracking-tighter">{{ $expenses->count() }}</p>
            </div>

        {{-- ALIMENTAÇÃO: Análise Nutricional --}}
        @elseif($slug === 'alimentacao')
            @php
                $totalGasto = $expenses->sum('amount');
                $expensesWithPeople = $expenses->filter(fn($e) => isset(json_decode($e->meta, true)['pessoas']));
                $custoPorPessoa = $expensesWithPeople->count() > 0
                    ? $expensesWithPeople->sum(function($e) {
                        $meta = json_decode($e->meta, true);
                        return $meta['pessoas'] ? $e->amount / $meta['pessoas'] : $e->amount;
                    }) / $expensesWithPeople->count()
                    : 0;
            @endphp

            <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-950/20 dark:to-red-950/20 rounded-2xl p-6 border border-orange-200 dark:border-orange-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="cube" class="size-8 text-orange-600" />
                    <span class="text-xs font-black text-orange-700 dark:text-orange-400 bg-orange-200/50 dark:bg-orange-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Gasto</span>
                </div>
                <p class="text-xs text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider mb-1">Total este Mês</p>
                <p class="text-3xl font-black text-orange-700 dark:text-orange-300 tracking-tighter">{{ number_format($totalGasto, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-yellow-50 dark:from-orange-950/20 dark:to-yellow-950/20 rounded-2xl p-6 border border-orange-200 dark:border-orange-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="users" class="size-8 text-yellow-600" />
                    <span class="text-xs font-black text-yellow-700 dark:text-yellow-400 bg-yellow-200/50 dark:bg-yellow-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Pessoa</span>
                </div>
                <p class="text-xs text-yellow-600 dark:text-yellow-400 font-bold uppercase tracking-wider mb-1">Custo Médio/Pessoa</p>
                <p class="text-3xl font-black text-yellow-700 dark:text-yellow-300 tracking-tighter">{{ number_format($custoPorPessoa, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-lime-50 dark:from-orange-950/20 dark:to-lime-950/20 rounded-2xl p-6 border border-orange-200 dark:border-orange-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="shopping-cart" class="size-8 text-lime-600" />
                    <span class="text-xs font-black text-lime-700 dark:text-lime-400 bg-lime-200/50 dark:bg-lime-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Frequência</span>
                </div>
                <p class="text-xs text-lime-600 dark:text-lime-400 font-bold uppercase tracking-wider mb-1">Refeições/Compras</p>
                <p class="text-3xl font-black text-lime-700 dark:text-lime-300 tracking-tighter">{{ $expenses->count() }}</p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-green-50 dark:from-orange-950/20 dark:to-green-950/20 rounded-2xl p-6 border border-orange-200 dark:border-orange-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="calendar" class="size-8 text-green-600" />
                    <span class="text-xs font-black text-green-700 dark:text-green-400 bg-green-200/50 dark:bg-green-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Média</span>
                </div>
                <p class="text-xs text-green-600 dark:text-green-400 font-bold uppercase tracking-wider mb-1">por Dia</p>
                <p class="text-3xl font-black text-green-700 dark:text-green-300 tracking-tighter">{{ number_format($totalGasto / now()->day, 2, ',', '.') }}€</p>
            </div>

        {{-- SAÚDE: Análise Cobertura --}}
        @elseif($slug === 'saude')
            @php
                $totalGasto = $expenses->sum('amount');
                $expensesComCobertura = $expenses->filter(fn($e) =>
                    isset(json_decode($e->meta, true)['cobertura_seguro']) &&
                    in_array(json_decode($e->meta, true)['cobertura_seguro'], ['Sim, 100%', 'Sim, Parcial'])
                );
                $totalCobertura = 0;
                foreach($expensesComCobertura as $e) {
                    $meta = json_decode($e->meta, true);
                    if($meta['cobertura_seguro'] === 'Sim, 100%') $totalCobertura += $e->amount;
                    else $totalCobertura += $e->amount * 0.5;
                }
            @endphp

            <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-950/20 dark:to-pink-950/20 rounded-2xl p-6 border border-red-200 dark:border-red-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="heart" class="size-8 text-red-600" />
                    <span class="text-xs font-black text-red-700 dark:text-red-400 bg-red-200/50 dark:bg-red-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Total</span>
                </div>
                <p class="text-xs text-red-600 dark:text-red-400 font-bold uppercase tracking-wider mb-1">Despesas Saúde</p>
                <p class="text-3xl font-black text-red-700 dark:text-red-300 tracking-tighter">{{ number_format($totalGasto, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-950/20 dark:to-rose-950/20 rounded-2xl p-6 border border-red-200 dark:border-red-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="shield-check" class="size-8 text-rose-600" />
                    <span class="text-xs font-black text-rose-700 dark:text-rose-400 bg-rose-200/50 dark:bg-rose-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Cobertura</span>
                </div>
                <p class="text-xs text-rose-600 dark:text-rose-400 font-bold uppercase tracking-wider mb-1">Valor Coberto Seguro</p>
                <p class="text-3xl font-black text-rose-700 dark:text-rose-300 tracking-tighter">{{ number_format($totalCobertura, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/20 dark:to-orange-950/20 rounded-2xl p-6 border border-red-200 dark:border-red-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="exclamation-triangle" class="size-8 text-orange-600" />
                    <span class="text-xs font-black text-orange-700 dark:text-orange-400 bg-orange-200/50 dark:bg-orange-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Seu Custo</span>
                </div>
                <p class="text-xs text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider mb-1">Valor a Pagar</p>
                <p class="text-3xl font-black text-orange-700 dark:text-orange-300 tracking-tighter">{{ number_format($totalGasto - $totalCobertura, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-purple-50 dark:from-red-950/20 dark:to-purple-950/20 rounded-2xl p-6 border border-red-200 dark:border-red-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="calendar" class="size-8 text-purple-600" />
                    <span class="text-xs font-black text-purple-700 dark:text-purple-400 bg-purple-200/50 dark:bg-purple-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Consultas</span>
                </div>
                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold uppercase tracking-wider mb-1">Atendimentos Registados</p>
                <p class="text-3xl font-black text-purple-700 dark:text-purple-300 tracking-tighter">{{ $expenses->count() }}</p>
            </div>

        {{-- TECNOLOGIA: Status Subscrições --}}
        @elseif($slug === 'tecnologia')
            @php
                $totalGasto = $expenses->sum('amount');
                $subscricoesAtivas = $expenses->filter(fn($e) =>
                    isset(json_decode($e->meta, true)['status']) &&
                    json_decode($e->meta, true)['status'] === 'Ativo'
                )->count();
                $subscricoesInativas = $expenses->filter(fn($e) =>
                    isset(json_decode($e->meta, true)['status']) &&
                    json_decode($e->meta, true)['status'] === 'Inativo'
                )->count();
                $custoAnual = $totalGasto * 12;
            @endphp

            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/20 dark:to-blue-950/20 rounded-2xl p-6 border border-indigo-200 dark:border-indigo-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="computer-desktop" class="size-8 text-indigo-600" />
                    <span class="text-xs font-black text-indigo-700 dark:text-indigo-400 bg-indigo-200/50 dark:bg-indigo-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Gasto</span>
                </div>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">Investimento Mensal</p>
                <p class="text-3xl font-black text-indigo-700 dark:text-indigo-300 tracking-tighter">{{ number_format($totalGasto, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-950/20 dark:to-purple-950/20 rounded-2xl p-6 border border-indigo-200 dark:border-indigo-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="calendar" class="size-8 text-purple-600" />
                    <span class="text-xs font-black text-purple-700 dark:text-purple-400 bg-purple-200/50 dark:bg-purple-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Anual</span>
                </div>
                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold uppercase tracking-wider mb-1">Custo Anual Estimado</p>
                <p class="text-3xl font-black text-purple-700 dark:text-purple-300 tracking-tighter">{{ number_format($custoAnual, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-green-50 dark:from-indigo-950/20 dark:to-green-950/20 rounded-2xl p-6 border border-indigo-200 dark:border-indigo-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="check" class="size-8 text-green-600" />
                    <span class="text-xs font-black text-green-700 dark:text-green-400 bg-green-200/50 dark:bg-green-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Ativas</span>
                </div>
                <p class="text-xs text-green-600 dark:text-green-400 font-bold uppercase tracking-wider mb-1">Subscrições Ativas</p>
                <p class="text-3xl font-black text-green-700 dark:text-green-300 tracking-tighter">{{ $subscricoesAtivas }}</p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-red-50 dark:from-indigo-950/20 dark:to-red-950/20 rounded-2xl p-6 border border-indigo-200 dark:border-indigo-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="x-mark" class="size-8 text-red-600" />
                    <span class="text-xs font-black text-red-700 dark:text-red-400 bg-red-200/50 dark:bg-red-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Inativas</span>
                </div>
                <p class="text-xs text-red-600 dark:text-red-400 font-bold uppercase tracking-wider mb-1">Subscrições Inativas</p>
                <p class="text-3xl font-black text-red-700 dark:text-red-300 tracking-tighter">{{ $subscricoesInativas }}</p>
            </div>

        {{-- EDUCAÇÃO: Análise Educacional --}}
        @elseif($slug === 'educacao')
            @php
                $totalGasto = $expenses->sum('amount');
                $expensesAtivas = $expenses->count();
            @endphp

            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/20 dark:to-teal-950/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="star" class="size-8 text-emerald-600" />
                    <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 bg-emerald-200/50 dark:bg-emerald-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Investimento</span>
                </div>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider mb-1">Gasto Total em Educação</p>
                <p class="text-3xl font-black text-emerald-700 dark:text-emerald-300 tracking-tighter">{{ number_format($totalGasto, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-950/20 dark:to-green-950/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="bars-3" class="size-8 text-green-600" />
                    <span class="text-xs font-black text-green-700 dark:text-green-400 bg-green-200/50 dark:bg-green-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Cursos</span>
                </div>
                <p class="text-xs text-green-600 dark:text-green-400 font-bold uppercase tracking-wider mb-1">Programas Ativos</p>
                <p class="text-3xl font-black text-green-700 dark:text-green-300 tracking-tighter">{{ $expensesAtivas }}</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-cyan-50 dark:from-emerald-950/20 dark:to-cyan-950/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="banknotes" class="size-8 text-cyan-600" />
                    <span class="text-xs font-black text-cyan-700 dark:text-cyan-400 bg-cyan-200/50 dark:bg-cyan-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Média</span>
                </div>
                <p class="text-xs text-cyan-600 dark:text-cyan-400 font-bold uppercase tracking-wider mb-1">Investimento Médio</p>
                <p class="text-3xl font-black text-cyan-700 dark:text-cyan-300 tracking-tighter">{{ $expensesAtivas > 0 ? number_format($totalGasto / $expensesAtivas, 2, ',', '.') : '0' }}€</p>
            </div>

        {{-- EMPRÉSTIMOS: Análise de Dívida --}}
        @elseif($slug === 'emprestimos')
            @php
                $totalGasto = $expenses->sum('amount');
                $numEmprestimos = $expenses->count();
                $saldoTotalAtual = 0;
                foreach($expenses as $e) {
                    $meta = json_decode($e->meta, true);
                    if(!empty($meta['saldo_atual'])) {
                        $saldoTotalAtual += $meta['saldo_atual'];
                    }
                }
                $taxaMediaJuros = 0;
                $countComTaxa = 0;
                foreach($expenses as $e) {
                    $meta = json_decode($e->meta, true);
                    if(!empty($meta['taxa_juros'])) {
                        $taxaMediaJuros += $meta['taxa_juros'];
                        $countComTaxa++;
                    }
                }
                $taxaMediaJuros = $countComTaxa > 0 ? $taxaMediaJuros / $countComTaxa : 0;
            @endphp

            <div class="bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-950/20 dark:to-red-950/20 rounded-2xl p-6 border border-rose-200 dark:border-rose-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="banknotes" class="size-8 text-rose-600" />
                    <span class="text-xs font-black text-rose-700 dark:text-rose-400 bg-rose-200/50 dark:bg-rose-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Dívida</span>
                </div>
                <p class="text-xs text-rose-600 dark:text-rose-400 font-bold uppercase tracking-wider mb-1">Saldo Total Atual</p>
                <p class="text-3xl font-black text-rose-700 dark:text-rose-300 tracking-tighter">{{ number_format($saldoTotalAtual, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-rose-50 to-orange-50 dark:from-rose-950/20 dark:to-orange-950/20 rounded-2xl p-6 border border-rose-200 dark:border-rose-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="hashtag" class="size-8 text-orange-600" />
                    <span class="text-xs font-black text-orange-700 dark:text-orange-400 bg-orange-200/50 dark:bg-orange-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Juros</span>
                </div>
                <p class="text-xs text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider mb-1">Taxa Média de Juros</p>
                <p class="text-3xl font-black text-orange-700 dark:text-orange-300 tracking-tighter">{{ number_format($taxaMediaJuros, 2, ',', '.') }}%</p>
            </div>

            <div class="bg-gradient-to-br from-rose-50 to-pink-50 dark:from-rose-950/20 dark:to-pink-950/20 rounded-2xl p-6 border border-rose-200 dark:border-rose-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="bars-3" class="size-8 text-pink-600" />
                    <span class="text-xs font-black text-pink-700 dark:text-pink-400 bg-pink-200/50 dark:bg-pink-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Contratos</span>
                </div>
                <p class="text-xs text-pink-600 dark:text-pink-400 font-bold uppercase tracking-wider mb-1">Empréstimos Ativos</p>
                <p class="text-3xl font-black text-pink-700 dark:text-pink-300 tracking-tighter">{{ $numEmprestimos }}</p>
            </div>

        {{-- SEGUROS: Análise de Cobertura --}}
        @elseif($slug === 'seguros')
            @php
                $totalPremios = $expenses->sum('amount');
                $numSeguros = $expenses->count();
                $coverturaTotal = 0;
                foreach($expenses as $e) {
                    $meta = json_decode($e->meta, true);
                    if(!empty($meta['cobertura_valor'])) {
                        $coverturaTotal += $meta['cobertura_valor'];
                    }
                }
                $segurosAtivos = $expenses->filter(fn($e) =>
                    isset(json_decode($e->meta, true)['estado']) &&
                    json_decode($e->meta, true)['estado'] === 'Ativa'
                )->count();
            @endphp

            <div class="bg-gradient-to-br from-sky-50 to-blue-50 dark:from-sky-950/20 dark:to-blue-950/20 rounded-2xl p-6 border border-sky-200 dark:border-sky-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="banknotes" class="size-8 text-sky-600" />
                    <span class="text-xs font-black text-sky-700 dark:text-sky-400 bg-sky-200/50 dark:bg-sky-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Prémios</span>
                </div>
                <p class="text-xs text-sky-600 dark:text-sky-400 font-bold uppercase tracking-wider mb-1">Total de Prémios Mensais</p>
                <p class="text-3xl font-black text-sky-700 dark:text-sky-300 tracking-tighter">{{ number_format($totalPremios, 2, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-sky-50 to-cyan-50 dark:from-sky-950/20 dark:to-cyan-950/20 rounded-2xl p-6 border border-sky-200 dark:border-sky-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="shield-check" class="size-8 text-cyan-600" />
                    <span class="text-xs font-black text-cyan-700 dark:text-cyan-400 bg-cyan-200/50 dark:bg-cyan-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Cobertura</span>
                </div>
                <p class="text-xs text-cyan-600 dark:text-cyan-400 font-bold uppercase tracking-wider mb-1">Valor Total Coberto</p>
                <p class="text-3xl font-black text-cyan-700 dark:text-cyan-300 tracking-tighter">{{ number_format($coverturaTotal, 0, ',', '.') }}€</p>
            </div>

            <div class="bg-gradient-to-br from-sky-50 to-indigo-50 dark:from-sky-950/20 dark:to-indigo-950/20 rounded-2xl p-6 border border-sky-200 dark:border-sky-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="check-circle" class="size-8 text-indigo-600" />
                    <span class="text-xs font-black text-indigo-700 dark:text-indigo-400 bg-indigo-200/50 dark:bg-indigo-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Ativas</span>
                </div>
                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">Apólices Ativas</p>
                <p class="text-3xl font-black text-indigo-700 dark:text-indigo-300 tracking-tighter">{{ $segurosAtivos }}</p>
            </div>

            <div class="bg-gradient-to-br from-sky-50 to-violet-50 dark:from-sky-950/20 dark:to-violet-950/20 rounded-2xl p-6 border border-sky-200 dark:border-sky-800/30 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <flux:icon name="check" class="size-8 text-violet-600" />
                    <span class="text-xs font-black text-violet-700 dark:text-violet-400 bg-violet-200/50 dark:bg-violet-950/50 px-3 py-1 rounded-lg uppercase tracking-wide">Total</span>
                </div>
                <p class="text-xs text-violet-600 dark:text-violet-400 font-bold uppercase tracking-wider mb-1">Apólices Registadas</p>
                <p class="text-3xl font-black text-violet-700 dark:text-violet-300 tracking-tighter">{{ $numSeguros }}</p>
            </div>
        @endif

    </div>
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="px-8 py-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/30 dark:bg-zinc-950/20">
            <p class="text-sm font-black dark:text-white uppercase italic tracking-tight">Histórico Detalhado: {{ $title }}</p>
            <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none">{{ $expenses->count() }} Registos</flux:badge>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-[0.2em]">
                        <th class="p-6 w-28">Data</th>
                        <th class="p-6">Tipo</th>
                        <th class="p-6">Detalhes Específicos</th>
                        <th class="p-6">Observações</th>
                        <th class="p-6 text-right px-8 w-32">Montante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row">
                        <td class="p-6 align-top whitespace-nowrap">
                            <span class="text-lg font-black dark:text-white leading-none tracking-tighter block">{{ $expense->spent_at->format('d') }}</span>
                            <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest mt-1.5 block">{{ $expense->spent_at->translatedFormat('M') }}</span>
                        </td>
                        <td class="p-6 align-top">
                            <span class="text-[9px] w-fit font-black {{ $hubTheme['color'] }} uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 rounded-lg inline-block">{{ $expense->subcategory }}</span>
                        </td>
                        <td class="p-6 align-top">
                            <div class="flex flex-col gap-2.5">
                                @php $meta = is_array($expense->meta) ? $expense->meta : (json_decode($expense->meta, true) ?? []); @endphp

                                {{-- CARRO --}}
                                @if($slug === 'carro' && !empty($meta))
                                    @if(!empty($meta['km']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ number_format($meta['km']) }}km</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['urgencia']))
                                        <div class="text-xs font-semibold {{ $meta['urgencia'] === 'Urgente' ? 'text-red-600 dark:text-red-400' : ($meta['urgencia'] === 'Preventiva' ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-600 dark:text-zinc-400') }}">
                                            {{ $meta['urgencia'] }}
                                        </div>
                                    @endif
                                    @if(!empty($meta['local']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">📍 {{ $meta['local'] }}</div>
                                    @endif
                                    @if(!empty($meta['combustivel']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">⛽ {{ $meta['combustivel'] }}L/100km</div>
                                    @endif
                                    @if(!empty($meta['estado']))
                                        <div class="text-xs font-semibold {{ $meta['estado'] === 'Crítico' ? 'text-red-600 dark:text-red-400' : ($meta['estado'] === 'Bom' ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400') }}">
                                            Estado: {{ $meta['estado'] }}
                                        </div>
                                    @endif

                                {{-- CASA --}}
                                @elseif($slug === 'casa' && !empty($meta))
                                    @if(!empty($meta['entidade']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $meta['entidade'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['piso']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">🏠 {{ $meta['piso'] }}</div>
                                    @endif
                                    @if(!empty($meta['valor_anterior']))
                                        @php $diff = $expense->amount - $meta['valor_anterior']; @endphp
                                        <div class="text-xs {{ $diff < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} font-semibold">
                                            {{ $diff < 0 ? '↓' : '↑' }} {{ abs($diff) > 0 ? number_format(abs($diff), 2, ',', '.') : '=' }}€ vs mês anterior
                                        </div>
                                    @endif
                                    @if(!empty($meta['anomalia']))
                                        <div class="text-xs text-orange-600 dark:text-orange-400 font-semibold">⚠️ Anomalia reportada</div>
                                    @endif
                                    @if(!empty($meta['referencia']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">📋 {{ $meta['referencia'] }}</div>
                                    @endif

                                {{-- ALIMENTAÇÃO --}}
                                @elseif($slug === 'alimentacao' && !empty($meta))
                                    @if(!empty($meta['pessoas']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                            @php $custoPessoa = $meta['pessoas'] > 0 ? $expense->amount / $meta['pessoas'] : $expense->amount; @endphp
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ number_format($custoPessoa, 2, ',', '.') }}€/pessoa ({{ $meta['pessoas'] }})</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['estabelecimento']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">🏪 {{ $meta['estabelecimento'] }}</div>
                                    @endif
                                    @if(!empty($meta['dieta']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">🥗 {{ $meta['dieta'] }}</div>
                                    @endif
                                    @if(!empty($meta['frequencia']))
                                        <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">{{ $meta['frequencia'] }}</div>
                                    @endif
                                    @if(!empty($meta['orcamento_pessoa']))
                                        @php $estaNoOrcamento = $meta['pessoas'] > 0 && ($expense->amount / $meta['pessoas']) <= $meta['orcamento_pessoa']; @endphp
                                        <div class="text-xs {{ $estaNoOrcamento ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }} font-semibold">
                                            {{ $estaNoOrcamento ? '✓ Dentro do orçamento' : '⚠️ Acima do orçamento' }} ({{ number_format($meta['orcamento_pessoa'], 2, ',', '.') }}€)
                                        </div>
                                    @endif

                                {{-- SAÚDE --}}
                                @elseif($slug === 'saude' && !empty($meta))
                                    @if(!empty($meta['profissional']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">👨‍⚕️ {{ $meta['profissional'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['especialidade']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">🩺 {{ $meta['especialidade'] }}</div>
                                    @endif
                                    @if(!empty($meta['cobertura_seguro']))
                                        <div class="text-xs font-semibold {{ strpos($meta['cobertura_seguro'], 'Sim') !== false ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }}">
                                            🛡️ {{ $meta['cobertura_seguro'] }}
                                        </div>
                                    @endif
                                    @if(!empty($meta['urgencia']))
                                        <div class="text-xs font-semibold {{ $meta['urgencia'] === 'Emergência' ? 'text-red-600 dark:text-red-400' : ($meta['urgencia'] === 'Urgente' ? 'text-orange-600 dark:text-orange-400' : 'text-blue-600 dark:text-blue-400') }}">
                                            {{ $meta['urgencia'] }}
                                        </div>
                                    @endif
                                    @if(!empty($meta['prescricao']))
                                        <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">✓ Com prescrição</div>
                                    @endif

                                {{-- TECNOLOGIA --}}
                                @elseif($slug === 'tecnologia' && !empty($meta))
                                    @if(!empty($meta['fornecedor']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">💼 {{ $meta['fornecedor'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['produtoServico']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">🔧 {{ $meta['produtoServico'] }}</div>
                                    @endif
                                    @if(!empty($meta['status']))
                                        <div class="text-xs font-semibold {{ $meta['status'] === 'Ativo' ? 'text-emerald-600 dark:text-emerald-400' : ($meta['status'] === 'Cancelado' ? 'text-red-600 dark:text-red-400' : 'text-zinc-600 dark:text-zinc-400') }}">
                                            Status: {{ $meta['status'] }}
                                        </div>
                                    @endif
                                    @if(!empty($meta['duracao']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">⏱️ {{ $meta['duracao'] }}</div>
                                    @endif
                                    @if(!empty($meta['roi_esperado']))
                                        <div class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">📈 ROI: {{ $meta['roi_esperado'] }}</div>
                                    @endif

                                {{-- EDUCAÇÃO --}}
                                @elseif($slug === 'educacao' && !empty($meta))
                                    @if(!empty($meta['instituicao']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">🎓 {{ $meta['instituicao'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['curso']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">📚 {{ $meta['curso'] }}</div>
                                    @endif
                                    @if(!empty($meta['nivel']))
                                        <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">🏆 {{ $meta['nivel'] }}</div>
                                    @endif
                                    @if(!empty($meta['estado_pagamento']))
                                        <div class="text-xs font-semibold {{ $meta['estado_pagamento'] === 'Pago' ? 'text-emerald-600 dark:text-emerald-400' : ($meta['estado_pagamento'] === 'Pendente' ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400') }}">
                                            💳 {{ $meta['estado_pagamento'] }}
                                        </div>
                                    @endif

                                {{-- EMPRÉSTIMOS --}}
                                @elseif($slug === 'emprestimos' && !empty($meta))
                                    @if(!empty($meta['credor']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">🏦 {{ $meta['credor'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['saldo_atual']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">Saldo: {{ number_format($meta['saldo_atual'], 2, ',', '.') }}€</div>
                                    @endif
                                    @if(!empty($meta['taxa_juros']))
                                        <div class="text-xs font-semibold text-orange-600 dark:text-orange-400">📊 Taxa: {{ number_format($meta['taxa_juros'], 2, ',', '.') }}%</div>
                                    @endif
                                    @if(!empty($meta['data_termino']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">📅 Término: {{ $meta['data_termino'] }}</div>
                                    @endif

                                {{-- SEGUROS --}}
                                @elseif($slug === 'seguros' && !empty($meta))
                                    @if(!empty($meta['tipo_seguro']))
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">🛡️ {{ $meta['tipo_seguro'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($meta['seguradora']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400 italic">🏢 {{ $meta['seguradora'] }}</div>
                                    @endif
                                    @if(!empty($meta['numero_apolice']))
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">📋 {{ $meta['numero_apolice'] }}</div>
                                    @endif
                                    @if(!empty($meta['cobertura_valor']))
                                        <div class="text-xs font-semibold text-sky-600 dark:text-sky-400">💰 Cobertura: {{ number_format($meta['cobertura_valor'], 0, ',', '.') }}€</div>
                                    @endif
                                    @if(!empty($meta['estado']))
                                        <div class="text-xs font-semibold {{ $meta['estado'] === 'Ativa' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            Status: {{ $meta['estado'] }}
                                        </div>
                                    @endif
                                @else
                                    @if(!empty($meta))
                                        <div class="text-xs text-zinc-500 italic">{{ count($meta) }} campo(s) registado(s)</div>
                                    @else
                                        <div class="text-xs text-zinc-400 italic">—</div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="p-6 align-top">
                            @if($expense->description)
                            <div class="relative pl-4 py-1">
                                <div class="absolute left-0 top-0 bottom-0 w-0.5 {{ str_replace('text','bg',$hubTheme['color']) }}/40 rounded-full"></div>
                                <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300 italic line-clamp-2">"{{ $expense->description }}"</p>
                            </div>
                            @else
                            <span class="text-xs text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="p-6 text-right px-8 align-middle whitespace-nowrap">
                            <span class="text-sm font-black text-red-500 tracking-wide italic block">-{{ number_format($expense->amount,2,',',' ') }}€</span>
                            <button wire:click="deleteExpense({{ $expense->id }})" wire:confirm="Tem a certeza?" class="mt-3 p-1.5 text-zinc-300 hover:text-red-500 opacity-0 group-hover/row:opacity-100 transition-all inline-block">
                                <flux:icon name="trash" variant="mini" class="size-4" />
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-32 text-center text-zinc-400 italic">Sem movimentos registados neste Hub.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1 — SCANNER IA                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="ai-scanner-modal" position="center">
        <div class="relative p-10 bg-zinc-950 rounded-[3rem] border border-white/10 shadow-2xl space-y-8 overflow-hidden">

            {{-- Loader --}}
            <div wire:loading wire:target="scanReceiptWithAI"
                 class="absolute inset-0 bg-zinc-950/95 backdrop-blur-md z-50 flex flex-col items-center justify-center rounded-[3rem] gap-6">
                <div class="relative">
                    <div class="size-20 border-4 border-brand-500/20 border-t-brand-500 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <flux:icon name="sparkles" class="size-7 text-brand-400 animate-pulse" />
                    </div>
                </div>
                <div class="text-center space-y-1">
                    <p class="text-brand-400 font-black uppercase tracking-[0.4em] text-[10px]">IA a processar fatura...</p>
                    <p class="text-zinc-600 text-[9px] uppercase tracking-widest">Extração de dados em curso</p>
                </div>
            </div>

            <div class="absolute -top-24 -left-24 size-64 bg-brand-500/10 blur-[100px] rounded-full animate-pulse pointer-events-none"></div>

            {{-- Cabeçalho --}}
            <div class="text-center space-y-2">
                <div class="inline-flex p-4 bg-brand-500/10 rounded-2xl text-brand-400">
                    <flux:icon name="sparkles" class="size-8" />
                </div>
                <flux:heading size="xl" class="text-white font-black uppercase italic tracking-tighter leading-none">Scanner IA Vision</flux:heading>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest italic">Protocolo de Extração Automática</p>
            </div>

            {{-- Erro do scan --}}
            @if($scanError)
            <div class="flex items-start gap-3 bg-red-500/10 border border-red-500/20 rounded-2xl px-5 py-4">
                <flux:icon name="exclamation-triangle" class="size-5 text-red-400 shrink-0 mt-0.5" />
                <p class="text-sm text-red-300 font-medium">{{ $scanError }}</p>
            </div>
            @endif

            {{-- Dropzone --}}
            <div class="relative bg-zinc-900/50 rounded-[2.5rem] p-10 border-2 border-dashed border-zinc-800 hover:border-brand-500/50 transition-all text-center group/drop">
                {{--
                    IMPORTANTE: o input NÃO usa wire:model para evitar conflitos com o Alpine preview.
                    O upload é feito manualmente via wire:model.live após selecção.
                --}}
                <input
                    type="file"
                    accept="image/*,.pdf"
                    x-ref="fileInput"
                    @change="
                        const file = $event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = e => { scannerPreview = e.target.result };
                            reader.readAsDataURL(file);
                        }
                    "
                    wire:model="receipt"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30"
                >

                <div class="relative z-10 pointer-events-none">
                    <template x-if="scannerPreview">
                        <div class="relative inline-block">
                            <img :src="scannerPreview" class="size-44 object-cover rounded-3xl border-4 border-brand-500/50 shadow-2xl mx-auto">
                            <div class="absolute -top-3 -right-3 size-10 bg-emerald-500 text-white rounded-full flex items-center justify-center border-4 border-zinc-950 shadow-lg">
                                <flux:icon name="check" variant="mini" />
                            </div>
                        </div>
                    </template>
                    <template x-if="!scannerPreview">
                        <div class="py-4 space-y-4">
                            <div class="size-20 bg-zinc-800/50 rounded-[1.5rem] flex items-center justify-center mx-auto border border-white/5 group-hover/drop:bg-brand-500/10 transition-colors">
                                <flux:icon name="camera" class="size-10 text-zinc-600 group-hover/drop:text-brand-400" />
                            </div>
                            <div>
                                <p class="text-sm font-black text-white uppercase tracking-tight italic">Anexar Documento</p>
                                <p class="text-[10px] text-zinc-600 mt-1 uppercase tracking-widest">Foto de fatura, recibo ou talão</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Scan line animada --}}
            <div wire:loading wire:target="scanReceiptWithAI" class="absolute inset-x-0 pointer-events-none h-1 animate-scan-line z-20"></div>

            {{-- Botões --}}
            <div class="flex gap-4" wire:loading.remove wire:target="scanReceiptWithAI">
                <flux:modal.close class="flex-1">
                    <button type="button"
                        x-on:click="scannerPreview = null"
                        class="w-full h-14 text-zinc-600 font-bold uppercase text-xs hover:text-white transition tracking-widest">
                        Cancelar
                    </button>
                </flux:modal.close>

                {{--
                    Botão separado do Alpine e do wire:loading para evitar dead-lock.
                    Fica desabilitado só enquanto não há preview OU enquanto o Livewire está a processar.
                --}}
                <button
                    type="button"
                    wire:click="scanReceiptWithAI"
                    x-bind:disabled="!scannerPreview"
                    x-bind:class="!scannerPreview ? 'opacity-30 cursor-not-allowed' : 'hover:bg-brand-500 cursor-pointer'"
                    class="flex-[2] h-14 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 transition-all text-sm"
                >
                    Iniciar Extração
                </button>
            </div>

            {{-- Estado de loading nos botões --}}
            <div wire:loading wire:target="scanReceiptWithAI" class="flex gap-4">
                <div class="flex-1 h-14"></div>
                <div class="flex-[2] h-14 rounded-2xl bg-brand-600/50 text-white/50 font-black uppercase tracking-widest text-sm flex items-center justify-center gap-3">
                    <div class="size-4 border-2 border-white/20 border-t-white/80 rounded-full animate-spin"></div>
                    A processar...
                </div>
            </div>
        </div>
    </flux:modal>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2 — REVISÃO IA                                          --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="ai-review-modal" position="center">
        <div class="relative bg-zinc-950 rounded-[2.5rem] shadow-2xl border border-white/10 overflow-hidden">
            <div class="absolute -top-32 -right-32 size-96 bg-brand-500/10 blur-[120px] rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 size-80 bg-emerald-500/5 blur-[100px] rounded-full pointer-events-none"></div>

            {{-- Header --}}
            <div class="relative p-8 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <flux:icon name="sparkles" class="size-6 text-emerald-400" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-white uppercase italic tracking-tighter leading-none">Revisão IA</h2>
                        <p class="text-[10px] text-zinc-500 mt-1 font-bold uppercase tracking-widest">Confirme os dados extraídos automaticamente</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-3 py-1.5">
                        <div class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">IA Analisada</span>
                    </div>
                    <flux:modal.close>
                        <button class="p-2 rounded-full hover:bg-white/5 text-zinc-500 transition-colors">
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </flux:modal.close>
                </div>
            </div>

            @if(!empty($scannedData))
            <div class="p-8 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">

                {{-- Valor principal --}}
                <div class="relative overflow-hidden bg-white/3 border border-white/8 rounded-[2rem] p-8 text-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 to-transparent pointer-events-none"></div>
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em] mb-2">Valor Total Extraído</p>
                    <p class="text-7xl font-black text-white tracking-tighter italic leading-none">
                        {{ number_format($scannedData['amount'], 2, ',', '.') }}€
                    </p>
                    @if(!empty($scannedData['tax']))
                    <p class="text-xs text-zinc-500 mt-3 uppercase tracking-widest font-bold">
                        IVA incluído: {{ number_format($scannedData['tax'], 2, ',', '.') }}€
                    </p>
                    @endif
                </div>

                {{-- Grid de metadados --}}
                <div class="grid grid-cols-2 gap-3">

                    <div class="col-span-2 bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-brand-500/10 shrink-0">
                            <flux:icon name="building-storefront" class="size-5 text-brand-400" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Estabelecimento</p>
                            <p class="text-base font-black text-white truncate italic">{{ $scannedData['store'] ?: '—' }}</p>
                        </div>
                    </div>

                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="calendar" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Data</p>
                            <p class="text-sm font-black text-white">{{ \Carbon\Carbon::parse($scannedData['date'])->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="tag" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Categoria</p>
                            <p class="text-sm font-black {{ $hubTheme['color'] }} uppercase">{{ $scannedData['subcategory'] }}</p>
                        </div>
                    </div>

                    @if(!empty($scannedData['payment_method']) && $scannedData['payment_method'] !== 'desconhecido')
                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="credit-card" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Pagamento</p>
                            <p class="text-sm font-black text-white uppercase">{{ $scannedData['payment_method'] }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($scannedData['invoice_number']))
                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="bars-3" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Nº Fatura</p>
                            <p class="text-sm font-black text-white font-mono">{{ $scannedData['invoice_number'] }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($scannedData['nif_emitter']))
                    <div class="bg-white/3 border border-white/8 rounded-2xl p-5 flex items-center gap-4">
                        <div class="p-2.5 rounded-xl bg-zinc-800 shrink-0">
                            <flux:icon name="identification" class="size-5 text-zinc-400" />
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">NIF Emitente</p>
                            <p class="text-sm font-black text-white font-mono">{{ $scannedData['nif_emitter'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Artigos detetados --}}
                @if(!empty($scannedData['items']) && count($scannedData['items']) > 0)
                <div class="bg-white/3 border border-white/8 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3 border-b border-white/5 flex items-center gap-2">
                        <div class="size-1.5 rounded-full bg-brand-500"></div>
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em]">Artigos Detetados ({{ count($scannedData['items']) }})</p>
                    </div>
                    <div class="divide-y divide-white/5">
                        @foreach($scannedData['items'] as $item)
                        <div class="px-5 py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                @if(!empty($item['qty']) && $item['qty'] > 1)
                                    <span class="shrink-0 text-[9px] font-black text-brand-400 bg-brand-500/10 rounded-md px-2 py-0.5">×{{ $item['qty'] }}</span>
                                @endif
                                <p class="text-sm text-zinc-300 font-medium truncate">{{ $item['name'] ?? '—' }}</p>
                            </div>
                            @if(!empty($item['price']))
                            <span class="shrink-0 text-sm font-black text-white whitespace-nowrap">{{ number_format($item['price'],2,',','.') }}€</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Notas --}}
                @if(!empty($scannedData['notes']))
                <div class="flex gap-3 bg-amber-500/5 border border-amber-500/10 rounded-2xl px-5 py-4">
                    <flux:icon name="light-bulb" class="size-4 text-amber-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-zinc-400 italic leading-relaxed">{{ $scannedData['notes'] }}</p>
                </div>
                @endif

                <div class="flex gap-3 bg-white/3 border border-white/5 rounded-2xl px-5 py-4">
                    <flux:icon name="pencil-square" class="size-4 text-zinc-500 shrink-0 mt-0.5" />
                    <p class="text-[10px] text-zinc-500 leading-relaxed uppercase tracking-wide font-bold">Pode corrigir qualquer campo no passo seguinte antes de guardar.</p>
                </div>
            </div>

            <div class="px-8 pb-8 pt-4 flex items-center gap-4">
                <flux:modal.close class="flex-1">
                    <button type="button" class="w-full h-14 rounded-2xl font-bold text-sm text-zinc-500 hover:text-white hover:bg-white/5 transition-all tracking-widest uppercase border border-white/5">
                        Cancelar
                    </button>
                </flux:modal.close>
                <button
                    wire:click="confirmScannedData"
                    class="flex-[2] h-14 rounded-2xl bg-brand-600 hover:bg-brand-500 text-white font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 transition-all text-sm flex items-center justify-center gap-3"
                >
                    <flux:icon name="check-circle" class="size-5" />
                    Confirmar e Editar Registo
                </button>
            </div>
            @else
            <div class="p-16 text-center text-zinc-500 italic">Nenhum dado disponível.</div>
            @endif
        </div>
    </flux:modal>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 3 — REGISTO MANUAL                                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <flux:modal name="add-expense-modal" position="center">
        <div class="relative bg-white dark:bg-zinc-950 rounded-[2.5rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="absolute -top-24 -right-24 size-72 bg-brand-500/10 blur-[120px] rounded-full pointer-events-none"></div>

            <div class="relative p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-white/50 dark:bg-zinc-950/50 backdrop-blur-xl">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-500/20">
                        <flux:icon name="plus" class="size-5" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white leading-none italic uppercase">Confirmar Registo</h2>
                        <p class="text-[11px] text-zinc-500 mt-1.5 font-medium uppercase tracking-wider">{{ $title }} • Transação Validada</p>
                    </div>
                </div>
                <flux:modal.close>
                    <button class="p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 transition-colors">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </flux:modal.close>
            </div>

            <form wire:submit="save" class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-bold uppercase tracking-widest text-brand-600 z-10">Valor ({{ $currency }})</label>
                        <div class="relative">
                            <flux:input wire:model="amount" type="number" step="0.01" placeholder="0,00"
                                class="!h-20 !text-3xl !font-black !pl-6 !rounded-3xl !border-2 focus:!border-brand-500 !bg-zinc-50/50 dark:!bg-zinc-900/50 shadow-inner" />
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-300 dark:text-zinc-700">
                                <flux:icon name="banknotes" class="size-8" />
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-zinc-950 text-[10px] font-black uppercase tracking-widest text-zinc-400 z-10">Data do Gasto</label>
                        <flux:input wire:model="spent_at" type="date"
                            class="!h-20 !font-bold !text-lg !rounded-3xl !border-2 focus:!border-brand-500 !bg-zinc-50/50 dark:!bg-zinc-900/50 shadow-inner" />
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Classificação</flux:label>
                    <flux:select wire:model="subcategory" class="h-14 !rounded-2xl font-bold text-base border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900">
                        <option value="">Selecione categoria...</option>
                        @foreach($subcategories as $sub)
                        <option value="{{ $sub }}">{{ ucfirst(strtolower($sub)) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="p-6 rounded-[2rem] bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg {{ str_replace('text','bg',$hubTheme['color']) }}/10">
                            <flux:icon name="{{ $categoryFields[$slug]['icon'] ?? 'tag' }}" class="size-4 {{ $hubTheme['color'] }}" />
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600 dark:text-zinc-400 italic">Informações Específicas</p>
                            <p class="text-xs text-zinc-500 mt-0.5">Personalize de acordo com a categoria</p>
                        </div>
                    </div>

                    @if(isset($categoryFields[$slug]))
                        <div class="space-y-3 pt-2">
                            @foreach($categoryFields[$slug]['fields'] as $field)
                                @if($field['type'] === 'select')
                                    <div class="space-y-1.5">
                                        <flux:label class="text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wide">
                                            {{ $field['label'] }}
                                        </flux:label>
                                        <flux:select wire:model="meta.{{ $field['name'] }}"
                                            class="h-11 !rounded-xl font-semibold text-sm border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                                            <option value="">Selecione...</option>
                                            @foreach($field['options'] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </flux:select>
                                    </div>
                                @elseif($field['type'] === 'number')
                                    <flux:input wire:model="meta.{{ $field['name'] }}"
                                        type="number"
                                        label="{{ $field['label'] }}"
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        @if(isset($field['icon'])) icon="{{ $field['icon'] }}" @endif
                                        class="rounded-xl" />
                                @elseif($field['type'] === 'checkbox')
                                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-900/80 transition-colors">
                                        <input type="checkbox" wire:model="meta.{{ $field['name'] }}" class="w-4 h-4 rounded cursor-pointer">
                                        <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $field['label_alt'] ?? $field['label'] }}</span>
                                    </label>
                                @else
                                    <flux:input wire:model="meta.{{ $field['name'] }}"
                                        type="text"
                                        label="{{ $field['label'] }}"
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        @if(isset($field['icon'])) icon="{{ $field['icon'] }}" @endif
                                        class="rounded-xl" />
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:textarea wire:model="description" rows="2" placeholder="Observações adicionais (ex: nome da loja, notas importantes)..."
                            class="!rounded-xl !bg-white dark:!bg-zinc-950 border-zinc-200 dark:border-zinc-800 font-medium italic text-sm" />
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <flux:modal.close class="flex-1">
                        <button type="button" class="w-full h-16 rounded-2xl font-bold text-base text-zinc-500 hover:text-zinc-800 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all tracking-widest uppercase">Cancelar</button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary"
                        class="flex-[1.5] h-16 !rounded-2xl !font-black !text-base !uppercase !tracking-widest !bg-brand-600 hover:!bg-brand-700 shadow-xl shadow-brand-500/20 border-none">
                        Confirmar
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- ── RODAPÉ ─────────────────────────────────────────────────── --}}
    <footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <div class="flex flex-col items-center gap-4">
            <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center border border-zinc-200 dark:border-zinc-800">
                <flux:icon name="shield-check" class="size-4 text-zinc-400" />
            </div>
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em] leading-relaxed">
                © {{ date('Y') }} {{ config('app.name') }}<br>
                Protocolo Hub Inteligente v2.6 • {{ $title }}
            </p>
        </div>
    </footer>

    <style>
        @keyframes scan {
            0%   { top: 0%;   opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan-line {
            position: absolute; width: 100%;
            animation: scan 2.5s infinite ease-in-out;
            background: linear-gradient(to bottom, transparent, rgba(59,130,246,0.5), transparent);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
        [portal] { z-index: 100 !important; }
    </style>
</div>

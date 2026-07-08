<div x-data="{ privacyMode: false }" class="space-y-10 pb-20">

    {{-- 1. HEADER PREMIUM --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 text-brand-600">
                        <flux:icon name="briefcase" class="w-10 h-10" />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                            Gestão de Projetos
                        </h1>
                        <flux:badge variant="neutral"
                            class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">
                            Operações Ativas
                        </flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">
                        Controlo de rentabilidade, orçamentos e prazos por unidade de trabalho
                    </p>
                </div>
            </div>

            {{-- Barra de pesquisa + botão --}}
            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="relative flex-1 min-w-[240px]">
                    <flux:input wire:model.live="search" icon="magnifying-glass"
                        placeholder="Procurar projeto..."
                        class="!bg-transparent border-none shadow-none" />
                </div>
                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:modal.trigger name="project-modal">
                    <flux:button variant="primary" icon="plus"
                        class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Novo Projeto
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- 2. KPIs OPERACIONAIS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        {{-- Pipeline Total --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400">
                        <flux:icon name="presentation-chart-line" variant="outline" class="size-6" />
                    </div>
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest">
                        Valor em Carteira
                    </span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">
                    Pipeline Total (Budgets)
                </p>
                <h3 class="text-4xl font-black text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''"
                        class="transition-all duration-500 inline-block">
                        {{ number_format($totalBudget, 2, ',', ' ') }} €
                    </span>
                </h3>
            </div>
        </div>

        {{-- Projetos Ativos --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-brand-500/30 transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">
                Projetos Ativos
            </p>
            <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    {{ $activeCount }}
                </span>
            </h3>
        </div>

        {{-- Margem Média --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-emerald-500/30 transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">
                Margem Média
            </p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    {{ round($avgMargin) }}%
                </span>
            </h3>
        </div>

        {{-- Rentabilidade Total --}}
        <div class="glass-card bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-emerald-500/30 transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">
                Rentabilidade Total
            </p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    {{ number_format($projects->sum('profit'), 2, ',', ' ') }} €
                </span>
            </h3>
        </div>
    </div>

    {{-- 3. GRELHA DE PROJETOS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      @forelse($projects as $project)
    @php
        $margin = min(max($project->margin, 0), 100);
        $isProfitable = $project->profit >= 0;
    @endphp

    {{-- 1. ADICIONA x-data PARA CONTROLAR O MENU E wire:key PARA O LIVEWIRE --}}
    <div wire:key="project-card-{{ $project->id }}"
         x-data="{ menuOpen: false }"
         class="glass-card p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-brand-500/30 transition-all group relative">

        <div class="flex justify-between items-start mb-8 gap-4">
            <div class="flex-1 min-w-0 text-left">
                <span class="inline-flex px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 font-black uppercase text-[8px] tracking-widest rounded-lg border border-zinc-200 dark:border-zinc-700 mb-3">
                    {{ $project->status }}
                </span>

                <h3 class="text-xl font-black dark:text-white uppercase tracking-tight leading-none group-hover:text-brand-600 transition-colors truncate">
                    {{ $project->name }}
                </h3>

                @if($project->client)
                    <div class="flex items-center gap-1.5 mt-2">
                        <flux:icon name="building-office" variant="micro" class="size-3 text-brand-500" />
                        <span class="text-[10px] font-black text-brand-600 dark:text-brand-400 uppercase tracking-tighter">
                            {{ $project->client->name }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2 shrink-0 relative">
                {{-- SELECT DO CLIENTE --}}
                <div class="w-32 md:w-44">
                    <flux:select
                        wire:change="updateProjectClient({{ $project->id }}, $event.target.value)"
                        class="font-black uppercase text-[9px] !bg-zinc-50 dark:!bg-zinc-800 !border-none rounded-xl h-10 shadow-sm"
                    >
                        <option value="">Sem Cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected($project->client_id == $client->id)>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- MENU MANUAL (ESTILO TEAM HUB) --}}
                <div class="relative">
                    <button type="button" @click.stop="menuOpen = !menuOpen"
                        class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white p-2 transition-colors cursor-pointer">
                        <flux:icon name="ellipsis-horizontal" class="size-5" />
                    </button>

                    <div
                        x-show="menuOpen"
                        x-cloak
                        @click.outside="menuOpen = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-10 w-48 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-xl z-[100] overflow-hidden"
                    >
                        <div class="p-1.5 space-y-0.5">
                            <button
                                type="button"
                                @click="menuOpen = false"
                                wire:click="edit({{ $project->id }})"
                                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-brand-50 dark:hover:bg-brand-500/10 hover:text-brand-600 transition-all text-left"
                            >
                                <flux:icon name="pencil-square" class="size-4 text-brand-500" />
                                Editar Projeto
                            </button>

                            <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>

                            <button
                                type="button"
                                @click="menuOpen = false"
                                wire:click="delete({{ $project->id }})"
                                wire:confirm="Eliminar permanentemente este projeto?"
                                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-left"
                            >
                                <flux:icon name="trash" class="size-4 text-red-500" />
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                {{-- Rentabilidade --}}
                <div class="space-y-5">
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">
                                Eficiência de Margem
                            </span>
                            <span class="text-xs font-bold {{ $isProfitable ? 'text-emerald-500' : 'text-red-500' }} mt-1">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                                    class="transition-all duration-500">
                                    {{ number_format($project->profit, 2, ',', ' ') }} € Lucro
                                </span>
                            </span>
                        </div>
                        <span class="text-2xl font-black {{ $isProfitable ? 'text-emerald-500' : 'text-red-500' }} tracking-tighter italic">
                            {{ round($project->margin) }}%
                        </span>
                    </div>

                    {{-- Barra de progresso --}}
                    <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-inner">
                        <div class="h-full {{ $isProfitable ? 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)]' : 'bg-red-500 shadow-[0_0_12px_rgba(239,68,68,0.5)]' }} transition-all duration-1000 ease-out"
                            style="width: {{ $margin }}%">
                        </div>
                    </div>

                    {{-- Custos vs Receita --}}
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase mb-1">Custo Total</p>
                            <p class="text-sm font-black text-red-500">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                                    class="transition-all duration-500">
                                    -{{ number_format($project->costs, 2, ',', ' ') }} €
                                </span>
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase mb-1">Receita Gerada</p>
                            <p class="text-sm font-black text-emerald-600">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                                    class="transition-all duration-500">
                                    +{{ number_format($project->revenue, 2, ',', ' ') }} €
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Deadline --}}
                @if($project->deadline)
                    <div class="mt-8 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="calendar" class="size-3 text-zinc-400" />
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                                Entrega Final:
                            </span>
                        </div>
                        <span class="text-[10px] font-black dark:text-zinc-300 uppercase {{ \Carbon\Carbon::parse($project->deadline)->isPast() ? 'text-red-500' : '' }}">
                            {{ \Carbon\Carbon::parse($project->deadline)->translatedFormat('d M, Y') }}
                        </span>
                    </div>
                @endif
            </div>

        @empty
            <div class="col-span-full py-24 text-center">
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 shadow-inner">
                        <flux:icon name="presentation-chart-bar" class="size-12 text-zinc-200 dark:text-zinc-700" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.3em]">
                            Pipeline Vazio
                        </p>
                        <p class="text-zinc-400 text-xs italic font-medium">
                            Cria o teu primeiro projeto para começar a monitorizar margens.
                        </p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 4. MODAL DE PROJETO --}}
<flux:modal name="project-modal" position="center" class="md:w-[650px] !p-0 overflow-visible">
    <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

        <div class="absolute top-6 right-6">
            <flux:modal.close>
                <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
            </flux:modal.close>
        </div>

        {{-- Cabeçalho --}}
        <div class="flex items-center gap-4">
            <div class="p-3 bg-zinc-900 dark:bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                <flux:icon name="briefcase" class="size-6" />
            </div>
            <div>
                <flux:heading size="xl"
                    class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                    {{ $editingId ? 'Editar Projeto' : 'Novo Projeto' }}
                </flux:heading>
                <p class="text-xs text-zinc-400 font-medium">
                    Define os parâmetros operacionais e financeiros do projeto.
                </p>
            </div>
        </div>

        <div class="space-y-8">

            {{-- Nome --}}
            <div class="space-y-2">
                <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                    Nome do Projeto
                </flux:label>
                <flux:input wire:model="name"
                    placeholder="Ex: Auditoria Financeira Q4"
                    class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
            </div>

            {{-- Descrição --}}
            <div class="space-y-2">
                <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                    Descrição
                </flux:label>
                <flux:textarea wire:model="description" rows="3"
                    placeholder="Descreve os objetivos principais..."
                    class="rounded-2xl shadow-sm border-none !bg-zinc-50 dark:!bg-zinc-900 text-sm p-4" />
            </div>

            {{-- Responsável --}}
            <div class="space-y-2">
                <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
                    Responsável pela Operação
                </flux:label>
                <flux:select wire:model="manager_id"
                    class="font-black uppercase text-[10px] !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
                    <option value="">Selecionar Responsável...</option>
                    @foreach($team as $member)
                        <option value="{{ $member->id }}">👤 {{ $member->name }}</option>
                    @endforeach
                </flux:select>
            </div>
{{-- ADICIONA ESTE BLOCO AQUI PARA O CLIENTE --}}
<div class="space-y-2">
    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">
        Cliente Associado
    </flux:label>
    <flux:select wire:model="client_id"
        class="font-black uppercase text-[10px] !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm">
        <option value="">-- Sem Cliente (Projeto Interno) --</option>
        @foreach($clients as $client)
            <option value="{{ $client->id }}">🏢 {{ $client->name }}</option>
        @endforeach
    </flux:select>
</div>
            {{-- Painel Financeiro --}}
            <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 shadow-inner">

    {{-- Título --}}
    <div class="flex items-center gap-2">
        <flux:icon name="presentation-chart-line" class="size-3 text-brand-500" />
        <p class="text-[9px] font-black uppercase text-brand-600 tracking-[0.2em]">
            Planeamento Financeiro
        </p>
    </div>

    {{-- Grelha Financeira --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        {{-- Orçamento --}}
        <div class="space-y-2 md:col-span-1">
            <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest text-center">
                Orçamento(€)
            </flux:label>
            <flux:input
                wire:model="budget"
                type="number"
                step="0.01"
                class="font-black text-xl text-center text-brand-600
                       !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
                placeholder="0,00"
            />
        </div>

       {{-- Receita --}}
<div class="space-y-2 md:col-span-1">
    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest text-center">
        Receita (€)
    </flux:label>
    <flux:input
        wire:model.live="revenue"
        type="number"
        step="0.01"
        class="font-black text-xl text-center text-emerald-600
               !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
        placeholder="0,00"
    />
</div>

{{-- Custos --}}
<div class="space-y-2 md:col-span-1">
    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest text-center">
        Custos (€)
    </flux:label>
    <flux:input
        wire:model.live="costs"
        type="number"
        step="0.01"
        class="font-black text-xl text-center text-red-500
               !bg-white dark:!bg-zinc-950 !border-none rounded-xl h-12 shadow-sm"
        placeholder="0,00"
    />
</div>

        {{-- Margem (automática + tooltip premium) --}}
        <div class="space-y-2 md:col-span-1">

            {{-- Label + Ícone --}}
            <div class="flex items-center justify-center gap-2">
                <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest text-center">
                    Margem (%)
                </flux:label>

                {{-- Ícone + Tooltip --}}
                <div class="relative group">
                    <flux:icon name="information-circle"
                        class="size-4 text-brand-600 cursor-pointer transition-all duration-300 group-hover:text-brand-700" />

                    <div class="absolute left-1/2 -translate-x-1/2 mt-2 w-48
                                bg-zinc-900 text-white text-[10px] font-bold p-3 rounded-xl shadow-xl
                                opacity-0 group-hover:opacity-100 pointer-events-none
                                transition-all duration-300 z-50">
                        Margem = (Lucro / Receita) × 100
                    </div>
                </div>
            </div>

            {{-- Campo bloqueado --}}
            <flux:input
                wire:model="margin"
                type="number"
                step="0.01"
                class="font-black text-xl text-center text-brand-600
                       !bg-zinc-950/20 dark:!bg-zinc-900/40
                       !border-none rounded-xl h-12 shadow-inner
                       opacity-60 cursor-not-allowed select-none"
                placeholder="0"
                readonly
            />
        </div>

    </div>
</div>











        </div>

        {{-- Ações Finais --}}
        <div class="flex gap-4 pt-4">
            <flux:modal.close>
                <flux:button variant="ghost"
                    class="flex-1 font-black uppercase text-[10px] text-zinc-400">
                    Cancelar
                </flux:button>
            </flux:modal.close>

            <flux:button wire:click="save" variant="primary"
                class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl">
                {{ $editingId ? 'Atualizar Projeto' : 'Criar Projeto' }}
            </flux:button>
        </div>

    </div>
</flux:modal>


    {{-- Rodapé --}}
    <footer class="pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Terminal de Gestão de Projetos
        </p>
    </footer>

</div>


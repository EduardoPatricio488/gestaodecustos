<div class="space-y-10 pb-20">
    @php
        $cycleLabels = ['monthly' => 'Mensal', 'quarterly' => 'Trimestral', 'semiannual' => 'Semestral', 'annual' => 'Anual'];
        $statusLabels = ['active' => 'Ativa', 'paused' => 'Pausada', 'cancelled' => 'Cancelada'];
        $paymentLabels = ['card' => 'Cartao', 'direct_debit' => 'Debito direto', 'bank_transfer' => 'Transferencia', 'paypal' => 'PayPal', 'cash' => 'Numerario'];
        $statusStyles = [
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20',
            'paused' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:border-rose-500/20',
        ];
    @endphp

    {{-- HEADER --}}
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
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Assinaturas</h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1">Custos Fixos</flux:badge>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">
                        Monitorizacao de debitos, renovacoes, ciclos e lembretes.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:button
    wire:click="openExtraModal"
    variant="primary"
    icon="plus"
    class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
    Nova Assinatura
</flux:button>
            </div>
        </header>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <div class="xl:col-span-2 stat-card bg-zinc-950 text-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800">
            <div class="absolute -right-10 -top-10 size-40 bg-brand-500/10 blur-3xl rounded-full"></div>
            <div class="relative z-10 flex h-full flex-col justify-between gap-8">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-400 mb-2">Custo mensal equivalente</p>
                    <h3 class="text-5xl font-black tracking-tighter italic text-white">
                        {{ number_format($totalMonthly, 2, ',', ' ') }} <small class="text-xl not-italic ml-1">E U R</small>
                    </h3>
                    <p class="mt-3 text-xs font-bold text-zinc-400 uppercase tracking-widest">
                        {{ number_format($totalAnnual, 2, ',', ' ') }} EUR / ano
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/5 p-4 border border-white/10">
                        <p class="text-[9px] uppercase tracking-widest text-zinc-500 font-black">Ativas</p>
                        <p class="text-2xl font-black">{{ $activeCount }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 border border-white/10">
                        <p class="text-[9px] uppercase tracking-widest text-zinc-500 font-black">Pausadas</p>
                        <p class="text-2xl font-black">{{ $pausedCount }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 border border-white/10">
                        <p class="text-[9px] uppercase tracking-widest text-zinc-500 font-black">Cancel.</p>
                        <p class="text-2xl font-black">{{ $cancelledCount }}</p>
                    </div>
                </div>
            </div>
            <flux:icon name="banknotes" class="absolute -right-4 -bottom-4 size-24 text-white/5 -rotate-12" />
        </div>

        <div class="glass-card p-7 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Por pagar este mes</p>
            <h3 class="text-4xl font-black text-orange-500 tracking-tighter italic">{{ number_format($upcoming, 2, ',', ' ') }} EUR</h3>
            <p class="mt-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Media por assinatura</p>
            <p class="text-xl font-black dark:text-white">{{ number_format($averageMonthly, 2, ',', ' ') }} EUR</p>
        </div>

        <div class="glass-card p-7 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-1">Proximo debito</p>
            @if($nextSub)
                <h3 class="text-2xl font-black dark:text-white tracking-tight">{{ $nextSub->name }}</h3>
                <p class="mt-2 text-sm font-bold text-brand-600">{{ $nextSub->next_billing_date->format('d/m/Y') }}</p>
                <p class="mt-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Daqui a {{ $nextSub->days_until_billing }} dias</p>
            @else
                <h3 class="text-2xl font-black dark:text-white tracking-tight">Sem debitos</h3>
                <p class="mt-2 text-sm font-bold text-zinc-500">Adiciona a primeira assinatura.</p>
            @endif
        </div>
    </div>

    {{-- FILTROS + LISTA --}}
    <div class="space-y-6">
        <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-4 sm:p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
                <label class="xl:col-span-2">
                    <span class="sr-only">Pesquisar</span>
                    <div class="relative">
                        <flux:icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-zinc-400" />
                        <input
                            wire:model.live.debounce.250ms="search"
                            type="search"
                            placeholder="Pesquisar por nome ou notas..."
                            class="w-full h-12 rounded-2xl border-0 bg-zinc-50 pl-11 pr-4 text-sm font-bold outline-none ring-0 transition focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white">
                    </div>
                </label>

               <select wire:model.live="categoryFilter" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white">
    <option value="all">Todas as Assinaturas</option>
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
    @endforeach
</select>

                <select wire:model.live="statusFilter" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white">
                    <option value="all">Todos estados</option>
                    <option value="active">Ativas</option>
                    <option value="paused">Pausadas</option>
                    <option value="cancelled">Canceladas</option>
                </select>

                <select wire:model.live="cycleFilter" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white">
                    <option value="all">Todos ciclos</option>
                    <option value="monthly">Mensal</option>
                    <option value="quarterly">Trimestral</option>
                    <option value="semiannual">Semestral</option>
                    <option value="annual">Anual</option>
                </select>

                <select wire:model.live="sortBy" class="h-12 rounded-2xl border-0 bg-zinc-50 px-4 text-xs font-black uppercase tracking-widest outline-none focus:ring-2 focus:ring-brand-500/40 dark:bg-zinc-950 dark:text-white">
                    <option value="billing_day">Dia de debito</option>
                    <option value="next_billing">Mais proxima</option>
                    <option value="amount_desc">Maior valor</option>
                    <option value="amount_asc">Menor valor</option>
                    <option value="name">Nome</option>
                </select>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <button wire:click="$set('amountFilter', 'all')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === 'all' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">Todos valores</button>
                <button wire:click="$set('amountFilter', 'under_10')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === 'under_10' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">Ate 10 EUR</button>
                <button wire:click="$set('amountFilter', '10_30')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === '10_30' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">10 a 30 EUR</button>
                <button wire:click="$set('amountFilter', 'over_30')" class="h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ $amountFilter === 'over_30' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950' : 'border-zinc-200 dark:border-zinc-800 text-zinc-500' }}">Mais de 30 EUR</button>
                <button wire:click="resetFilters" class="ml-auto h-9 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-500/10">Limpar filtros</button>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 px-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="queue-list" variant="outline" class="size-4" />
                </div>
                <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">Assinaturas encontradas</h2>
            </div>
            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ $subscriptions->count() }} resultados</span>
        </div>

        {{-- CARDS --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @foreach($subscriptions as $sub)
                <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-sm hover:border-brand-500/40 transition-all duration-200 group">
                    <div class="p-6 flex flex-col gap-5">

                        {{-- Topo: dia + nome + valor --}}
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 min-w-0">
                                <div class="size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 flex flex-col items-center justify-center shadow-inner shrink-0">
                                    <span class="text-[8px] font-black text-zinc-400 uppercase leading-none mb-1">Dia</span>
                                    <span class="text-xl font-black text-brand-600 leading-none tracking-tighter">{{ str_pad($sub->billing_day, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-black dark:text-white uppercase text-base tracking-tight truncate">{{ $sub->name }}</h4>
                                        <span class="px-2.5 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest {{ $statusStyles[$sub->status] ?? $statusStyles['active'] }}">
                                            {{ $statusLabels[$sub->status] ?? 'Ativa' }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">{{ $sub->category?->name ?? 'Sem categoria' }}</span>
                                        <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">{{ $cycleLabels[$sub->cycle] ?? 'Mensal' }}</span>
                                        @if($sub->payment_method)
                                            <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[9px] font-black uppercase tracking-widest rounded-md border border-zinc-200 dark:border-zinc-700">{{ $paymentLabels[$sub->payment_method] ?? $sub->payment_method }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-2xl font-black dark:text-white tracking-tighter italic">{{ number_format($sub->amount, 2, ',', ' ') }} <small class="text-xs">EUR</small></p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ number_format($sub->monthly_equivalent, 2, ',', ' ') }} EUR / mes</p>
                            </div>
                        </div>

                        {{-- Datas --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Proximo</p>
                                <p class="text-sm font-black dark:text-white mt-1">{{ $sub->next_billing_date->format('d/m') }}</p>
                            </div>
                            <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Faltam</p>
                                <p class="text-sm font-black dark:text-white mt-1">{{ $sub->days_until_billing }} dias</p>
                            </div>
                            <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Inicio</p>
                                <p class="text-sm font-black dark:text-white mt-1">{{ $sub->started_at ? $sub->started_at->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-100 dark:border-zinc-800">
                                <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Renova</p>
                                <p class="text-sm font-black dark:text-white mt-1">{{ $sub->renewal_date ? $sub->renewal_date->format('d/m/Y') : '-' }}</p>
                            </div>
                        </div>

                        {{-- Rodapé do card: lembrete + acções --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">

                            {{-- Lembrete --}}
                            <div class="flex flex-wrap items-center gap-2">
                                @if($sub->notify_before_billing)
                                    <span class="inline-flex items-center gap-2 rounded-xl bg-brand-50 dark:bg-brand-500/10 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-brand-700 dark:text-brand-300">
                                        <flux:icon name="bell" class="size-3.5" />
                                        Avisar {{ $sub->notify_days_before ?: 1 }} dias antes
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-zinc-400">
                                        <flux:icon name="bell-slash" class="size-3.5" />
                                        Sem lembrete
                                    </span>
                                @endif
                                @if($sub->notes)
                                    <span class="text-xs font-semibold text-zinc-500 line-clamp-1">{{ $sub->notes }}</span>
                                @endif
                            </div>

                            {{-- Acções --}}
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="delete({{ $sub->id }})"
                                    wire:confirm="Queres mesmo apagar a assinatura '{{ $sub->name }}'?"
                                    class="p-2 rounded-lg text-zinc-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all"
                                    title="Apagar Assinatura"
                                >
                                    <flux:icon name="trash" variant="mini" class="size-4" />
                                </button>

                                <div x-data="{ open: false }" class="relative">
                                    <button
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        class="p-2 rounded-xl text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all"
                                    >
                                        <flux:icon name="ellipsis-horizontal" class="size-4" />
                                    </button>

                                    <div
                                        x-show="open"
                                        x-cloak
                                        x-transition
                                        class="absolute right-0 bottom-full mb-2 z-50 w-52 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl overflow-hidden"
                                    >
                                        <div class="p-1">
                                            <button
                                                wire:click="edit({{ $sub->id }})"
                                                @click="open = false"
                                                class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-left"
                                            >
                                                <flux:icon name="pencil-square" class="size-4" /> Editar Detalhes
                                            </button>
                                            <button
                                                wire:click="duplicate({{ $sub->id }})"
                                                @click="open = false"
                                                class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-left"
                                            >
                                                <flux:icon name="document-duplicate" class="size-4" /> Duplicar
                                            </button>

                                            <div class="my-1 border-t border-zinc-100 dark:border-zinc-800"></div>

                                            @if($sub->status === 'active')
                                                <button
                                                    wire:click="toggleStatus({{ $sub->id }})"
                                                    @click="open = false"
                                                    class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all text-left"
                                                >
                                                    <flux:icon name="pause-circle" class="size-4" /> Pausar Assinatura
                                                </button>
                                            @else
                                                <button
                                                    wire:click="toggleStatus({{ $sub->id }})"
                                                    @click="open = false"
                                                    class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-xl transition-all text-left"
                                                >
                                                    <flux:icon name="play-circle" class="size-4" /> Reativar agora
                                                </button>
                                            @endif

                                            <div class="my-1 border-t border-zinc-100 dark:border-zinc-800"></div>

                                            <button
                                                wire:click="delete({{ $sub->id }})"
                                                wire:confirm="Eliminar permanentemente a assinatura '{{ $sub->name }}'?"
                                                @click="open = false"
                                                class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-all text-left"
                                            >
                                                <flux:icon name="trash" class="size-4" /> Remover Assinatura
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- /Acções --}}

                        </div>
                        {{-- /Rodapé --}}

                    </div>
                </div>
            @endforeach

            @if($subscriptions->isEmpty())
                <div class="xl:col-span-2 py-20 text-center glass-card rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                    <flux:icon name="credit-card" class="size-12 text-zinc-300 mx-auto mb-4" />
                    <p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">Sem assinaturas para estes filtros</p>
                    <button wire:click="resetFilters" class="mt-5 text-brand-600 font-black text-[10px] uppercase tracking-widest">Limpar filtros</button>
                </div>
            @endif
        </div>
        {{-- /CARDS --}}

    </div>
    {{-- /FILTROS + LISTA --}}




















{{-- MODAL --}}
@if($showExtraModal)
<div
    x-data="{
        open: false,
        show() {
            requestAnimationFrame(() => {
                this.open = true;
                document.documentElement.classList.add('overflow-hidden');
            });
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
            setTimeout(() => $wire.closeExtraModal(), 50);
        }
    }"
    x-init="show()"
    x-on:keydown.escape.window="close()"
>

    {{-- BACKDROP — instantâneo --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.50ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/70"
    ></div>

    {{-- WRAPPER --}}
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 will-change-transform"
    >

        {{-- PAINEL ULTRA-FLUIDO --}}
        <div
            @click.stop
            x-show="open"

            x-transition:enter="transition duration-65 ease-[cubic-bezier(.18,.89,.32,1.28)] transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.997] translate-y-0.5"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"

            x-transition:leave="transition duration-50 ease-[cubic-bezier(.4,0,.2,1)] transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.985] translate-y-1"

            class="relative w-full max-w-2xl rounded-[2rem] overflow-hidden
                   bg-white/10 dark:bg-zinc-900/10 backdrop-blur-sm
                   border border-white/15 dark:border-white/10
                   shadow-[0_6px_22px_-4px_rgba(0,0,0,0.45)]
                   will-change-transform will-change-opacity"
        >

            <form wire:submit.prevent="save" class="flex max-h-[86vh] flex-col">

                {{-- HEADER --}}
                <div class="shrink-0 p-6 pb-4 flex items-center gap-4
                            bg-white/10 dark:bg-zinc-900/10 border-b border-white/10">

                    <div class="p-3 rounded-2xl bg-brand-600/30 text-brand-300 shadow">
                        <flux:icon name="credit-card" class="size-5" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-black uppercase italic tracking-tight text-white">
                            {{ $editingId ? 'Editar Assinatura' : 'Nova Assinatura' }}
                        </h2>
                        <p class="text-[10px] text-brand-300 font-black uppercase tracking-widest italic mt-1.5">
                            Configuração completa de custo recorrente
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="close()"
                        class="rounded-full p-2 hover:bg-white/10 text-zinc-300 hover:text-white transition-all active:scale-90"
                    >
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6
                            will-change-scroll transition-all duration-60 ease-out">

                    {{-- Nome --}}
                    <label class="block space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">
                            Nome da assinatura
                        </span>
                        <input wire:model="name" type="text"
                            placeholder="Ex: Netflix, Spotify, Renda..."
                            class="w-full h-14 rounded-2xl bg-white/10 dark:bg-zinc-900/20
                                   border border-white/10 px-4 text-sm font-bold text-white
                                   placeholder-white/40 outline-none transition-all
                                   focus:ring-2 focus:ring-brand-500/40 focus:bg-white/20">
                    </label>

                    {{-- Valor + Dia --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Valor</span>
                            <input wire:model="amount" type="number" step="0.01" min="0"
                                placeholder="12.99"
                                class="w-full h-14 rounded-2xl bg-white/10 dark:bg-zinc-900/20
                                       border border-white/10 px-4 text-sm font-black text-brand-400
                                       placeholder-white/40 outline-none transition-all
                                       focus:ring-2 focus:ring-brand-500/40 focus:bg-white/20">
                        </label>

                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">
                                Dia de débito
                            </span>
                            <input wire:model="billing_day" type="number" min="1" max="31"
                                placeholder="Ex: 15"
                                class="w-full h-14 rounded-2xl bg-white/10 dark:bg-zinc-900/20
                                       border border-white/10 px-4 text-sm font-black text-white
                                       placeholder-white/40 outline-none transition-all
                                       focus:ring-2 focus:ring-brand-500/40 focus:bg-white/20">
                        </label>
                    </div>

                    {{-- Categoria + Ciclo --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- CATEGORIA --}}
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Categoria</span>
                            <select wire:model="category_id"
                                class="w-full h-14 rounded-2xl bg-white/20 dark:bg-zinc-900/30
                                       border border-white/10 px-4 text-sm font-bold
                                       text-white dark:text-white
                                       outline-none transition-all duration-50 ease-out
                                       focus:ring-2 focus:ring-brand-500/40">

                                <option class="text-black bg-white" value="">Selecionar tipo de serviço...</option>

                                @foreach($categories as $cat)
                                    <option class="text-black bg-white" value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </label>

                        {{-- CICLO --}}
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Ciclo</span>
                            <select wire:model="billing_cycle"
                                class="w-full h-14 rounded-2xl bg-white/20 dark:bg-zinc-900/30
                                       border border-white/10 px-4 text-sm font-bold
                                       text-white dark:text-white
                                       outline-none transition-all duration-50 ease-out
                                       focus:ring-2 focus:ring-brand-500/40">

                                <option class="text-black bg-white" value="monthly">Mensal</option>
                                <option class="text-black bg-white" value="quarterly">Trimestral</option>
                                <option class="text-black bg-white" value="semiannual">Semestral</option>
                                <option class="text-black bg-white" value="annual">Anual</option>
                            </select>
                        </label>
                    </div>

                    {{-- Pagamento + Estado --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- PAGAMENTO --}}
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Pagamento</span>
                            <select wire:model="payment_method"
                                class="w-full h-14 rounded-2xl bg-white/20 dark:bg-zinc-900/30
                                       border border-white/10 px-4 text-sm font-bold
                                       text-white dark:text-white
                                       outline-none transition-all duration-50 ease-out
                                       focus:ring-2 focus:ring-brand-500/40">

                                <option class="text-black bg-white" value="">Selecionar...</option>
                                <option class="text-black bg-white" value="card">Cartão</option>
                                <option class="text-black bg-white" value="direct_debit">Débito direto</option>
                                <option class="text-black bg-white" value="bank_transfer">Transferência</option>
                                <option class="text-black bg-white" value="paypal">PayPal</option>
                                <option class="text-black bg-white" value="cash">Numerário</option>
                            </select>
                        </label>

                        {{-- ESTADO --}}
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Estado</span>
                            <select wire:model="status"
                                class="w-full h-14 rounded-2xl bg-white/20 dark:bg-zinc-900/30
                                       border border-white/10 px-4 text-sm font-bold
                                       text-white dark:text-white
                                       outline-none transition-all duration-50 ease-out
                                       focus:ring-2 focus:ring-brand-500/40">

                                <option class="text-black bg-white" value="active">Ativa</option>
                                <option class="text-black bg-white" value="paused">Pausada</option>
                                <option class="text-black bg-white" value="cancelled">Cancelada</option>
                            </select>
                        </label>
                    </div>

                    {{-- Datas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Início</span>
                            <input wire:model="started_at" type="date"
                                class="w-full h-14 rounded-2xl bg-white/10 dark:bg-zinc-900/20
                                       border border-white/10 px-4 text-sm font-bold text-white
                                       outline-none transition-all focus:ring-2 focus:ring-brand-500/40">
                        </label>

                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Renovação</span>
                            <input wire:model="renewal_date" type="date"
                                class="w-full h-14 rounded-2xl bg-white/10 dark:bg-zinc-900/20
                                       border border-white/10 px-4 text-sm font-bold text-white
                                       outline-none transition-all focus:ring-2 focus:ring-brand-500/40">
                        </label>
                    </div>

                    {{-- Notas --}}
                    <label class="block space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">Notas</span>
                        <textarea wire:model="notes" rows="3"
                            placeholder="Ex: plano familiar, contrato anual, promoção termina em breve..."
                            class="w-full rounded-2xl bg-white/10 dark:bg-zinc-900/20
                                   border border-white/10 px-4 py-3 text-sm font-bold text-white
                                   placeholder-white/40 outline-none transition-all
                                   focus:ring-2 focus:ring-brand-500/40"></textarea>
                    </label>

                    {{-- Lembrete --}}
                    <div class="rounded-2xl bg-white/10 dark:bg-zinc-900/20 border border-white/10 p-4 space-y-3">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-brand-300">
                                    Lembrete de cobrança
                                </p>
                                <p class="text-xs font-bold text-zinc-400 mt-1">
                                    Avisar antes da renovação ou débito.
                                </p>
                            </div>
                            <input type="checkbox" wire:model="notify_before_billing"
                                class="rounded border-zinc-300 text-brand-500 focus:ring-brand-500">
                        </div>

                        <label class="block space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-brand-300">
                                Dias antes
                            </span>
                            <input wire:model="notify_days_before" type="number" min="1" max="30"
                                placeholder="3"
                                class="w-full h-12 rounded-2xl bg-white/10 dark:bg-zinc-900/20
                                       border border-white/10 px-4 text-sm font-black text-white
                                       outline-none transition-all focus:ring-2 focus:ring-brand-500/40">
                        </label>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="shrink-0 p-6 pt-4 flex flex-col sm:flex-row gap-3
                            bg-white/10 dark:bg-zinc-900/10 backdrop-blur-md border-t border-white/10">

                    <button
                        type="button"
                        @click="close()"
                        class="w-full h-14 rounded-2xl text-zinc-300 hover:text-white
                               hover:bg-white/10 font-bold uppercase text-xs tracking-widest
                               transition-all active:scale-95">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="w-full h-14 rounded-2xl bg-brand-600 hover:bg-brand-500
                               text-white font-black uppercase tracking-widest shadow-xl
                               shadow-brand-500/20 transition-all active:scale-95
                               disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Atualizar Assinatura' : 'Guardar Assinatura' }}
                        </span>
                        <span wire:loading wire:target="save">A guardar...</span>
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>
@endif




    <footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-60">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            (c) {{ date('Y') }} {{ config('app.name') }} - Sistema de Monitorizacao
        </p>
    </footer>
</div>

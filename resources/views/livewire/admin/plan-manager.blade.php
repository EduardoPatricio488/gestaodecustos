<div class="p-6 space-y-8 max-w-7xl mx-auto text-left" x-data x-on:scroll-to-top.window="window.scrollTo({top: 0, behavior: 'smooth'})">

    <x-page-header title="Gestor de Preçários" description="Cria, edita e publica os planos da loja. O slug liga o plano aos utilizadores e à faturação.">
        <x-slot:actions>
            <flux:button wire:click="startCreate" variant="primary" icon="plus" class="rounded-2xl font-black uppercase tracking-widest h-12">
                Novo plano
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    {{-- LISTA DE PLANOS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($plans as $plan)
    @php
        $isBusiness = str_contains(strtolower($plan->slug.$plan->name), 'business') || $plan->price >= 10;
        $isEditing = $editingId === $plan->id && $showForm;
        // 🔥 Definição da cor: usa a da BD ou os padrões de fallback
        $brandColor = $plan->color ?? ($isBusiness ? '#8b5cf6' : '#10b981');
    @endphp

    {{-- O card agora usa a cor na borda e tem um efeito de brilho (glow) no fundo --}}
    <div class="p-7 bg-white dark:bg-zinc-900 border-2 rounded-[2.5rem] shadow-sm flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-xl"
         style="border-color: {{ $isEditing ? '#3b82f6' : $brandColor }}33; {{ $isEditing ? 'ring: 4px solid #3b82f61a;' : '' }}">

        {{-- Brilho de fundo com a cor do plano --}}
        <div class="absolute -right-10 -top-10 size-40 blur-[80px] rounded-full opacity-10 pointer-events-none"
             style="background-color: {{ $brandColor }};"></div>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-xl font-black dark:text-white uppercase italic tracking-tight">{{ $plan->name }}</h4>
                           @if($plan->is_active)
    <span class="text-[8px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md text-white shadow-sm"
          style="background-color: {{ $brandColor }}; border: 1px solid {{ $brandColor }};">
        Ativo
    </span>
@else
                                <span class="text-[8px] font-black uppercase tracking-widest px-2 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border border-zinc-200 dark:border-zinc-700">Oculto</span>
                            @endif
                        </div>
                        <p class="text-2xl font-black italic mt-1" style="color: {{ $brandColor }};">
    {{ number_format($plan->price, 2, ',', ' ') }}€ <small class="text-xs text-zinc-400 font-bold uppercase not-italic">/ mês</small>
</p>
                        <p class="text-[10px] font-mono text-zinc-400 mt-1">slug: {{ $plan->slug }} · {{ $plan->subscribers_count }} utilizadores</p>
                    </div>
                </div>

                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-4 leading-relaxed line-clamp-3">{{ $plan->description ?: 'Sem descrição comercial.' }}</p>

                <div class="mt-5 flex flex-wrap gap-1.5">
                    @forelse($plan->featureKeys() as $feat)
                        <span class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[8px] font-black uppercase rounded-lg border border-zinc-200 dark:border-zinc-700 tracking-widest">
                            {{ $availableFeatures[$feat] ?? str_replace('_', ' ', $feat) }}
                        </span>
                    @empty
                        <span class="text-[8px] font-black uppercase text-zinc-300 italic">Sem privilégios</span>
                    @endforelse
                </div>

                <div class="mt-6 pt-4 border-t border-dashed border-zinc-100 dark:border-zinc-800">
                    <p class="text-[8px] font-black uppercase text-zinc-400">Stripe Price ID</p>
                    <p class="text-[11px] font-mono text-zinc-600 dark:text-zinc-300 mt-1 break-all">{{ $plan->stripe_price_id ?: '—' }}</p>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-2">
                    <button wire:click="edit({{ $plan->id }})"
        class="h-11 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest transition-all hover:brightness-110 shadow-lg"
        style="background-color: {{ $brandColor }};">
    Editar
</button>
                    <button wire:click="viewDossier({{ $plan->id }})" class="h-11 rounded-2xl border border-zinc-200 dark:border-zinc-700 text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:border-brand-500 hover:text-brand-600">
                        Dossiê
                    </button>
                    <button wire:click="toggleActive({{ $plan->id }})" class="h-11 rounded-2xl border border-zinc-200 dark:border-zinc-700 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                        {{ $plan->is_active ? 'Ocultar' : 'Publicar' }}
                    </button>
                    <button wire:click="delete({{ $plan->id }})" wire:confirm="Eliminar este plano? Só é possível se não tiver utilizadores." class="h-11 rounded-2xl border border-red-200 text-[10px] font-black uppercase tracking-widest text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30">
                        Eliminar
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center bg-zinc-50 dark:bg-zinc-900/50 rounded-[3rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                <flux:icon name="ticket" class="size-16 mx-auto mb-6 text-zinc-200 dark:text-zinc-800" />
                <h3 class="text-xl font-black text-zinc-400 uppercase tracking-widest italic">Ainda não há planos</h3>
                <p class="text-sm text-zinc-500 mt-2 font-medium">Cria o primeiro plano para a loja aparecer com preços reais.</p>
                <flux:button wire:click="startCreate" variant="primary" class="mt-6 rounded-2xl font-black uppercase">Criar plano</flux:button>
            </div>
        @endforelse
    </div>

    {{-- FORMULÁRIO CRIAR / EDITAR --}}
    @if($showForm)
        <div class="fixed inset-0 z-[300] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md" wire:click="resetFields"></div>

            <div class="relative w-full max-w-3xl bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 flex flex-col max-h-[90vh] overflow-hidden">
                <div class="p-6 border-b dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black dark:text-white uppercase italic">{{ $editingId ? 'Editar plano' : 'Novo plano' }}</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mt-1">Alterações passam a valer na loja e na faturação</p>
                    </div>
                    <button wire:click="resetFields" class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full text-zinc-400"><flux:icon name="x-mark" class="size-6" /></button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-6 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model.blur="name" label="Nome do plano" placeholder="Ex: Pro, Business..." />
                        <flux:input wire:model="slug" label="Slug (chave interna)" placeholder="pro" :disabled="(bool) $editingId" />
                    </div>
                    @if($editingId)
                        <p class="text-[11px] text-zinc-400 -mt-3">O slug não muda ao editar para não desligar os utilizadores já associados.</p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model="price" type="number" step="0.01" min="0" label="Preço mensal (€)" />
                        <flux:input wire:model="stripe_price_id" label="Stripe Price ID" placeholder="price_... ou STRIPE_PRICE_TOTO" />
                        <p class="text-[11px] text-zinc-400">Cola o ID que começa por price_ ou o nome da variável no .env. Planos extra publicados aparecem em /planos, por baixo de Free, Pro e Business.</p>
                    </div>
<div class="space-y-2">
    <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-1">Cor Identificadora</flux:label>
    <div class="flex items-center gap-4 p-3 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800">
        {{-- Input nativo do browser para escolher cor --}}
        <input type="color" wire:model.live="color" class="size-10 rounded-lg border-none bg-transparent cursor-pointer shadow-sm">

        {{-- Input de texto para ver o código Hex --}}
        <flux:input wire:model="color" placeholder="#000000" class="flex-1 !bg-transparent border-none font-mono text-xs uppercase" />

        {{-- Preview visual --}}
        <div class="size-4 rounded-full animate-pulse" :style="'background-color: ' + $wire.color"></div>
    </div>
</div>
                    <flux:textarea wire:model="description" label="Descrição comercial" placeholder="O que o utilizador ganha neste plano..." rows="3" />

                    <div class="space-y-3">
                        <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Privilégios de acesso</flux:label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($availableFeatures as $key => $label)
                                <label class="flex items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl cursor-pointer hover:border-brand-500/40">
                                    <input type="checkbox" wire:model="features" value="{{ $key }}" class="size-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                                    <span class="text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <label class="flex items-center gap-3 text-sm font-medium">
                        <input type="checkbox" wire:model="is_active" class="size-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500">
                        Publicado na loja
                    </label>

                    <div class="flex gap-3 pt-2">
                        <flux:button type="submit" variant="primary" class="flex-1 h-12 rounded-2xl font-black uppercase tracking-widest">
                            {{ $editingId ? 'Guardar alterações' : 'Criar plano' }}
                        </flux:button>
                        <flux:button type="button" wire:click="resetFields" variant="ghost" class="h-12 rounded-2xl font-black uppercase">
                            Cancelar
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- 3. MODAL: DOSSIÊ DE INTELIGÊNCIA COMERCIAL (CENTRADO) --}}
    @if($viewingPlan)
        <div class="fixed inset-0 z-[300] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md" wire:click="closeDossier"></div>

<div class="relative w-full max-w-4xl bg-white dark:bg-zinc-900 rounded-[3rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 flex flex-col animate-in zoom-in-95 duration-300 overflow-hidden text-left h-auto max-h-[90vh]">
                {{-- Header Premium --}}
                <div class="p-8 border-b dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/20">
                    <div class="flex items-center gap-6">
                        <div class="size-20 rounded-[1.8rem] bg-zinc-950 flex items-center justify-center text-4xl shadow-2xl border border-white/10 shrink-0" style="border-color: {{ $viewingPlan->color }}44;">
                            {{ $viewingPlan->price >= 10 ? '💎' : '⭐' }}
                        </div>
                        <div>
                            <h3 class="text-3xl font-black dark:text-white uppercase italic tracking-tighter leading-none">{{ $viewingPlan->name }}</h3>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-[10px] font-black uppercase text-white px-2 py-0.5 rounded bg-zinc-800" style="background-color: {{ $viewingPlan->color }};">ID: {{ $viewingPlan->slug }}</span>
                                <span class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Auditado em {{ now()->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <button wire:click="closeDossier" class="p-3 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full transition-colors text-zinc-400"><flux:icon name="x-mark" class="size-6" /></button>
                </div>

<div class="p-10 space-y-10 overflow-y-auto custom-scrollbar text-left">
                    {{-- GRELHA 1: KPIs FINANCEIROS --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 bg-emerald-500/5 rounded-3xl border border-emerald-500/10">
                            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">Receita Mensal Atual</p>
                            <p class="text-3xl font-black text-emerald-600 italic tracking-tighter">{{ number_format($detailedStats['monthly_revenue'], 2) }}€</p>
                        </div>
                        <div class="p-6 bg-blue-500/5 rounded-3xl border border-blue-500/10">
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1">Projeção de LTV (12m)</p>
                            <p class="text-3xl font-black text-blue-600 italic tracking-tighter">{{ number_format($detailedStats['lifetime_value'], 0, ',', ' ') }}€</p>
                        </div>
                        <div class="p-6 bg-purple-500/5 rounded-3xl border border-purple-500/10">
                            <p class="text-[9px] font-black text-purple-600 uppercase tracking-widest mb-1">Quota de Mercado Interno</p>
                            <p class="text-3xl font-black text-purple-600 italic tracking-tighter">{{ $detailedStats['market_share'] }}%</p>
                        </div>
                    </div>

                    {{-- GRELHA 2: MÉTRICAS DE ENGAJAMENTO --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Atividade --}}
                        <div class="p-8 bg-zinc-50 dark:bg-zinc-950/50 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800">
                            <h4 class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em] mb-6">Uso do Ecossistema</h4>
                            <div class="flex items-end justify-between">
                                <div class="space-y-1">
                                    <p class="text-4xl font-black dark:text-white tracking-tighter">{{ $detailedStats['total_ops'] }}</p>
                                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Operações Processadas</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-black text-brand-600 italic">{{ $detailedStats['activity_score'] }}</p>
                                    <p class="text-[8px] font-black text-zinc-400 uppercase">Ações p/ utilizador</p>
                                </div>
                            </div>
                        </div>

                        {{-- Retenção --}}
                        <div class="p-8 bg-zinc-50 dark:bg-zinc-950/50 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800">
                            <h4 class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em] mb-6">Fidelização Média</h4>
                            <div class="flex items-end justify-between">
                                <div class="space-y-1">
                                    <p class="text-4xl font-black dark:text-white tracking-tighter">{{ round($detailedStats['avg_retention']) }}</p>
                                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Dias de Permanência</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-black text-emerald-500 italic">+{{ $detailedStats['growth_rate'] }}%</p>
                                    <p class="text-[8px] font-black text-zinc-400 uppercase">Crescimento Mensal</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- LISTA DE MEMBROS DETALHADA --}}
                    <div class="space-y-5">
                        <div class="flex items-center justify-between border-b dark:border-zinc-800 pb-4 px-2">
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 border-l-4 border-brand-500 pl-4">Utilizadores Vinculados</h4>
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ $subscribers->count() }} Membros</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($subscribers as $sub)
                                <div class="p-5 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 rounded-3xl flex items-center justify-between group hover:border-brand-500/30 transition-all shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <flux:avatar initials="{{ substr($sub->name, 0, 2) }}" class="size-10 rounded-2xl shadow-inner" />
                                        <div class="min-w-0">
                                            <p class="text-sm font-black dark:text-white uppercase truncate leading-none">{{ $sub->name }}</p>
                                            <p class="text-[9px] text-zinc-400 font-bold mt-1.5 uppercase tracking-tighter italic">Nível {{ $sub->level ?? 1 }} · {{ $sub->xp ?? 0 }} XP</p>
                                        </div>
                                    </div>
                                    <div class="size-8 rounded-xl bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:icon name="arrow-right" variant="micro" class="size-3 text-zinc-400" />
                                    </div>
                                </div>
                            @empty
    <div class="col-span-full py-12 text-center bg-zinc-50/50 dark:bg-zinc-950/30 rounded-[2rem] border-2 border-dashed border-zinc-100 dark:border-zinc-800">
        <flux:icon name="users" class="size-8 mx-auto mb-3 text-zinc-300 opacity-50" />
        <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">Aguardando primeira venda para auditoria</p>
    </div>
@endforelse
                        </div>
                    </div>
                </div>

                {{-- Footer Tecnocrático --}}
                <div class="p-8 border-t dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex gap-4">
                    <button wire:click="closeDossier" class="flex-1 h-14 bg-zinc-950 text-white rounded-2xl font-black uppercase text-[10px] tracking-[0.3em] shadow-xl hover:bg-black transition-all">
                        Fechar Relatório de Auditoria
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

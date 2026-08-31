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
            @endphp

            <div class="p-7 bg-white dark:bg-zinc-900 border {{ $isEditing ? 'border-brand-500 ring-4 ring-brand-500/10' : 'border-zinc-200 dark:border-zinc-800' }} rounded-[2.5rem] shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-xl font-black dark:text-white uppercase italic tracking-tight">{{ $plan->name }}</h4>
                            @if($plan->is_active)
                                <span class="text-[8px] font-black uppercase tracking-widest px-2 py-1 rounded-md {{ $isBusiness ? 'bg-violet-500/10 text-violet-600 border border-violet-500/20' : 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' }}">Na loja</span>
                            @else
                                <span class="text-[8px] font-black uppercase tracking-widest px-2 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border border-zinc-200 dark:border-zinc-700">Oculto</span>
                            @endif
                        </div>
                        <p class="text-2xl font-black {{ $isBusiness ? 'text-violet-600' : 'text-emerald-600' }} italic mt-1">
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
                    <button wire:click="edit({{ $plan->id }})" class="h-11 rounded-2xl bg-zinc-950 text-white text-[10px] font-black uppercase tracking-widest hover:bg-brand-600 transition-colors">
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

    {{-- DOSSIÊ --}}
    @if($viewingPlan)
        <div class="fixed inset-0 z-[300] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-md" wire:click="closeDossier"></div>

            <div class="relative w-full max-w-3xl bg-white dark:bg-zinc-900 rounded-[3rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 flex flex-col overflow-hidden text-left">
                <div class="p-8 border-b dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/20">
                    <div>
                        <h3 class="text-2xl font-black dark:text-white uppercase italic tracking-tighter">{{ $viewingPlan->name }}</h3>
                        <p class="text-[10px] font-black uppercase text-brand-600 tracking-[0.3em] mt-2">{{ $subscribers->count() }} utilizadores · {{ number_format($viewingPlan->price, 2) }}€ / mês</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $viewingPlan->id }})" class="px-4 h-10 rounded-xl bg-zinc-950 text-white text-[10px] font-black uppercase">Editar</button>
                        <button wire:click="closeDossier" class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full text-zinc-400"><flux:icon name="x-mark" class="size-6" /></button>
                    </div>
                </div>

                <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">MRR deste plano</p>
                            <p class="text-xl font-black dark:text-white italic">{{ number_format($extraStats['total_revenue'], 2) }}€</p>
                        </div>
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Projeção anual</p>
                            <p class="text-xl font-black dark:text-white italic">{{ number_format($extraStats['yearly_projection'], 0, ',', ' ') }}€</p>
                        </div>
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Novos (30d)</p>
                            <p class="text-xl font-black dark:text-white italic">+{{ $extraStats['new_users_30d'] }}</p>
                        </div>
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                            <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Atividade (mês)</p>
                            <p class="text-xl font-black dark:text-white italic">{{ $extraStats['activity_volume'] }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b dark:border-zinc-800 pb-3">
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500">Utilizadores neste plano</h4>
                            <span class="text-[10px] font-black text-zinc-400 uppercase">{{ $subscribers->count() }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @forelse($subscribers as $sub)
                                <div class="p-4 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 rounded-2xl flex items-center gap-4">
                                    <div class="size-11 rounded-xl bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center font-black text-brand-600">
                                        {{ substr($sub->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black dark:text-white uppercase truncate">{{ $sub->name }}</p>
                                        <p class="text-[9px] text-zinc-400 font-bold truncate">{{ $sub->email }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-10 text-center text-xs text-zinc-400 italic">Ainda não há utilizadores neste plano.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

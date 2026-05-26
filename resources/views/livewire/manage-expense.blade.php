<div class="mx-auto max-w-2xl space-y-8">
    {{-- HEADER DINÂMICO --}}
    <x-page-header
        :title="$isEditing ? 'Editar Registo' : 'Novo Gasto'"
        :description="$isEditing ? 'Altera os detalhes do gasto selecionado.' : 'Adiciona um novo gasto ao teu espaço atual.'"
    >
        <x-slot:actions>
            <flux:button href="{{ route('expenses') }}" variant="ghost" icon="arrow-left" wire:navigate>
                Voltar
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <flux:separator />

    {{-- FORMULÁRIO PRINCIPAL --}}
    <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
        <form wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- VALOR (Usa o símbolo da moeda das definições) --}}
                <flux:input
                    wire:model="amount"
                    label="Valor em {{ $currentCurrencySymbol }}"
                    type="number"
                    step="0.01"
                    placeholder="0,00"
                    class="font-black text-xl text-brand-600"
                    required
                />

                {{-- DATA --}}
                <flux:input
                    wire:model="spent_at"
                    label="Data do Movimento"
                    type="date"
                    required
                />
            </div>

            <flux:select wire:model.live="category_id" label="Categoria" placeholder="Escolhe uma categoria...">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </flux:select>

            {{-- SECÇÃO DINÂMICA (METADADOS) --}}
            @if($categorySlug)
                <div class="p-6 bg-zinc-50 dark:bg-zinc-950/50 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-inner">
                    <p class="text-[10px] font-black uppercase text-brand-600 tracking-widest">Detalhes de {{ ucfirst($categorySlug) }}</p>

                    @if($categorySlug == 'carro')
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="meta.km" label="KM Atuais" type="number" />
                            <flux:input wire:model="meta.local" label="Posto / Local" />
                        </div>
                    @elseif($categorySlug == 'casa')
                        <flux:input wire:model="meta.fornecedor" label="Entidade (Ex: EDP, MEO)" />
                    @elseif($categorySlug == 'alimentacao')
                        <flux:input wire:model="meta.pessoas" type="number" label="Nº de Pessoas" />
                    @endif
                </div>
            @endif

            {{-- SECÇÃO DE UPLOAD DE FATURA --}}
            <div class="space-y-3">
                <flux:heading size="sm" class="font-bold">Fatura ou Recibo (+20 XP)</flux:heading>
                <div class="flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-8 hover:border-brand-500 transition-colors bg-zinc-50/50 dark:bg-zinc-950/20 relative">
                    <input type="file" wire:model="receipt" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">

                    <div class="text-center space-y-2">
                        <flux:icon name="camera" class="w-10 h-10 text-zinc-400 mx-auto" />
                        <p class="text-xs text-zinc-500 font-medium">
                            @if($receipt)
                                <span class="text-emerald-500 font-bold">Foto selecionada: {{ $receipt->getClientOriginalName() }}</span>
                            @else
                                Clique para tirar foto ou carregar talão
                            @endif
                        </p>
                    </div>
                </div>

                @if ($receipt)
                    <div class="mt-4 flex justify-center">
                        <img src="{{ $receipt->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-2xl border-4 border-white dark:border-zinc-800 shadow-xl">
                    </div>
                @endif
            </div>

            <flux:textarea
                wire:model="description"
                label="Notas Adicionais"
                placeholder="Detalhes sobre este gasto..."
                rows="3"
            />

            {{-- ACÇÕES --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <flux:button type="submit" variant="primary" class="flex-1 !h-14 text-md font-black uppercase tracking-widest shadow-xl shadow-brand-500/20">
                    {{ $isEditing ? 'Atualizar' : 'Guardar Gasto' }}
                </flux:button>

                <flux:button href="{{ route('expenses') }}" variant="ghost" class="!h-14 font-bold" wire:navigate>
                    Cancelar
                </flux:button>
            </div>
        </form>
    </div>
</div>

<div class="space-y-6">

    <x-page-header
        title="Categorias"
        description="Organiza os teus gastos por categoria."
    />

    @if (session('ok'))
        <flux:callout color="green" class="mb-6">
            {{ session('ok') }}
        </flux:callout>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- FORMULÁRIO --}}
        <div class="glass-card p-6">

            <flux:heading size="lg" class="mb-1">
                Nova categoria
            </flux:heading>

            <flux:text class="mb-4 text-zinc-500">
                Cria uma categoria para organizar os teus gastos
            </flux:text>

            <form wire:submit="save" class="space-y-4">

                <flux:input
                    wire:model="name"
                    label="Nome"
                    placeholder="Ex.: Alimentação, Transporte…"
                    required
                />

                <flux:input
                    wire:model="color"
                    type="color"
                    label="Cor"
                />

                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full"
                    icon="check"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Guardar categoria</span>
                    <span wire:loading>A guardar…</span>
                </flux:button>

            </form>

        </div>

        {{-- LISTA DE CATEGORIAS --}}
        <div class="glass-card p-6">

            <flux:heading size="lg" class="mb-4">
                As tuas categorias
            </flux:heading>

            @forelse ($categories as $c)
                <div
                    class="flex items-center justify-between border-b border-zinc-100 py-3 last:border-0 dark:border-zinc-800"
                    wire:key="category-{{ $c->id }}"
                >

                    <div class="flex items-center gap-3">

                        <span
                            class="size-3 rounded-full"
                            style="background: {{ $c->color ?? '#71717a' }}"
                        ></span>

                        <span class="font-medium">
                            {{ $c->name }}
                        </span>

                    </div>

                    <flux:button
                        size="sm"
                        variant="ghost"
                        icon="trash"
                        wire:click="delete({{ $c->id }})"
                        wire:confirm="Eliminar esta categoria?"
                    />

                </div>
            @empty
                <div class="text-center text-zinc-500">
                    Sem categorias ainda.
                </div>
            @endforelse

        </div>

    </div>

</div>

<section>
    <form wire:submit="updateSettings" class="space-y-6">
        <header>
            <flux:heading size="lg" class="font-bold">Idioma e Moeda</flux:heading>
            <flux:subheading>Personaliza a língua da interface e a moeda de visualização do teu espaço.</flux:subheading>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- SELETOR DE IDIOMA --}}
            <flux:field>
                <flux:label>Idioma do Site</flux:label>
                <flux:select wire:model="locale" placeholder="Selecione o idioma...">
                    @foreach($languages as $code => $name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:description>Altera os textos e menus do site.</flux:description>
            </flux:field>

            {{-- SELETOR DE MOEDA --}}
            <flux:field>
                <flux:label>Moeda Principal</flux:label>
                <flux:select wire:model="currency" placeholder="Selecione a moeda...">
                    @foreach($currencies as $code => $name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:description>Altera o símbolo dos valores e gráficos.</flux:description>
            </flux:field>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
            <flux:button type="submit" variant="primary" class="px-8 font-bold uppercase tracking-widest">
                Guardar Preferências
            </flux:button>

            <x-action-message on="profile-updated" class="text-emerald-500 font-medium">
                Atualizado com sucesso!
            </x-action-message>
        </div>
    </form>
</section>

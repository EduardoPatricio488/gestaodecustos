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

        {{-- SELETOR DE TEMA (Claro / Escuro / Automático) --}}
        <div
            x-data="{
                theme: localStorage.getItem('flux.appearance') || localStorage.theme || 'system',
                setTheme(value) {
                    this.theme = value;
                    document.documentElement.classList.add('theme-switching');
                    window.Flux.applyAppearance(value);
                    localStorage.removeItem('theme');
                    setTimeout(() => document.documentElement.classList.remove('theme-switching'), 200);
                }
            }"
        >
            <flux:label>Tema</flux:label>
            <div class="grid grid-cols-3 gap-3 mt-2">
                <button
                    type="button"
                    @click="setTheme('light')"
                    :class="theme === 'light' ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg' : 'border-transparent opacity-60'"
                    class="flex flex-col items-center gap-2 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border-2 hover:opacity-100 transition-all"
                >
                    <flux:icon.sun variant="outline" class="size-5 text-amber-500" />
                    <span class="text-xs font-bold dark:text-white">Claro</span>
                </button>
                <button
                    type="button"
                    @click="setTheme('dark')"
                    :class="theme === 'dark' ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg' : 'border-transparent opacity-60'"
                    class="flex flex-col items-center gap-2 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border-2 hover:opacity-100 transition-all"
                >
                    <flux:icon.moon variant="outline" class="size-5 text-indigo-500" />
                    <span class="text-xs font-bold dark:text-white">Escuro</span>
                </button>
                <button
                    type="button"
                    @click="setTheme('system')"
                    :class="theme === 'system' ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg' : 'border-transparent opacity-60'"
                    class="flex flex-col items-center gap-2 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border-2 hover:opacity-100 transition-all"
                >
                    <flux:icon.computer-desktop variant="outline" class="size-5 text-zinc-500" />
                    <span class="text-xs font-bold dark:text-white">Automático</span>
                </button>
            </div>
            <flux:description class="mt-2">"Automático" segue o tema definido no teu dispositivo.</flux:description>
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

<section>
    <form wire:submit="updateSettings" class="space-y-6">
        <header>
            <flux:heading size="lg" class="font-bold">Idioma e Moeda</flux:heading>
            <flux:subheading>Personaliza a língua da interface e a moeda de visualização do teu espaço.</flux:subheading>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label>Idioma do Site</flux:label>
                <flux:select wire:model="locale" placeholder="Selecione o idioma...">
                    @foreach($languages as $code => $name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:description>Altera os textos e menus do site.</flux:description>
            </flux:field>

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

        {{-- SELETOR DE TEMA --}}
        <div x-data="{
            theme: window.FinanceProTheme?.getTheme() || localStorage.getItem('flux.appearance') || 'system',
            scheduleOpen: false,
            setTheme(value) {
                this.theme = value;
                if (window.FinanceProTheme) window.FinanceProTheme.setTheme(value);
            }
        }"
        x-on:finance-pro-theme-changed.window="theme = $event.detail.value"
        >
            <flux:label>Tema</flux:label>
            <div class="grid grid-cols-3 gap-3 mt-2">
                <button type="button" @click="setTheme('light')"
                    :class="theme === 'light' ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg' : 'border-transparent opacity-60'"
                    class="flex flex-col items-center gap-2 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border-2 hover:opacity-100 transition-all">
                    <flux:icon.sun variant="outline" class="size-5 text-amber-500" />
                    <span class="text-xs font-bold dark:text-white">Claro</span>
                </button>
                <button type="button" @click="setTheme('dark')"
                    :class="theme === 'dark' ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg' : 'border-transparent opacity-60'"
                    class="flex flex-col items-center gap-2 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border-2 hover:opacity-100 transition-all">
                    <flux:icon.moon variant="outline" class="size-5 text-indigo-500" />
                    <span class="text-xs font-bold dark:text-white">Escuro</span>
                </button>
                <button type="button" @click="setTheme('system')"
                    :class="theme === 'system' ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg' : 'border-transparent opacity-60'"
                    class="flex flex-col items-center gap-2 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-800 border-2 hover:opacity-100 transition-all">
                    <flux:icon.computer-desktop variant="outline" class="size-5 text-zinc-500" />
                    <span class="text-xs font-bold dark:text-white">Automático</span>
                </button>
            </div>

            <div x-show="theme === 'system'" x-transition x-cloak class="mt-4">
                <button type="button" @click="scheduleOpen = true"
                    class="w-full rounded-2xl border border-brand-500/20 bg-brand-500/5 px-5 py-4 text-left transition hover:border-brand-500/40 hover:bg-brand-500/10">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-400">
                                <flux:icon name="clock" variant="outline" class="size-5" />
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-white">Horário automático</p>
                                <p class="mt-1 text-[10px] font-medium text-zinc-500">
                                    Branco às <span class="font-black text-amber-600 dark:text-amber-400" x-text="window.FinanceProTheme?.getSchedule()?.light || '07:00'"></span>
                                    · Preto às <span class="font-black text-indigo-600 dark:text-indigo-400" x-text="window.FinanceProTheme?.getSchedule()?.dark || '19:00'"></span>
                                </p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-600">Alterar</span>
                    </div>
                </button>

                <div x-show="scheduleOpen" x-transition class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4" @keydown.escape.window="scheduleOpen = false">
                    <div @click.outside="scheduleOpen = false" class="w-full max-w-md rounded-3xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-black uppercase tracking-tight text-zinc-900 dark:text-white">Horário do tema automático</h3>
                                <p class="mt-1 text-xs text-zinc-500">Define quando o Finance Pro AI muda automaticamente entre tema claro e escuro.</p>
                            </div>
                            <button type="button" @click="scheduleOpen = false" class="rounded-xl p-2 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800">×</button>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4">
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">Tema branco</span>
                                <input id="finance-pro-light-time" type="time" value="07:00" class="mt-2 w-full rounded-xl border-zinc-200 bg-white font-black dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                            </label>
                            <label class="rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4">
                                <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Tema preto</span>
                                <input id="finance-pro-dark-time" type="time" value="19:00" class="mt-2 w-full rounded-xl border-zinc-200 bg-white font-black dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                            </label>
                        </div>

                        <div class="mt-5 rounded-2xl bg-zinc-50 p-4 text-[10px] leading-relaxed text-zinc-500 dark:bg-zinc-950/60">
                            O horário é aplicado automaticamente todos os dias, de acordo com a hora local do dispositivo.
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="scheduleOpen = false" class="rounded-xl px-4 py-2 text-xs font-black uppercase tracking-widest text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800">Cancelar</button>
                            <button type="button" @click="window.FinanceProTheme?.setSchedule(document.getElementById('finance-pro-light-time').value, document.getElementById('finance-pro-dark-time').value); scheduleOpen = false" class="rounded-xl bg-brand-600 px-5 py-2 text-xs font-black uppercase tracking-widest text-white hover:bg-brand-700">Guardar horário</button>
                        </div>
                    </div>
                </div>
            </div>

            <flux:description class="mt-2">"Automático" usa o horário definido acima para alternar entre tema claro e escuro.</flux:description>
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

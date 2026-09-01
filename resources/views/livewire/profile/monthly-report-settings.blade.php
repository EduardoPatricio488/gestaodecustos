<section class="space-y-6 text-left">
    <header>
        <flux:heading size="lg" class="font-black">Relatórios por Email</flux:heading>
        <flux:subheading>Escolhe os resumos financeiros que queres receber no teu email.</flux:subheading>
    </header>

    <form wire:submit="saveSettings" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Ativar envio automático</label>
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input wire:model="enabled" type="checkbox" class="rounded text-brand-600" />
                    <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Enviar relatório mensal em PDF</span>
                </label>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Dia do envio</label>
                <div class="flex items-center gap-3">
                    <input wire:model="day" type="number" min="1" max="28"
                        class="w-28 h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-brand-500/20" />
                    <span class="text-xs text-zinc-500 font-medium">(1 a 28)</span>
                </div>
                @error('day')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="border-t border-zinc-200 pt-6 dark:border-zinc-800">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Relatório diário</label>
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input wire:model="dailyEnabled" type="checkbox" class="rounded text-brand-600" />
                    <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Enviar todos os dias às 00:00 com o resumo do dia anterior</span>
                </label>
            </div>

            <fieldset class="mt-5 space-y-3" @disabled(! $dailyEnabled)>
                <legend class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Incluir no relatório diário</legend>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="inline-flex items-center gap-3 cursor-pointer"><input wire:model="dailySections" value="earned" type="checkbox" class="rounded text-brand-600" /> <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Receitas</span></label>
                    <label class="inline-flex items-center gap-3 cursor-pointer"><input wire:model="dailySections" value="spent" type="checkbox" class="rounded text-brand-600" /> <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Despesas</span></label>
                    <label class="inline-flex items-center gap-3 cursor-pointer"><input wire:model="dailySections" value="balance" type="checkbox" class="rounded text-brand-600" /> <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Saldo do dia</span></label>
                    <label class="inline-flex items-center gap-3 cursor-pointer"><input wire:model="dailySections" value="categories" type="checkbox" class="rounded text-brand-600" /> <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Despesas por categoria</span></label>
                </div>
                @error('dailySections')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </fieldset>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <flux:button type="submit" variant="primary" class="font-black uppercase text-[10px] tracking-widest px-8 h-12">
                Guardar Preferências
            </flux:button>

            <flux:button type="button" wire:click="sendTest" variant="ghost" class="font-black uppercase text-[10px] tracking-widest px-6 h-12">
                Enviar relatorio mensal
            </flux:button>

            <flux:button type="button" wire:click="sendDailyTest" variant="ghost" class="font-black uppercase text-[10px] tracking-widest px-6 h-12">
                Enviar relatorio diario
            </flux:button>

            <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider">Destino: {{ auth()->user()->email }}</span>
        </div>
    </form>
</section>

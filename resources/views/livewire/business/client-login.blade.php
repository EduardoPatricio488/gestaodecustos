<div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl backdrop-blur-md space-y-8" x-data="{ open: false }">
    <div class="flex justify-center">
        <div class="size-16 bg-emerald-600 rounded-2xl shadow-xl shadow-emerald-500/20 flex items-center justify-center">
            <flux:icon name="user-group" variant="solid" class="size-8 text-white" />
        </div>
    </div>

    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter leading-none">Portal do Cliente</h1>
        <p class="text-[11px] text-zinc-500 font-medium italic">Acede à área privada da tua empresa.</p>
    </div>

    @if (session()->has('error'))
        <div class="p-3 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-xl border border-red-500/20 text-center uppercase tracking-widest italic">
            {{ session('error') }}
        </div>
    @endif

    <button type="button" @click="open = true" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black uppercase tracking-widest text-[11px] shadow-lg shadow-emerald-500/20 transition-all active:scale-[.98] flex items-center justify-center gap-2">
        <flux:icon name="key" class="size-5" />
        Introduzir dados de acesso
    </button>

    <div x-show="open" x-cloak x-transition class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/70 backdrop-blur-sm" @keydown.escape.window="open = false">
        <div @click.stop class="w-full max-w-xl max-h-[90vh] overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl p-6 sm:p-8">
            <div class="flex items-start justify-between gap-4 mb-7">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-emerald-600 dark:text-emerald-400">Acesso do cliente</p>
                    <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white mt-1">Dados de acesso</h2>
                    <p class="text-xs text-zinc-500 mt-1">Introduz o NIF da empresa e a chave de parceiro.</p>
                </div>
                <button type="button" @click="open = false" class="size-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            <form wire:submit.prevent="login" class="space-y-5">
                <div class="space-y-2" x-data="{ nif: @entangle('tax_number'), formatNIF(value) { return (value || '').replace(/\D/g, '').replace(/(\d{3})(?=\d)/g, '$1 ').substring(0, 11); } }" x-init="nif = formatNIF(nif)">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">NIF da Empresa</label>
                    <input type="text" x-model="nif" x-on:input="nif = formatNIF($event.target.value)" inputmode="numeric" maxlength="11" placeholder="000 000 000" class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-bold text-sm tracking-[0.1em] focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none dark:text-white" />
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Chave de Parceiro</label>
                    <input wire:model="token" type="text" inputmode="numeric" maxlength="6" placeholder="000000" autocomplete="one-time-code" class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-black text-lg tracking-[0.4em] focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none dark:text-white" />
                </div>

                <flux:button type="submit" variant="primary" class="w-full h-14 !bg-emerald-600 hover:!bg-emerald-500 rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-lg shadow-emerald-500/20 text-white border-none">
                    Entrar no Portal
                </flux:button>
            </form>
        </div>
    </div>

    <div class="text-center pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
        <a href="/" class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] hover:text-emerald-600 transition-colors">← Voltar ao site principal</a>
    </div>
</div>

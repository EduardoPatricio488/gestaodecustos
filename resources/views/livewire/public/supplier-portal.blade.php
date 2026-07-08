{{-- ESTA É A PARTE BRANCA ARREDONDADA --}}
<div class="w-full max-w-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-14 rounded-[3.5rem] shadow-2xl backdrop-blur-md space-y-8 relative mx-auto">

    {{-- ÍCONE DO PORTAL --}}
    <div class="flex justify-center">
        <div class="size-20 bg-emerald-600 rounded-2xl shadow-xl shadow-emerald-500/20 flex items-center justify-center">
            <flux:icon name="{{ $isLoggedIn ? 'document-text' : 'building-storefront' }}" variant="solid" class="size-10 text-white" />
        </div>
    </div>

    {{-- TÍTULOS --}}
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter leading-none">
            {{ $isLoggedIn ? 'Submeter Faturação' : 'Portal do Fornecedor' }}
        </h1>
        <p class="text-[11px] text-zinc-500 font-medium italic text-center w-full">
            {{ $isLoggedIn ? 'Parceiro: ' . $supplier->name : 'Introduz o teu código de acesso exclusivo.' }}
        </p>
    </div>

    @if (!$isLoggedIn)
        {{-- 1. FORMULÁRIO DE LOGIN --}}
        <form wire:submit.prevent="login" class="space-y-6 text-left">
            @if (session()->has('error'))
                <div class="p-3 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-xl border border-red-500/20 text-center uppercase tracking-widest italic">
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-5">
                {{-- NIF FORMATADO --}}
                <div class="space-y-2" x-data="{
                    nif: @entangle('tax_number'),
                    formatNIF(value) {
                        if (!value) return '';
                        return value.replace(/\D/g, '').replace(/(\d{3})(?=\d)/g, '$1 ').substring(0, 11);
                    }
                }" x-init="nif = formatNIF(nif)">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">NIF da Empresa</label>
                    <input type="text" x-model="nif" x-on:input="nif = formatNIF($event.target.value)" placeholder="000 000 000"
                           class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-bold text-sm tracking-[0.1em] focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all dark:text-white" />
                </div>

                {{-- CHAVE (PIN) --}}
                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Chave de Parceiro (PIN)</label>
                    <input wire:model="token" type="text" maxlength="6" placeholder="000000"
                           class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-black text-lg tracking-[0.4em] focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all dark:text-white" />
                </div>

                <flux:button type="submit" variant="primary" class="w-full h-14 !bg-emerald-600 hover:!bg-emerald-500 rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-lg shadow-emerald-500/20 transition-all active:scale-95 border-none text-white mt-4">
                    Entrar no Portal
                </flux:button>
            </div>
        </form>
    @else
        {{-- 2. FORMULÁRIO DE SUBMISSÃO --}}
        <form wire:submit.prevent="submitInvoice" class="space-y-6">
             <flux:button type="submit" variant="primary" class="w-full h-14 !bg-emerald-600 rounded-2xl font-black uppercase">Submeter Documento</flux:button>
             <button type="button" wire:click="$set('isLoggedIn', false)" class="w-full text-center text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-red-500 transition-colors">Sair da Sessão</button>
        </form>
    @endif

    {{-- LINK DE VOLTA --}}
    <div class="text-center pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
        <a href="/" class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] hover:text-emerald-600 transition-colors">
            ← Voltar ao site principal
        </a>
    </div>
</div>

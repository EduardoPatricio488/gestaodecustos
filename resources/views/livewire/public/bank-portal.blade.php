{{-- ESTA É A CAIXA BRANCA (MOLDURA) --}}
<div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl backdrop-blur-md space-y-8 text-left">

    {{-- ÍCONE CENTRAL (Bancário) --}}
    <div class="flex justify-center">
        <div class="size-16 bg-zinc-900 rounded-2xl shadow-xl flex items-center justify-center border border-white/10">
            <flux:icon name="building-library" variant="solid" class="size-10 text-white" />
        </div>
    </div>

    {{-- TÍTULOS INTERNOS --}}
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter leading-none">
            Acesso de Auditoria
        </h1>
        <p class="text-[11px] text-zinc-500 font-medium italic">
            Introduz as credenciais institucionais para verificação.
        </p>
    </div>

    {{-- FORMULÁRIO --}}
    <form wire:submit.prevent="login" class="space-y-6">
        @if (session()->has('error'))
            <div class="p-3 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-xl border border-red-500/20 text-center uppercase tracking-widest italic">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-5">
            {{-- NIF DA EMPRESA FORMATADO --}}
            <div class="space-y-2" x-data="{
                nif: @entangle('company_nif'),
                formatNIF(v) { if (!v) return ''; return v.replace(/\D/g, '').replace(/(\d{3})(?=\d)/g, '$1 ').substring(0, 11); }
            }" x-init="nif = formatNIF(nif)">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">NIF da Empresa Auditada</label>
                <input type="text" x-model="nif" x-on:input="nif = formatNIF($event.target.value)" placeholder="000 000 000"
                       class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-bold text-sm focus:ring-2 focus:ring-zinc-500 outline-none transition-all dark:text-white" />
            </div>

            {{-- CÓDIGO DE AUDITORIA (PIN) --}}
            <div class="space-y-2">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Código de Auditoria (Token)</label>
                <input wire:model="token" type="text" maxlength="6" placeholder="000000"
                       class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-black text-xl tracking-[0.6em] text-zinc-900 dark:text-white focus:ring-2 focus:ring-zinc-500 outline-none transition-all" />
            </div>

            <flux:button type="submit" variant="primary" class="w-full h-14 !bg-zinc-900 hover:!bg-zinc-800 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] border-none mt-4 shadow-lg active:scale-95 transition-all">
                Autenticar Auditoria
            </flux:button>
        </div>
    </form>

    {{-- LINK DE VOLTA --}}
    <div class="text-center pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
        <a href="/" class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] hover:text-zinc-900 transition-colors">
            ← Voltar ao site principal
        </a>
    </div>
</div>

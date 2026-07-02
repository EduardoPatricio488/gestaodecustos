<x-layouts.app>
    {{-- Contentor Principal centralizado com espaçamento extra --}}
    <div class="mx-auto max-w-4xl space-y-12 pb-24 pt-4">

        {{-- 1. HEADER SaaS PREMIUM --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
            <div class="flex items-center gap-5">
                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                    <flux:icon name="cog-6-tooth" class="w-8 h-8 text-brand-600" />
                </div>
                <div class="text-left">
                    <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Definições</h1>
                    <p class="text-sm text-zinc-500 font-medium italic mt-2">Gere a tua identidade, segurança e preferências</p>
                </div>
            </div>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="rounded-2xl font-bold">
                Voltar ao Dashboard
            </flux:button>
        </div>

        {{-- 2. SECÇÃO: IDENTIDADE VISUAL (Apenas para utilizadores normais) --}}
        @if(!auth()->user()->isAdmin())
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-indigo-500/10 rounded-lg text-indigo-600">
                    <flux:icon name="face-smile" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Personalização de Perfil</h2>
            </div>

            <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-xl">
                <livewire:profile.update-visual-identity-form />
            </div>
        </div>
        @endif

        {{-- 3. SECÇÃO: PREFERÊNCIAS VISUAIS --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-brand-500/10 rounded-lg text-brand-600">
                    <flux:icon name="swatch" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400 text-left">Aparência e Sistema</h2>
            </div>

            <div class="glass-card relative overflow-hidden p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
                <div class="absolute -right-10 -top-10 size-32 bg-brand-500/5 blur-3xl rounded-full group-hover:bg-brand-500/10 transition-all duration-1000"></div>
                <div class="relative z-10">
                    <livewire:profile.update-appearance-form />
                </div>
            </div>
        </div>

        {{-- 4. SECÇÃO: DADOS DE IDENTIDADE --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="user" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500 text-left">Informações de Conta</h2>
            </div>

            <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
                <div class="max-w-2xl text-left">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>
        </div>

  {{-- 5. SECÇÃO: SEGURANÇA --}}
<div class="space-y-6">
    <div class="flex items-center gap-3 px-4 text-left">
        <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
            <flux:icon name="shield-check" variant="outline" class="size-4" />
        </div>
        <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500">Segurança</h2>
    </div>

    <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md relative min-h-[350px] flex flex-col justify-center">

        {{-- CASO 1: PRIVACIDADE DESATIVADA (Mostra o Formulário) --}}
        <div x-show="!privacyMode" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="w-full">
            <div class="max-w-2xl text-left">
                <livewire:profile.update-password-form />
            </div>
        </div>

        {{-- CASO 2: PRIVACIDADE ATIVA (Mostra o Aviso) --}}
        <div x-show="privacyMode" x-cloak x-transition:enter="transition ease-out duration-300" class="flex flex-col items-center justify-center text-center space-y-4 w-full">
            <div class="size-20 rounded-full bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center shadow-inner">
                <flux:icon name="eye-slash" class="size-10 text-zinc-300 dark:text-zinc-600" />
            </div>
            <div class="space-y-2">
                <h3 class="text-lg font-black uppercase italic text-zinc-400 tracking-tighter leading-none">Privacidade Ativada</h3>
                <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">O formulário de segurança está oculto</p>
                <button @click="privacyMode = false" class="mt-4 px-4 py-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase hover:bg-emerald-500 hover:text-white transition-all">
                    Revelar agora
                </button>
            </div>
        </div>

    </div>
</div>

        {{-- 6. SECÇÃO: ZONA CRÍTICA --}}
        <div class="space-y-6 pt-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-red-500/10 rounded-lg text-red-600">
                    <flux:icon name="exclamation-triangle" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-red-600 text-left">Gestão Crítica</h2>
            </div>

            <div class="relative overflow-hidden p-8 bg-red-50/40 dark:bg-red-950/10 rounded-[2.5rem] border border-red-100 dark:border-red-900/20 group">
                <div class="absolute -right-10 -bottom-10 size-40 bg-red-500/5 blur-3xl rounded-full group-hover:bg-red-500/10 transition-all duration-1000"></div>
                <div class="relative z-10 text-left">
                    <div class="mb-6">
                        <h3 class="text-sm font-black text-red-700 dark:text-red-400 uppercase tracking-tight leading-none">Encerrar Conta</h3>
                        <p class="text-[11px] text-red-600/70 dark:text-red-500/50 mt-2 italic font-medium">Esta ação é irreversível. Todos os dados serão apagados.</p>
                    </div>
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>

        <footer class="pt-12 text-center opacity-50">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
                Finance Pro IA · Protocolo de Segurança v3.4
            </p>
        </footer>

    </div>
</x-layouts.app>

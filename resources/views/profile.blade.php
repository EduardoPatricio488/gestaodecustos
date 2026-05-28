<x-app-layout>
    {{-- Contentor Principal centralizado com espaçamento extra --}}
    <div class="mx-auto max-w-4xl space-y-12 pb-24 pt-4">

        {{-- 1. HEADER SaaS PREMIUM --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
            <div class="flex items-center gap-5">
                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                    <flux:icon name="cog-6-tooth" class="w-8 h-8 text-brand-600" />
                </div>
                <div>
                    <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Definições</h1>
                    <p class="text-sm text-zinc-500 font-medium italic">Gere a tua identidade, segurança e preferências</p>
                </div>
            </div>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate class="rounded-2xl font-bold">
                Voltar ao Dashboard
            </flux:button>
        </div>

        {{-- 2. SECÇÃO: PERSONALIZAÇÃO (APARÊNCIA E MOEDA) --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-brand-500/10 rounded-lg text-brand-600">
                    <flux:icon name="swatch" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">Preferências Visuais</h2>
            </div>

            <div class="glass-card relative overflow-hidden p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group">
                {{-- Efeito Decorativo --}}
                <div class="absolute -right-10 -top-10 size-32 bg-brand-500/5 blur-3xl rounded-full group-hover:bg-brand-500/10 transition-all duration-1000"></div>

                <div class="relative z-10">
                    <livewire:profile.update-appearance-form />
                </div>
            </div>
        </div>

        {{-- 3. SECÇÃO: INFORMAÇÕES PESSOAIS --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="user" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500">Dados de Identidade</h2>
            </div>

            <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
                <div class="max-w-2xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>
        </div>

        {{-- 4. SECÇÃO: SEGURANÇA --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                    <flux:icon name="shield-check" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-500">Segurança da Conta</h2>
            </div>

            <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:shadow-md">
                <div class="max-w-2xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
        </div>

        {{-- 5. SECÇÃO: ZONA DE PERIGO (DESIGN DE ALERTA) --}}
        <div class="space-y-6 pt-6">
            <div class="flex items-center gap-3 px-4">
                <div class="p-1.5 bg-red-500/10 rounded-lg text-red-600">
                    <flux:icon name="exclamation-triangle" variant="outline" class="size-4" />
                </div>
                <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-red-600 dark:text-red-500">Gestão Crítica</h2>
            </div>

            <div class="relative overflow-hidden p-8 bg-red-50/40 dark:bg-red-950/10 rounded-[2.5rem] border border-red-100 dark:border-red-900/20 group">
                {{-- Efeito decorativo de aviso --}}
                <div class="absolute -right-10 -bottom-10 size-40 bg-red-500/5 blur-3xl rounded-full group-hover:bg-red-500/10 transition-all duration-1000"></div>

                <div class="relative z-10">
                    <div class="mb-6">
                        <h3 class="text-sm font-black text-red-700 dark:text-red-400 uppercase tracking-tight">Encerrar Conta</h3>
                        <p class="text-xs text-red-600/70 dark:text-red-500/50 mt-1 italic">Ao apagar a conta, todos os teus registos financeiros, recibos e configurações serão eliminados permanentemente.</p>
                    </div>

                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>

        {{-- RODAPÉ DE CONFIGURAÇÕES --}}
        <footer class="pt-12 text-center">
            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
                Finance Pro IA · Protocolo de Segurança Ativo
            </p>
        </footer>


    </div>
</x-app-layout>

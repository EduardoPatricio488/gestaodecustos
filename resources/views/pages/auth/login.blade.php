<x-guest-layout>
    {{-- EFEITO DE FUNDO DISCRETO --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[5%] -left-[5%] size-64 bg-brand-500/5 blur-[80px] rounded-full"></div>
    </div>

    {{-- LIMITADOR DE LARGURA (MAX-W-SM) --}}
    <div class="mx-auto max-w-[360px] relative z-10 space-y-6">
{{-- BARRA DE TOPO COM BOTÃO VOLTAR --}}
        <div class="flex justify-start">
            <flux:button href="/" variant="ghost" icon="arrow-left" size="sm" wire:navigate class="rounded-xl text-zinc-400 hover:text-zinc-800 dark:hover:text-white" />
        </div>

        {{-- HEADER COMPACTO --}}
        <div class="text-center space-y-4">
            <div class="inline-flex relative group">
                <div class="absolute inset-0 bg-brand-500/15 blur-xl rounded-full transition-all"></div>
                <div class="relative p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl">
                    <flux:icon name="shield-check" class="size-7 text-brand-600" />
                </div>
            </div>

            <div class="space-y-1">
                <h1 class="text-2xl font-black dark:text-white uppercase tracking-tighter italic">Acesso</h1>
                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest opacity-80">Terminal de Gestão</p>
            </div>
        </div>

        @if (session('status'))
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <p class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest text-center">
                    {{ session('status') }}
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- IDENTIFICAÇÃO (EMAIL) -->
            <div class="space-y-1.5">
                <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] px-1">Credencial</flux:label>
                <flux:input
                    name="email"
                    :value="old('email')"
                    type="email"
                    icon="envelope"
                    required
                    autofocus
                    placeholder="teu@email.com"
                    class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-xl h-11 shadow-inner focus:ring-1 focus:ring-brand-500/20 text-sm"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

          <!-- SEGURANÇA (PASSWORD) -->
<div class="space-y-1.5" x-data="{ showPassword: false }">
    <div class="flex justify-between items-center px-1">
        <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em]">Password</flux:label>
        @if (Route::has('password.request'))
            <flux:link :href="route('password.request')" class="text-[9px] font-black uppercase tracking-widest text-zinc-400 hover:text-brand-500" variant="subtle">
                {{ __('Esqueceste-te?') }}
            </flux:link>
        @endif
    </div>

    <div class="relative group">
        {{-- Input principal --}}
        <flux:input
            name="password"
            ::type="showPassword ? 'text' : 'password'"
            icon="lock-closed"
            required
            placeholder="••••••••"
            {{-- pr-10 garante que o texto não fica por baixo do olho --}}
            class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-xl h-11 shadow-inner focus:ring-1 focus:ring-brand-500/20 text-sm pr-10"
        />

        {{-- Botão do Olho (Posicionamento Absoluto para não falhar) --}}
        <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 z-20">
            <button type="button" @click="showPassword = !showPassword" class="text-zinc-400 hover:text-zinc-600 focus:outline-none transition-colors">
                <flux:icon x-show="!showPassword" name="eye" class="size-4" />
                <flux:icon x-show="showPassword" name="eye-slash" class="size-4" x-cloak />
            </button>
        </div>
    </div>

    <x-input-error :messages="$errors->get('password')" class="mt-1" />
</div>



            {{-- BOTÃO DE LOGIN COMPACTO --}}
            <div class="pt-1">
                <flux:button variant="primary" type="submit" class="w-full h-11 rounded-xl font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 hover:bg-brand-500 transition-all hover:scale-[1.01] active:scale-95 text-xs">
                    {{ __('Entrar') }}
                </flux:button>
            </div>
        </form>



        {{-- RODAPÉ MINIMALISTA --}}
        <div class="text-center space-y-6 animate-fade-in-delayed">
            <p class="text-xs text-zinc-500 font-medium">
                Sem conta?
                <flux:link :href="route('register')" wire:navigate class="font-black text-brand-600 dark:text-brand-400 hover:text-brand-500 transition-colors">Cria uma agora</flux:link>
            </p>

            <footer class="pt-4">
                <p class="text-[8px] font-black text-zinc-300 dark:text-zinc-800 uppercase tracking-[0.5em] cursor-default">
                    &copy; {{ date('Y') }} · {{ config('app.name') }}
                </p>
            </footer>
        </div>
    </div>
</x-guest-layout>

{{-- 4. ESTILOS DE ANIMAÇÃO COMPACTOS --}}
<style>
    @keyframes compactFadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .relative.z-10 {
        animation: compactFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .animate-fade-in-delayed {
        opacity: 0;
        animation: compactFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
    }

    /* Melhora o aspeto do cursor no input */
    input {
        caret-color: #3b82f6;
    }
</style>

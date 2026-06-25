<x-guest-layout>
    {{-- EFEITO DE FUNDO DISCRETO --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[5%] -left-[5%] size-64 bg-emerald-500/5 blur-[80px] rounded-full"></div>
    </div>

    {{-- LIMITADOR DE LARGURA (MAX-W-SM) --}}
    <div class="mx-auto max-w-[360px] relative z-10 space-y-6">

        {{-- HEADER COMPACTO --}}
        <div class="text-center space-y-4">
            <div class="inline-flex relative group">
                <div class="absolute inset-0 bg-emerald-500/15 blur-xl rounded-full transition-all"></div>
                <div class="relative p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl">
                    <flux:icon name="user-plus" class="size-7 text-emerald-600" />
                </div>
            </div>

            <div class="space-y-1">
                <h1 class="text-2xl font-black dark:text-white uppercase tracking-tighter italic">Novo Registo</h1>
                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest opacity-80 italic">Cria a tua identidade no sistema</p>
            </div>
        </div>

        {{-- INÍCIO DO FORMULÁRIO COM LÓGICA DE VISUALIZAÇÃO DE PASSWORD --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ showPass: false, showConfirm: false }">
            @csrf

            <!-- NOME -->
            <div class="space-y-1.5">
                <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] px-1">Nome Identificativo</flux:label>
                <flux:input
                    name="name"
                    :value="old('name')"
                    icon="user"
                    required
                    autofocus
                    placeholder="O teu nome"
                    class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-xl h-11 shadow-inner focus:ring-1 focus:ring-emerald-500/20 text-sm"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- EMAIL -->
            <div class="space-y-1.5">
                <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] px-1">Email</flux:label>
                <flux:input
                    name="email"
                    :value="old('email')"
                    type="email"
                    icon="envelope"
                    required
                    placeholder="teu@email.com"
                    class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-xl h-11 shadow-inner focus:ring-1 focus:ring-emerald-500/20 text-sm"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- PASSWORD -->
            <div class="space-y-1.5">
                <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] px-1">Palavra-passe</flux:label>
                <flux:input
                    name="password"
                    x-bind:type="showPass ? 'text' : 'password'"
                    icon="lock-closed"
                    required
                    placeholder="Mínimo 8 caracteres"
                    class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-xl h-11 shadow-inner focus:ring-1 focus:ring-emerald-500/20 text-sm"
                >
                    <x-slot:append>
                        <button type="button" @click="showPass = !showPass" class="flex items-center px-2 text-zinc-400 hover:text-zinc-600 outline-none">
                            <flux:icon x-show="!showPass" name="eye" class="size-4" />
                            <flux:icon x-show="showPass" name="eye-slash" class="size-4" x-cloak />
                        </button>
                    </x-slot:append>
                </flux:input>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- CONFIRMAÇÃO DE PASSWORD -->
            <div class="space-y-1.5">
                <flux:label class="text-[9px] font-black uppercase text-zinc-400 tracking-[0.2em] px-1">Confirmar</flux:label>
                <flux:input
                    name="password_confirmation"
                    x-bind:type="showConfirm ? 'text' : 'password'"
                    icon="shield-check"
                    required
                    placeholder="Repete a senha"
                    class="font-bold !bg-zinc-50 dark:!bg-zinc-950 !border-none rounded-xl h-11 shadow-inner focus:ring-1 focus:ring-emerald-500/20 text-sm"
                >
                    <x-slot:append>
                        <button type="button" @click="showConfirm = !showConfirm" class="flex items-center px-2 text-zinc-400 hover:text-zinc-600 outline-none">
                            <flux:icon x-show="!showConfirm" name="eye" class="size-4" />
                            <flux:icon x-show="showConfirm" name="eye-slash" class="size-4" x-cloak />
                        </button>
                    </x-slot:append>
                </flux:input>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            {{-- BOTÃO DE REGISTO --}}
            <div class="pt-3">
                <flux:button variant="primary" type="submit" class="w-full h-11 rounded-xl font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 bg-emerald-600 border-none hover:bg-emerald-500 transition-all hover:scale-[1.01] active:scale-95 text-xs text-white">
                    {{ __('Ativar a Minha Conta') }}
                </flux:button>
            </div>
        </form>

        {{-- DIVISOR DISCRETO --}}
        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-zinc-100 dark:border-zinc-800"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white dark:bg-zinc-950 px-3 text-[8px] font-black uppercase tracking-[0.4em] text-zinc-400 opacity-50 italic">Security Protocol</span>
            </div>
        </div>

        {{-- RETORNO AO LOGIN --}}
        <div class="text-center space-y-6 animate-fade-in-delayed">
            <p class="text-xs text-zinc-500 font-medium">
                Já tens uma conta?
                <flux:link :href="route('login')" wire:navigate class="font-black text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 transition-colors">Fazer Login</flux:link>
            </p>

            <footer class="pt-4">
                <p class="text-[8px] font-black text-zinc-300 dark:text-zinc-800 uppercase tracking-[0.5em] cursor-default">
                    &copy; {{ date('Y') }} · {{ config('app.name') }}
                </p>
            </footer>
        </div>
    </div>
</x-guest-layout>

{{-- ESTILOS DE ANIMAÇÃO E INTERFACE --}}
<style>
    [x-cloak] { display: none !important; }

    @keyframes compactFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .relative.z-10 {
        animation: compactFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .animate-fade-in-delayed {
        opacity: 0;
        animation: compactFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
    }

    /* Estilo do foco nos inputs */
    input:focus {
        background-color: transparent !important;
    }
</style>

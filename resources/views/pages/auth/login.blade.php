<x-guest-layout>
    <div class="space-y-7" x-data>

        {{-- HEADER --}}
        <div class="text-center space-y-3">
            <div class="inline-flex relative">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full scale-150"></div>
                <div class="relative flex items-center justify-center size-14 bg-gradient-to-br from-brand-500 to-brand-700 rounded-2xl shadow-lg shadow-brand-500/30">
                    <flux:icon name="shield-check" class="size-7 text-white" />
                </div>
            </div>
            <div>
                <h1 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Bem-vindo de volta</h1>
                <p class="text-xs text-zinc-500 mt-0.5">Entra na tua conta para continuar</p>
            </div>
        </div>

        {{-- ALERTA DE STATUS --}}
        @if (session('status'))
            <div class="flex items-center gap-2.5 p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                <flux:icon name="check-circle" class="size-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">{{ session('status') }}</p>
            </div>
        @endif

        {{-- ERROS GLOBAIS --}}
        @if ($errors->any())
            <div class="flex items-start gap-2.5 p-3.5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-xl">
                <flux:icon name="exclamation-triangle" class="size-4 text-red-500 shrink-0 mt-0.5" />
                <div>
                    <p class="text-xs font-bold text-red-700 dark:text-red-400">Credenciais inválidas</p>
                    <p class="text-[11px] text-red-600/80 dark:text-red-500/80 mt-0.5">Verifica o email e a password e tenta novamente.</p>
                </div>
            </div>
        @endif

        {{-- FORMULÁRIO --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- EMAIL --}}
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                        <flux:icon name="envelope" class="size-4 text-zinc-400" />
                    </div>
                    <input
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="teu@email.com"
                        class="w-full h-11 pl-10 pr-4 bg-zinc-50 dark:bg-zinc-900 border {{ $errors->has('email') ? 'border-red-400 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 dark:focus:border-brand-600 transition-all"
                    />
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-[11px]" />
            </div>

            {{-- PASSWORD --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-500 transition-colors">
                            Esqueceste-te?
                        </a>
                    @endif
                </div>
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                        <flux:icon name="lock-closed" class="size-4 text-zinc-400" />
                    </div>
                    <input
                        name="password"
                        :type="show ? 'text' : 'password'"
                        required
                        placeholder="••••••••"
                        class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border {{ $errors->has('password') ? 'border-red-400 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 dark:focus:border-brand-600 transition-all"
                    />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center px-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none">
                        <flux:icon x-show="!show" name="eye" class="size-4" />
                        <flux:icon x-show="show" name="eye-slash" class="size-4" x-cloak />
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="text-[11px]" />
            </div>

            {{-- LEMBRAR SESSÃO --}}
            <div class="flex items-center gap-2.5 pt-0.5">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    class="size-4 rounded-md border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-brand-600 focus:ring-brand-500/30 focus:ring-2 cursor-pointer"
                />
                <label for="remember" class="text-xs text-zinc-500 dark:text-zinc-400 cursor-pointer select-none">
                    Manter sessão iniciada
                </label>
            </div>

            {{-- BOTÃO --}}
            <div class="pt-1">
                <button
                    type="submit"
                    class="group relative w-full h-11 flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 transition-all duration-200 hover:-translate-y-px active:translate-y-0 active:shadow-md"
                >
                    <flux:icon name="arrow-right-end-on-rectangle" class="size-4 opacity-80" />
                    <span>Entrar na conta</span>
                </button>
            </div>
        </form>

        {{-- SEPARADOR --}}
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-zinc-100 dark:border-zinc-800"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white dark:bg-zinc-900/50 px-3 text-[10px] font-semibold text-zinc-400 uppercase tracking-widest">
                    Novo por aqui?
                </span>
            </div>
        </div>

        {{-- LINK REGISTO --}}
        <a
            href="{{ route('register') }}"
            wire:navigate
            class="flex items-center justify-center gap-2 w-full h-11 bg-zinc-50 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-sm font-semibold text-zinc-700 dark:text-zinc-300 rounded-xl transition-all duration-200 hover:-translate-y-px"
        >
            <flux:icon name="user-plus" class="size-4 text-zinc-400" />
            Criar uma conta gratuita
        </a>

    </div>
</x-guest-layout>

<style>
    [x-cloak] { display: none !important; }
    input { caret-color: theme('colors.brand.500', #3b82f6); }
</style>

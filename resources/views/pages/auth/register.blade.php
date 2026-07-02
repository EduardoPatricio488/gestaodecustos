<x-guest-layout>
    <div class="space-y-7" x-data>

        {{-- HEADER --}}
        <div class="text-center space-y-3">
            <div class="inline-flex relative">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full scale-150"></div>
                <div class="relative flex items-center justify-center size-14 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl shadow-lg shadow-emerald-500/30">
                    <flux:icon name="user-plus" class="size-7 text-white" />
                </div>
            </div>
            <div>
                <h1 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Criar conta</h1>
                <p class="text-xs text-zinc-500 mt-0.5">Regista-te para começar a gerir os teus custos</p>
            </div>
        </div>

        {{-- FORMULÁRIO --}}
        <form
            method="POST"
            action="{{ route('register') }}"
            class="space-y-4"
            x-data="{
                showPass: false,
                showConfirm: false,
                password: '',
                get strength() {
                    if (!this.password) return 0;
                    let score = 0;
                    if (this.password.length >= 8) score++;
                    if (this.password.length >= 12) score++;
                    if (/[A-Z]/.test(this.password)) score++;
                    if (/[0-9]/.test(this.password)) score++;
                    if (/[^A-Za-z0-9]/.test(this.password)) score++;
                    return score;
                },
                get strengthLabel() {
                    const labels = ['', 'Fraca', 'Razoável', 'Boa', 'Forte', 'Excelente'];
                    return labels[this.strength] || '';
                },
                get strengthColor() {
                    const colors = ['', 'bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-emerald-400', 'bg-emerald-500'];
                    return colors[this.strength] || '';
                }
            }"
        >
            @csrf

            {{-- NOME --}}
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Nome</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                        <flux:icon name="user" class="size-4 text-zinc-400" />
                    </div>
                    <input
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="O teu nome completo"
                        class="w-full h-11 pl-10 pr-4 bg-zinc-50 dark:bg-zinc-900 border {{ $errors->has('name') ? 'border-red-400 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-400 dark:focus:border-emerald-600 transition-all"
                    />
                </div>
                <x-input-error :messages="$errors->get('name')" class="text-[11px]" />
            </div>

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
                        autocomplete="email"
                        placeholder="teu@email.com"
                        class="w-full h-11 pl-10 pr-4 bg-zinc-50 dark:bg-zinc-900 border {{ $errors->has('email') ? 'border-red-400 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-400 dark:focus:border-emerald-600 transition-all"
                    />
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-[11px]" />
            </div>

            {{-- PASSWORD --}}
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                        <flux:icon name="lock-closed" class="size-4 text-zinc-400" />
                    </div>
                    <input
                        name="password"
                        :type="showPass ? 'text' : 'password'"
                        x-model="password"
                        required
                        autocomplete="new-password"
                        placeholder="Mínimo 8 caracteres"
                        class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border {{ $errors->has('password') ? 'border-red-400 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-400 dark:focus:border-emerald-600 transition-all"
                    />
                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-3 flex items-center px-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none">
                        <flux:icon x-show="!showPass" name="eye" class="size-4" />
                        <flux:icon x-show="showPass" name="eye-slash" class="size-4" x-cloak />
                    </button>
                </div>

                {{-- INDICADOR DE FORÇA --}}
                <div x-show="password.length > 0" x-cloak class="space-y-1.5 pt-0.5">
                    <div class="flex gap-1">
                        <template x-for="i in 5">
                            <div
                                class="h-1 flex-1 rounded-full transition-all duration-300"
                                :class="i <= strength ? strengthColor : 'bg-zinc-200 dark:bg-zinc-800'"
                            ></div>
                        </template>
                    </div>
                    <p class="text-[11px] font-semibold transition-colors" :class="strength <= 1 ? 'text-red-500' : strength <= 2 ? 'text-orange-500' : strength <= 3 ? 'text-yellow-600' : 'text-emerald-600'">
                        Segurança: <span x-text="strengthLabel"></span>
                    </p>
                </div>

                <x-input-error :messages="$errors->get('password')" class="text-[11px]" />
            </div>

            {{-- CONFIRMAR PASSWORD --}}
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Confirmar password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                        <flux:icon name="shield-check" class="size-4 text-zinc-400" />
                    </div>
                    <input
                        name="password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="Repete a password"
                        class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border {{ $errors->has('password_confirmation') ? 'border-red-400 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-400 dark:focus:border-emerald-600 transition-all"
                    />
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-3 flex items-center px-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none">
                        <flux:icon x-show="!showConfirm" name="eye" class="size-4" />
                        <flux:icon x-show="showConfirm" name="eye-slash" class="size-4" x-cloak />
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="text-[11px]" />
            </div>

            {{-- BOTÃO --}}
            <div class="pt-1">
                <button
                    type="submit"
                    class="group relative w-full h-11 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-200 hover:-translate-y-px active:translate-y-0 active:shadow-md"
                >
                    <flux:icon name="check-circle" class="size-4 opacity-80" />
                    <span>Criar a minha conta</span>
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
                    Já tens conta?
                </span>
            </div>
        </div>

        {{-- LINK LOGIN --}}
        <a
            href="{{ route('login') }}"
            wire:navigate
            class="flex items-center justify-center gap-2 w-full h-11 bg-zinc-50 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-sm font-semibold text-zinc-700 dark:text-zinc-300 rounded-xl transition-all duration-200 hover:-translate-y-px"
        >
            <flux:icon name="arrow-right-end-on-rectangle" class="size-4 text-zinc-400" />
            Fazer login
        </a>

    </div>
</x-guest-layout>

<style>
    [x-cloak] { display: none !important; }
    input { caret-color: theme('colors.emerald.500', #10b981); }
</style>

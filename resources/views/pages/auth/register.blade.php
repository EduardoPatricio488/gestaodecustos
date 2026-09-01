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
                confirmPassword: '',
                agreedToTerms: false,
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
                },
                get passwordsMatch() {
                    return this.password && this.confirmPassword && this.password === this.confirmPassword;
                },
                get passwordsMismatch() {
                    return this.password && this.confirmPassword && this.password !== this.confirmPassword;
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
                <div x-show="password.length > 0" x-cloak class="space-y-2 pt-0.5">
                    <div class="flex gap-1">
                        <template x-for="i in 5">
                            <div
                                class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                :class="i <= strength ? strengthColor : 'bg-zinc-200 dark:bg-zinc-800'"
                            ></div>
                        </template>
                    </div>
                    <p class="text-[11px] font-semibold transition-colors" :class="strength <= 1 ? 'text-red-500' : strength <= 2 ? 'text-orange-500' : strength <= 3 ? 'text-yellow-600' : 'text-emerald-600'">
                        Segurança: <span x-text="strengthLabel"></span>
                    </p>

                    {{-- REQUISITOS DE SENHA --}}
                    <div class="space-y-1.5 bg-zinc-50 dark:bg-zinc-800/50 p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Requisitos:</p>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <flux:icon :name="password.length >= 8 ? 'check-circle' : 'circle'" :class="password.length >= 8 ? 'text-emerald-500' : 'text-zinc-300 dark:text-zinc-600'" class="size-3.5 shrink-0" />
                                <span class="text-[10px]" :class="password.length >= 8 ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-zinc-500 dark:text-zinc-400'">Mínimo 8 caracteres</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon :name="/[A-Z]/.test(password) ? 'check-circle' : 'circle'" :class="/[A-Z]/.test(password) ? 'text-emerald-500' : 'text-zinc-300 dark:text-zinc-600'" class="size-3.5 shrink-0" />
                                <span class="text-[10px]" :class="/[A-Z]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-zinc-500 dark:text-zinc-400'">Pelo menos uma letra maiúscula</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon :name="/[0-9]/.test(password) ? 'check-circle' : 'circle'" :class="/[0-9]/.test(password) ? 'text-emerald-500' : 'text-zinc-300 dark:text-zinc-600'" class="size-3.5 shrink-0" />
                                <span class="text-[10px]" :class="/[0-9]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-zinc-500 dark:text-zinc-400'">Pelo menos um número</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon :name="/[^A-Za-z0-9]/.test(password) ? 'check-circle' : 'circle'" :class="/[^A-Za-z0-9]/.test(password) ? 'text-emerald-500' : 'text-zinc-300 dark:text-zinc-600'" class="size-3.5 shrink-0" />
                                <span class="text-[10px]" :class="/[^A-Za-z0-9]/.test(password) ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-zinc-500 dark:text-zinc-400'">Pelo menos um caractere especial (!@#$%)</span>
                            </div>
                        </div>
                    </div>
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
                        x-model="confirmPassword"
                        required
                        autocomplete="new-password"
                        placeholder="Repete a password"
                        class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border {{ $errors->has('password_confirmation') ? 'border-red-400 dark:border-red-700' : 'border-zinc-200 dark:border-zinc-800' }} transition-all rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-400 dark:focus:border-emerald-600"
                        :class="passwordsMismatch ? 'border-red-400 dark:border-red-700' : passwordsMatch ? 'border-emerald-400 dark:border-emerald-600 ring-2 ring-emerald-500/20' : 'border-zinc-200 dark:border-zinc-800'"
                    />
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-3 flex items-center px-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none">
                        <flux:icon x-show="!showConfirm" name="eye" class="size-4" />
                        <flux:icon x-show="showConfirm" name="eye-slash" class="size-4" x-cloak />
                    </button>
                </div>

                {{-- FEEDBACK DE CONFIRMAÇÃO --}}
                <div x-show="password && confirmPassword" x-cloak class="flex items-center gap-2">
                    <template x-if="passwordsMatch">
                        <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                            <flux:icon name="check-circle" class="size-4" />
                            <span class="text-[11px] font-semibold">As passwords coincidem</span>
                        </div>
                    </template>
                    <template x-if="passwordsMismatch">
                        <div class="flex items-center gap-1.5 text-red-600 dark:text-red-400">
                            <flux:icon name="exclamation-circle" class="size-4" />
                            <span class="text-[11px] font-semibold">As passwords não coincidem</span>
                        </div>
                    </template>
                </div>

                <x-input-error :messages="$errors->get('password_confirmation')" class="text-[11px]" />
            </div>

            {{-- TERMOS E PRIVACIDADE --}}
            <div class="flex items-start gap-2.5 pt-0.5">
                <input
                    id="terms"
                    name="terms"
                    type="checkbox"
                    x-model="agreedToTerms"
                    class="size-4 mt-0.5 rounded-md border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-emerald-600 focus:ring-emerald-500/30 focus:ring-2 cursor-pointer transition-all"
                />
                <label for="terms" class="text-[11px] text-zinc-600 dark:text-zinc-400 cursor-pointer select-none">
                    Concordo com os <a href="#" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold">Termos de Serviço</a> e <a href="#" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold">Política de Privacidade</a>
                </label>
            </div>

            {{-- BOTÃO --}}
            <div class="pt-1">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    :disabled="!agreedToTerms || passwordsMismatch"
                    class="group relative w-full h-11 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-400 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 disabled:shadow-emerald-500/10 transition-all duration-200 hover:-translate-y-px active:translate-y-0 active:shadow-md"
                >
                    <flux:icon name="check-circle" class="size-4 opacity-80" wire:loading.remove />
                    <flux:icon name="spinner" class="size-4 opacity-80 animate-spin" wire:loading />
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

        {{-- INFORMAÇÃO DE SEGURANÇA --}}
        <div class="flex items-start gap-2.5 p-2.5 bg-blue-50 dark:bg-blue-950/30 rounded-lg border border-blue-100 dark:border-blue-900/50">
            <flux:icon name="lock-closed" class="size-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
            <div class="space-y-0.5">
                <p class="text-[10px] font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Encriptação de Dados</p>
                <p class="text-[11px] text-blue-600 dark:text-blue-400">Os teus dados são encriptados e armazenados de forma segura.</p>
            </div>
        </div>

    </div>
</x-guest-layout>

<style>
    [x-cloak] { display: none !important; }
    input { caret-color: theme('colors.emerald.500', #10b981); }
</style>

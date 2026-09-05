<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $isSubmitting = false;
    public bool $emailAvailable = true;
    public bool $checkingEmail = false;

    /**
     * Check email availability in real-time
     */
    #[\Livewire\Attributes\On('blur-email')]
    public function checkEmailAvailability(): void
    {
        if (!$this->email) {
            $this->emailAvailable = true;
            return;
        }

        $this->checkingEmail = true;

        try {
            $this->emailAvailable = !User::where('email', strtolower($this->email))->exists();
        } finally {
            $this->checkingEmail = false;
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $this->isSubmitting = true;

        try {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            ]);

            // 1. Gerar o código de 6 dígitos
            $code = rand(100000, 999999);

            // 2. Criar o utilizador
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'verification_code' => $code,
            ]);

            // 3. Enviar o e-mail
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerifyAccountMail($code));
                \Illuminate\Support\Facades\Log::info("E-mail enviado para: " . $user->email);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Erro ao enviar e-mail: " . $e->getMessage());
            }

            event(new \Illuminate\Auth\Events\Registered($user));
            auth()->login($user);

            $this->redirect(route('verification.notice', absolute: false), navigate: true);
        } finally {
            $this->isSubmitting = false;
        }
    }
}; ?>













<div
    class="space-y-7"
    x-data="{
        showPass: false,
        showConfirm: false,
        password: '',
        password_confirmation: '', {{-- 1. ADICIONADO AQUI --}}
        get strength() {
            if (!this.password) return 0;
            let s = 0;
            if (this.password.length >= 8) s++;
            if (this.password.length >= 12) s++;
            if (/[A-Z]/.test(this.password)) s++;
            if (/[0-9]/.test(this.password)) s++;
            if (/[^A-Za-z0-9]/.test(this.password)) s++;
            return s;
        },
        get strengthLabel() {
            return ['', 'Fraca', 'Razoável', 'Boa', 'Forte', 'Excelente'][this.strength] || '';
        },
        get strengthColor() {
            return ['', 'bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-emerald-400', 'bg-emerald-500'][this.strength] || '';
        }
    }"
>

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

    <form wire:submit="register" class="space-y-4">

        {{-- NOME (Manteve-se igual) --}}
        <div class="space-y-1.5">
            <label for="name" class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Nome</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                    <flux:icon name="user" class="size-4 text-zinc-400" />
                </div>
                <input
                    wire:model="name"
                    id="name"
                    type="text"
                    required
                    autofocus
                    placeholder="O teu nome completo"
                    class="w-full h-11 pl-10 pr-4 bg-zinc-50 dark:bg-zinc-900 border @error('name') border-red-400 dark:border-red-700 @else border-zinc-200 dark:border-zinc-800 @enderror rounded-xl text-sm font-medium text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all"
                />
            </div>
            <x-input-error :messages="$errors->get('name')" class="text-[11px]" />
        </div>

        {{-- EMAIL (Manteve-se igual) --}}
        <div class="space-y-1.5">
            <label for="email" class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                    <flux:icon name="envelope" class="size-4 text-zinc-400" />
                </div>
                <input
                    wire:model.blur="email"
                    id="email"
                    type="email"
                    required
                    placeholder="teu@email.com"
                    class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border rounded-xl text-sm font-medium text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all"
                    :class="!$wire.emailAvailable && $wire.email ? 'border-red-400' : 'border-zinc-200'"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- PASSWORD --}}
        <div class="space-y-1.5">
            <label for="password" class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                    <flux:icon name="lock-closed" class="size-4 text-zinc-400" />
                </div>
                <input
                    wire:model.live="password" {{-- 2. ADICIONADO .live --}}
                    id="password"
                    :type="showPass ? 'text' : 'password'"
                    required
                    placeholder="Mínimo 8 caracteres"
                    @input="password = $event.target.value" {{-- Sincroniza com Alpine --}}
                    class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border @error('password') border-red-400 @else border-zinc-200 @enderror rounded-xl text-sm font-medium text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all"
                />
                <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-3 flex items-center px-1 text-zinc-400 hover:text-zinc-600">
                    <flux:icon x-show="!showPass" name="eye" class="size-4" />
                    <flux:icon x-show="showPass" name="eye-slash" class="size-4" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- CONFIRMAR PASSWORD --}}
        <div class="space-y-1.5">
            <label for="password_confirmation" class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Confirmar password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                    <flux:icon name="shield-check" class="size-4 text-zinc-400" />
                </div>
                <input
                    wire:model.live="password_confirmation" {{-- 3. ADICIONADO .live --}}
                    id="password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    required
                    placeholder="Repete a password"
                    @input="password_confirmation = $event.target.value" {{-- 4. ADICIONADO PARA SINCRONIZAR COM ALPINE --}}
                    class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border rounded-xl text-sm font-medium text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all"
                    :class="password_confirmation && password_confirmation !== password ? 'border-red-400' : 'border-zinc-200'"
                />
                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-3 flex items-center px-1 text-zinc-400 hover:text-zinc-600">
                    <flux:icon x-show="!showConfirm" name="eye" class="size-4" />
                    <flux:icon x-show="showConfirm" name="eye-slash" class="size-4" x-cloak />
                </button>
            </div>

            {{-- 5. LÓGICA DE COMPARAÇÃO NO HTML --}}
            <div x-show="password_confirmation && password_confirmation !== password" x-cloak class="text-[11px] text-red-600 font-medium">
                As passwords não coincidem
            </div>
            <div x-show="password_confirmation && password_confirmation === password && password.length > 0" x-cloak class="text-[11px] text-emerald-600 font-medium">
                As passwords coincidem
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        {{-- BOTÃO (Desativa se não coincidirem) --}}
        <div class="pt-2">
            <button
                type="submit"
                :disabled="$wire.isSubmitting || (password !== password_confirmation)"
                class="w-full h-11 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-lg transition-all"
            >
                <span wire:loading.remove wire:target="register">Criar a minha conta</span>
                <span wire:loading wire:target="register">A processar...</span>
            </button>
        </div>
    </form>






        {{-- AVISO DE VALIDAÇÃO --}}
        <div x-show="password && password_confirmation && password !== password_confirmation" x-cloak class="mt-3 p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-2.5">
            <flux:icon name="exclamation-triangle" class="size-4 text-red-600 dark:text-red-400 shrink-0 mt-0.5" />
            <p class="text-xs text-red-700 dark:text-red-300">As passwords devem coincidir para continuar</p>
        </div>
        <div x-show="$wire.email && !$wire.emailAvailable" x-cloak class="mt-3 p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-lg flex items-start gap-2.5">
            <flux:icon name="exclamation-triangle" class="size-4 text-red-600 dark:text-red-400 shrink-0 mt-0.5" />
            <p class="text-xs text-red-700 dark:text-red-300">Utiliza um email diferente ou faz login se já tens conta</p>
        </div>
    </form>

    {{-- SEPARADOR --}}
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-zinc-100 dark:border-zinc-800"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-white dark:bg-zinc-900/50 px-3 text-[10px] font-semibold text-zinc-400 uppercase tracking-widest">Já tens conta?</span>
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

<style>[x-cloak] { display: none !important; }</style>

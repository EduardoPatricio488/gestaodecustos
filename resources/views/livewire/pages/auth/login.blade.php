<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public bool $isSubmitting = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->isSubmitting = true;

        try {
            $this->validate();
            $this->form->authenticate();
            Session::regenerate();

            $user = auth()->user();

            if (session()->pull('pending_app_launch', false)) {
                $this->redirect(route('app.launch', absolute: false), navigate: true);
            } elseif (session()->pull('pending_mobile_login', false)) {
                session()->put('mobile_access_mode', 'mobile');
                $this->redirect(route('mobile.home', absolute: false), navigate: true);
            } elseif ($user->isAdminRole() || $user->is_admin) {
                $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
            } else {
                $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            }
        } finally {
            $this->isSubmitting = false;
        }
    }
}; ?>

<div class="space-y-7">

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

    {{-- ECRÃ DINÂMICO DE CARREGAMENTO APÓS INICIAR SESSÃO --}}
    <div
        wire:loading.flex
        wire:target="login"
        class="fixed !inset-0 !left-0 !top-0 z-[99999] hidden !h-[100dvh] !min-h-screen !w-screen !max-w-none flex-col items-center justify-center overflow-hidden bg-[#070b13] px-6 text-center text-white" style="position:fixed;inset:0;width:100vw;height:100dvh;min-height:100vh;margin:0;border-radius:0;"
        role="status"
        aria-live="polite"
    >
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_45%,rgba(16,185,129,.18),transparent_38%)]"></div>
        <div class="relative flex size-28 items-center justify-center">
            <div class="absolute inset-0 rounded-full border border-emerald-400/20"></div>
            <div class="absolute inset-2 rounded-full border-2 border-transparent border-t-emerald-300 animate-spin"></div>
            <div class="absolute inset-5 rounded-full border-2 border-transparent border-b-cyan-300 animate-[spin_1.6s_linear_infinite_reverse]"></div>
            <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-400/15 text-emerald-300 shadow-[0_0_35px_rgba(16,185,129,.2)]">
                <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 18V9h4v9M10 18V5h4v13M16 18V2h4v16" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        </div>
        <p class="relative mt-8 text-xs font-black uppercase tracking-[.35em] text-emerald-300">Finance Pro AI</p>
        <h2 class="relative mt-3 text-2xl font-black tracking-tight">A preparar a tua conta</h2>
        <p class="relative mt-2 max-w-xs text-sm leading-relaxed text-zinc-400">Estamos a carregar os teus dados e a preparar o teu espaço de trabalho.</p>
        <div class="relative mt-8 h-1 w-48 overflow-hidden rounded-full bg-white/10">
            <div class="h-full w-1/3 animate-[fp-login-progress_1.2s_ease-in-out_infinite] rounded-full bg-gradient-to-r from-emerald-400 to-cyan-300"></div>
        </div>
        <style>
            @keyframes fp-login-progress {
                0% { transform: translateX(-110%); }
                100% { transform: translateX(410%); }
            }
            @media (prefers-reduced-motion: reduce) {
                .animate-spin, .animate-\[spin_1\.6s_linear_infinite_reverse\], .animate-\[fp-login-progress_1\.2s_ease-in-out_infinite\] { animation: none !important; }
            }
        </style>
    </div>

    {{-- FORMULÁRIO --}}
    <form
        wire:submit="login"
        x-data="{
            autoLoginTriggered: false,
            tryAutoLogin() {
                if (this.autoLoginTriggered) return;
                const email = this.$el.querySelector('#email');
                const password = this.$el.querySelector('#password');
                if (email?.value?.trim() && password?.value) {
                    this.autoLoginTriggered = true;
                    this.$el.requestSubmit();
                }
            }
        }"
        x-on:input.debounce.700ms="tryAutoLogin()"
        x-on:change="setTimeout(() => tryAutoLogin(), 250)"
        class="space-y-4"
    >

        {{-- EMAIL --}}
        <div class="space-y-1.5">
            <label for="email" class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                    <flux:icon name="envelope" class="size-4 transition-colors" :class="$isSubmitting ? 'text-zinc-300 dark:text-zinc-700' : 'text-zinc-400'" />
                </div>
                <input
                    wire:model="form.email"
                    id="email"
                    type="email"
                    required
                    autocomplete="username"
                    placeholder="teu@email.com"
                    :disabled="$isSubmitting"
                    aria-label="Endereço de email"
                    class="w-full h-11 pl-10 pr-4 bg-zinc-50 dark:bg-zinc-900 border @error('form.email') border-red-400 dark:border-red-700 @else border-zinc-200 dark:border-zinc-800 @enderror rounded-xl text-base sm:text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 dark:focus:border-brand-600 transition-all"
                />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="text-[11px]" />
        </div>

        {{-- PASSWORD --}}
        <div class="space-y-1.5" x-data="{ show: false }">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate class="text-[11px] font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" :class="$isSubmitting ? 'pointer-events-none opacity-50' : ''">
                        Esqueceste-te?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                    <flux:icon name="lock-closed" class="size-4 transition-colors" :class="$isSubmitting ? 'text-zinc-300 dark:text-zinc-700' : 'text-zinc-400'" />
                </div>
                <input
                    wire:model="form.password"
                    id="password"
                    :type="show ? 'text' : 'password'"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    :disabled="$isSubmitting"
                    aria-label="Password"
                    class="w-full h-11 pl-10 pr-11 bg-zinc-50 dark:bg-zinc-900 border @error('form.password') border-red-400 dark:border-red-700 @else border-zinc-200 dark:border-zinc-800 @enderror rounded-xl text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400 disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-400 dark:focus:border-brand-600 transition-all"
                />
                <button type="button" @click="show = !show" :disabled="$isSubmitting" aria-label="Mostrar/ocultar password" class="absolute inset-y-0 right-3 flex items-center px-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors focus:outline-none">
                    <flux:icon x-show="!show" name="eye" class="size-4" />
                    <flux:icon x-show="show" name="eye-slash" class="size-4" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="text-[11px]" />
        </div>

        {{-- LEMBRAR SESSÃO --}}
        <div class="flex items-center gap-2.5 pt-0.5">
            <input
                wire:model="form.remember"
                id="remember"
                type="checkbox"
                :disabled="$isSubmitting"
                class="size-4 rounded-md border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-brand-600 disabled:opacity-60 disabled:cursor-not-allowed focus:ring-brand-500/30 focus:ring-2 cursor-pointer transition-opacity"
            />
            <label for="remember" class="text-xs text-zinc-500 dark:text-zinc-400 cursor-pointer select-none" :class="$isSubmitting ? 'opacity-60' : ''">
                Manter sessão iniciada
            </label>
        </div>

        {{-- OCULTAR VALORES --}}
        <div
            class="flex items-center gap-2.5 transition-opacity"
            x-data="{ blurNumbers: localStorage.getItem('privacyMode') === 'true' }"
            x-init="$watch('blurNumbers', v => localStorage.setItem('privacyMode', v))"
            :class="$isSubmitting ? 'opacity-60 pointer-events-none' : ''"
        >
            <input
                id="blur-numbers"
                type="checkbox"
                x-model="blurNumbers"
                :disabled="$isSubmitting"
                class="size-4 rounded-md border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 text-brand-600 disabled:opacity-60 disabled:cursor-not-allowed focus:ring-brand-500/30 focus:ring-2 cursor-pointer transition-opacity"
            />
            <label for="blur-numbers" class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400 cursor-pointer select-none">
                <flux:icon name="eye-slash" class="size-3.5 opacity-60" />
                Ocultar valores monetários
            </label>
        </div>

        {{-- BOTÃO --}}
        <div class="pt-1">
            <button
                type="submit"
                :disabled="$isSubmitting"
                class="w-full h-11 flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-500 disabled:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-75 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 disabled:shadow-none transition-all duration-200 hover:-translate-y-px active:translate-y-0 disabled:translate-y-0"
                aria-busy="$isSubmitting"
            >
                <div wire:loading wire:target="login" class="hidden" >
                    <flux:icon name="arrow-path" class="size-4 opacity-80 animate-spin" />
                </div>
                <div wire:loading.remove wire:target="login">
                    <flux:icon name="arrow-right-end-on-rectangle" class="size-4 opacity-80" />
                </div>
                <span wire:loading.remove wire:target="login">Entrar na conta</span>
                <span wire:loading wire:target="login">A carregar...</span>
            </button>
        </div>
    </form>

    {{-- SEPARADOR --}}
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-zinc-100 dark:border-zinc-800"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-white dark:bg-zinc-900/50 px-3 text-[10px] font-semibold text-zinc-400 uppercase tracking-widest">Novo por aqui?</span>
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


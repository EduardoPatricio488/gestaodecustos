<x-guest-layout>
    <div class="space-y-8">
        <div class="space-y-2">
            <flux:heading size="xl" class="font-black tracking-tight">Bem-vindo</flux:heading>
            <flux:subheading>Introduz as tuas credenciais para entrar.</flux:subheading>
        </div>

        {{-- Status da Sessão (Erros/Mensagens) --}}
        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <flux:input
                name="email"
                :label="__('Endereço de Email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                placeholder="exemplo@email.com"
            />

            <!-- Password -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="text-sm font-medium">{{ __('Palavra-passe') }}</label>
                    @if (Route::has('password.request'))
                        <flux:link :href="route('password.request')" class="text-[11px] font-black uppercase tracking-tight" variant="subtle">
                            {{ __('Esqueceste-te?') }}
                        </flux:link>
                    @endif
                </div>
                <flux:input
                    name="password"
                    type="password"
                    required
                    placeholder="••••••••"
                    viewable
                />
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Manter sessão iniciada')" />

            <div class="pt-2">
                <flux:button variant="primary" type="submit" class="w-full !h-12 font-bold text-md rounded-2xl">
                    {{ __('Entrar na Conta') }}
                </flux:button>
            </div>
        </form>

        <flux:separator />

        <div class="text-center">
            <p class="text-sm text-zinc-500">
                Ainda não tens conta?
                <flux:link :href="route('register')" wire:navigate class="font-black text-brand-600 dark:text-brand-400">Regista-te grátis</flux:link>
            </p>
        </div>
    </div>
</x-guest-layout>

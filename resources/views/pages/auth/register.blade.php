<x-guest-layout>
    <div class="space-y-8">
        <div class="space-y-2">
            <flux:heading size="xl" class="font-black tracking-tight text-center">Criar Conta</flux:heading>
            <flux:subheading class="text-center">Começa a gerir os teus custos hoje.</flux:subheading>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Nome -->
            <flux:input
                name="name"
                :label="__('Nome Completo')"
                :value="old('name')"
                type="text"
                required
                autofocus
                placeholder="O teu nome"
            />

            <!-- Email -->
            <flux:input
                name="email"
                :label="__('Endereço de Email')"
                :value="old('email')"
                type="email"
                required
                placeholder="email@exemplo.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Palavra-passe')"
                type="password"
                required
                placeholder="Mínimo 8 caracteres"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirmar Palavra-passe')"
                type="password"
                required
                placeholder="Repete a senha"
                viewable
            />

            <div class="pt-4">
                <flux:button type="submit" variant="primary" class="w-full !h-12 font-bold text-md rounded-2xl shadow-xl shadow-brand-500/20">
                    {{ __('Criar Minha Conta') }}
                </flux:button>
            </div>
        </form>

        <flux:separator />

        <div class="text-center">
            <p class="text-sm text-zinc-500">
                Já tens uma conta?
                <flux:link :href="route('login')" wire:navigate class="font-black text-brand-600 dark:text-brand-400">Fazer Login</flux:link>
            </p>
        </div>
    </div>
</x-guest-layout>

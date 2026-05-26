<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
        $this->dispatch('toast', text: 'Palavra-passe alterada!');
    }
}; ?>

<section class="space-y-6">
    <header>
        <flux:heading size="lg" class="font-black">Segurança da Conta</flux:heading>
        <flux:subheading>Garante que a tua conta usa uma palavra-passe forte e aleatória.</flux:subheading>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <flux:input
            wire:model="current_password"
            label="Palavra-passe Atual"
            type="password"
            viewable
            placeholder="••••••••"
        />

        <flux:input
            wire:model="password"
            label="Nova Palavra-passe"
            type="password"
            viewable
            placeholder="Mínimo 8 caracteres"
        />

        <flux:input
            wire:model="password_confirmation"
            label="Confirmar Nova Palavra-passe"
            type="password"
            viewable
            placeholder="Repete a senha"
        />

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">Atualizar Password</flux:button>
        </div>
    </form>
</section>

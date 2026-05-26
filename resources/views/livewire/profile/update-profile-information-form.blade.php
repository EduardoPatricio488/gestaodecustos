<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
        $this->dispatch('toast', text: 'Perfil atualizado com sucesso!');
    }

    public function sendVerification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="space-y-6">
    <header>
        <flux:heading size="lg" class="font-black">Informações do Perfil</flux:heading>
        <flux:subheading>Atualiza o teu nome de exibição e endereço de email.</flux:subheading>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <flux:input
            wire:model="name"
            label="Nome Completo"
            type="text"
            required
            autofocus
            autocomplete="name"
            placeholder="Como queres ser chamado?"
        />

        <div class="space-y-4">
            <flux:input
                wire:model="email"
                label="Endereço de Email"
                type="email"
                required
                autocomplete="username"
                placeholder="teu@email.com"
            />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800">
                    <p class="text-xs text-amber-700 dark:text-amber-400 font-medium">
                        O teu email ainda não foi verificado.
                        <button wire:click.prevent="sendVerification" class="underline font-bold hover:text-amber-800">
                            Clica aqui para reenviar o email de verificação.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-bold text-emerald-600">
                            Um novo link de verificação foi enviado.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">Guardar Alterações</flux:button>
        </div>
    </form>
</section>

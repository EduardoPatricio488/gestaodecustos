<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <flux:heading size="lg" class="text-red-600 font-black">Eliminar Conta</flux:heading>
        <flux:subheading>Esta ação é permanente e todos os teus dados serão apagados para sempre.</flux:subheading>
    </header>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger">Eliminar Conta Definitivamente</flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" position="center" class="md:w-[450px] p-6">
        <form wire:submit="deleteUser" class="space-y-6">
            <flux:heading size="lg">Tens a certeza absoluta?</flux:heading>

            <flux:subheading>
                Uma vez eliminada, não há volta atrás. Por favor, introduz a tua palavra-passe para confirmar que queres apagar todos os teus registos financeiros.
            </flux:subheading>

            <flux:input
                wire:model="password"
                label="Confirma com a tua Password"
                type="password"
                placeholder="Password de segurança"
            />

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger">Sim, Eliminar Tudo</flux:button>
            </div>
        </form>
    </flux:modal>
</section>

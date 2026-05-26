<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    /**
     * Reseta a página da paginação sempre que a pesquisa muda.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Alterna o estado de atividade do utilizador (Banir/Reativar).
     */
    public function toggleActive($userId)
    {
        // Segurança: Impede que o Admin se banir a si próprio por erro
        if ($userId === auth()->id()) {
            $this->dispatch('toast', variant: 'error', text: 'Não podes desativar a tua própria conta.');
            return;
        }

        $user = User::findOrFail($userId);
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'reativado' : 'banido';
        $this->dispatch('toast', text: "Utilizador {$status} com sucesso!");
    }

    /**
     * Elimina um utilizador definitivamente.
     */
    public function deleteUser($userId)
    {
        if ($userId === auth()->id()) {
            $this->dispatch('toast', variant: 'error', text: 'Não podes eliminar a tua própria conta.');
            return;
        }

        User::findOrFail($userId)->delete();
        $this->dispatch('toast', text: 'Utilizador removido permanentemente.');
    }

    public function render()
    {
        // Procura utilizadores por nome ou email e ordena pelos mais recentes
        $users = User::where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users
        ]);
    }
}

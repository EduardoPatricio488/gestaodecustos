<?php

namespace App\Livewire;

use App\Models\Workspace;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ManageTeam extends Component
{
    public $newWorkspaceName;
    public $type = 'personal';
    public $inputInviteCode; // Para quem quer entrar

    // Gerar um novo código para o workspace atual
    public function generateCode()
    {
        $workspace = auth()->user()->currentWorkspace;
        $workspace->update([
            'invite_code' => Workspace::generateUniqueCode()
        ]);

        $this->dispatch('toast', text: 'Código gerado com sucesso!');
    }

    // Entrar num workspace de outra pessoa usando um código
    public function joinWithCode()
    {
        $this->validate(['inputInviteCode' => 'required|string|exists:workspaces,invite_code']);

        $workspace = Workspace::where('invite_code', $this->inputInviteCode)->first();

        // Verifica se já faz parte
        if ($workspace->users()->where('user_id', auth()->id())->exists()) {
            $this->dispatch('toast', variant: 'error', text: 'Já fazes parte deste espaço.');
            return;
        }

        // Adiciona o utilizador ao workspace
        auth()->user()->workspaces()->attach($workspace->id, ['role' => 'member']);

        // Muda para o novo workspace automaticamente
        auth()->user()->update(['current_workspace_id' => $workspace->id]);

        $this->inputInviteCode = '';
        session()->flash('ok', 'Entraste no espaço: ' . $workspace->name);
        return redirect()->route('dashboard');
    }

    public function createWorkspace()
    {
        $this->validate(['newWorkspaceName' => 'required|string|min:3']);

        $ws = Workspace::create([
            'name' => $this->newWorkspaceName,
            'type' => $this->type,
            'owner_id' => auth()->id(),
            'invite_code' => Workspace::generateUniqueCode() // Já cria com código
        ]);

        auth()->user()->workspaces()->attach($ws->id, ['role' => 'admin']);
        auth()->user()->update(['current_workspace_id' => $ws->id]);

        return redirect()->route('manage-team');
    }

    public function render()
    {
        return view('livewire.manage-team', [
            'currentWorkspace' => auth()->user()->currentWorkspace,
            'members' => auth()->user()->currentWorkspace->users
        ]);
    }
}

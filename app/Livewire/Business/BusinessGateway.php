<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Workspace, Employee};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessGateway extends Component
{
    public $accessCode = '';

    public function mount()
    {
        $user = Auth::user();

        if (request()->has('new')) {
            $user->update(['current_workspace_id' => null]);
            return;
        }

        if ($user->current_workspace_id) {
            return redirect()->route('hub.business.dashboard');
        }
    }

    public function enterAsOwner()
    {
        $user = auth()->user();

        $workspace = $user->workspaces()
            ->where('workspaces.id', $user->current_workspace_id)
            ->wherePivot('role', 'admin')
            ->first();

        if (!$workspace) {
            $workspace = $user->workspaces()->wherePivot('role', 'admin')->first();
        }

        if (!$workspace) {
            $workspace = Workspace::create([
                'name'     => 'Gestão de ' . explode(' ', $user->name)[0],
                'type'     => 'business',
                'plan'     => $user->plan ?? 'pro',
                'owner_id' => $user->id,
            ]);

            $workspace->users()->attach($user->id, ['role' => 'admin']);

            $this->dispatch('toast', variant: 'success', heading: 'Negócio Configurado', message: 'O teu novo espaço de trabalho foi criado com sucesso.');
        }

        $user->update(['current_workspace_id' => $workspace->id]);

        return redirect()->route('hub.business.dashboard');
    }

    /**
     * Opção: Entrar como Colaborador
     * ADAPTADO: Agora valida Tokens de Colaborador (EMP-...) e Códigos de Empresa
     */
    public function joinAsCollaborator()
    {
        $this->validate([
            'accessCode' => 'required|string'
        ], [
            'accessCode.required' => 'Insira o código fornecido pelo seu administrador.'
        ]);

        $code = strtoupper(trim($this->accessCode));
        $user = Auth::user();

        // 1. TENTAR ENCONTRAR PELO TOKEN DE COLABORADOR (Gerado no TeamHub)
        $employee = Employee::where('portal_token', $code)->first();

        if ($employee) {
            // Vincula este utilizador à ficha de colaborador existente
            $employee->update(['user_id' => $user->id]);

            $workspace = $employee->workspace;

            // Garante que o utilizador está na lista de acessos do workspace
            $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'editor']]);

            // Define o workspace ativo e redireciona
            $user->update(['current_workspace_id' => $workspace->id]);

            $this->dispatch('toast', variant: 'success', heading: 'Sucesso', message: 'Ligação à ficha de colaborador efetuada!');
            return redirect()->route('hub.business.dashboard');
        }

        // 2. TENTAR ENCONTRAR PELO CÓDIGO GERAL DA EMPRESA (Invite Code)
        $workspace = Workspace::where('invite_code', $code)->first();

        if ($workspace) {
            $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'viewer']]);

            Employee::firstOrCreate(
                ['user_id' => $user->id, 'workspace_id' => $workspace->id],
                [
                    'name' => $user->name,
                    'role' => 'Colaborador',
                    'salary' => 0,
                    'active' => true
                ]
            );

            $user->update(['current_workspace_id' => $workspace->id]);

            $this->dispatch('toast', variant: 'success', heading: 'Sucesso', message: 'Bem-vindo à equipa!');
            return redirect()->route('hub.business.dashboard');
        }

        // Se nenhum código for válido
        $this->addError('accessCode', 'Este código de acesso é inválido ou já expirou.');
    }

    public function render()
    {
        return view('livewire.business.business-gateway')
            ->layout('components.layouts.app');
    }
}

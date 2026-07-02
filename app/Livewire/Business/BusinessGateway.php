<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Workspace, Employee};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessGateway extends Component
{
    public $accessCode = '';

    /**
     * O mount decide se mostra as opções ou se entra direto.
     */
    public function mount()
    {
        $user = Auth::user();

        // Se clicaste no "+ Novo Espaço" (parâmetro 'new'), limpamos a seleção
        // para forçar a visualização dos dois cartões de escolha.
        if (request()->has('new')) {
            $user->update(['current_workspace_id' => null]);
            return;
        }

        // Se já tens uma empresa selecionada (via clique na sidebar),
        // o sistema deixa-te passar direto para o Dashboard.
        if ($user->current_workspace_id) {
            return redirect()->route('hub.business.dashboard');
        }
    }

    /**
     * Opção: Entrar como CEO
     * CORREÇÃO: Agora ele verifica primeiro se já selecionaste uma empresa
     * antes de tentar adivinhar qual é a primeira.
     */
    public function enterAsOwner()
    {
        $user = auth()->user();

        // 1. Tentar encontrar o workspace que já está definido no teu perfil (vencendo o erro do 'first()')
        $workspace = $user->workspaces()
            ->where('workspaces.id', $user->current_workspace_id)
            ->wherePivot('role', 'admin')
            ->first();

        // 2. Se o ID atual não for de uma empresa tua (ou estiver vazio),
        // aí sim procuramos a primeira empresa onde és Admin.
        if (!$workspace) {
            $workspace = $user->workspaces()->wherePivot('role', 'admin')->first();
        }

        // 3. SE NÃO EXISTIR NENHUMA: Criamos a tua primeira empresa agora.
        if (!$workspace) {
            $workspace = Workspace::create([
                'name'     => 'Gestão de ' . explode(' ', $user->name)[0],
                'type'     => 'business',
                'plan'     => $user->plan ?? 'pro',
                'owner_id' => $user->id,
            ]);

            // Vincula o utilizador como Administrador
            $workspace->users()->attach($user->id, ['role' => 'admin']);

            $this->dispatch('toast', variant: 'success', heading: 'Negócio Configurado', message: 'O teu novo espaço de trabalho foi criado com sucesso.');
        }

        // 4. Atualizar o workspace atual e entrar
        $user->update(['current_workspace_id' => $workspace->id]);

        return redirect()->route('hub.business.dashboard');
    }

    /**
     * Opção: Entrar como Colaborador (Validando o Token)
     */
    public function joinAsCollaborator()
    {
        $this->validate([
            'accessCode' => 'required|string'
        ], [
            'accessCode.required' => 'Insira o código fornecido pelo seu administrador.'
        ]);

        // 1. Procurar o workspace pelo código
        $workspace = Workspace::where('invite_code', strtoupper($this->accessCode))->first();

        if (!$workspace) {
            $this->addError('accessCode', 'Este token de acesso é inválido ou não existe.');
            return;
        }

        $user = Auth::user();

        // 2. Vincular o utilizador ao workspace na tabela pivot (Viewer/Colaborador)
        $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'viewer']]);

        // 3. Criar ficha de colaborador para aparecer no painel de RH
        Employee::firstOrCreate(
            ['user_id' => $user->id, 'workspace_id' => $workspace->id],
            [
                'name' => $user->name,
                'role' => 'Colaborador',
                'salary' => 0,
                'pay_day' => 25,
                'active' => true
            ]
        );

        // 4. Ativar o workspace na sessão do utilizador
        $user->update(['current_workspace_id' => $workspace->id]);

        // 5. NOTIFICAR O CEO
        $owner = $workspace->users()->wherePivot('role', 'admin')->first();
        if ($owner) {
            DB::table('app_notifications')->insert([
                'user_id'    => $owner->id,
                'title'      => 'Novo Colaborador 👤',
                'message'    => $user->name . " entrou na empresa através do token de acesso.",
                'type'       => 'info',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->dispatch('toast', variant: 'success', heading: 'Sucesso', message: 'Acesso validado. Bem-vindo à equipa!');

        return redirect()->route('hub.business.dashboard');
    }

    public function render()
    {
        return view('livewire.business.business-gateway')
            ->layout('components.layouts.app');
    }
}

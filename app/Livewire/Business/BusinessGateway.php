<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Workspace, Employee};
use Illuminate\Support\Facades\Auth;

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
        // Agora o CEO vai para o Onboarding em vez de criar logo
        return redirect()->route('hub.business.onboarding');
    }

    public function joinAsCollaborator()
    {
        $this->validate(['accessCode' => 'required|string']);
        $code = strtoupper(trim($this->accessCode));
        $user = Auth::user();

        $employee = Employee::where('portal_token', $code)->first();

        if ($employee) {
            $employee->update(['user_id' => $user->id]);
            $workspace = $employee->workspace;
            $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'editor']]);
            $user->update(['current_workspace_id' => $workspace->id]);
            return redirect()->route('hub.business.dashboard');
        }

        $this->addError('accessCode', 'Código inválido.');
    }

    public function render()
    {
        return view('livewire.business.business-gateway')
            ->layout('components.layouts.app');
    }
}

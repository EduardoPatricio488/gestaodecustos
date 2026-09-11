<?php

namespace App\Livewire\Business;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

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
        return redirect()->route('hub.business.onboarding');
    }

    public function joinAsCollaborator()
    {
        $this->validate(['accessCode' => 'required|string|min:32|max:128']);
        $code = trim($this->accessCode);
        $user = Auth::user();
        $rateLimitKey = 'employee-invite:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $this->addError('accessCode', 'Demasiadas tentativas. Tenta novamente mais tarde.');

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        $employee = DB::transaction(function () use ($code, $user) {
            // O convite é uma credencial independente do workspace atualmente
            // ativo. Por isso, a procura tem de ignorar o scope de workspace.
            $candidates = Employee::withoutGlobalScopes()
                ->whereNull('user_id')
                ->where('active', true)
                ->where('suspended', false)
                ->whereNull('terminated_at')
                ->whereNull('invite_used_at')
                ->whereNull('invite_revoked_at')
                ->where(function ($query) {
                    $query->whereNull('invite_expires_at')
                        ->orWhere('invite_expires_at', '>', now());
                })
                ->whereNotNull('portal_token')
                ->lockForUpdate()
                ->get();

            $employee = $candidates->first(
                fn (Employee $candidate) => Hash::check($code, (string) $candidate->portal_token)
            );

            if (! $employee) {
                return null;
            }

            $workspace = Workspace::withoutGlobalScopes()
                ->whereKey($employee->workspace_id)
                ->first();

            if (! $workspace || ! in_array($workspace->type, ['business', 'company'], true)) {
                return null;
            }

            // Primeiro cria a relação de acesso. Isto é necessário para que o
            // audit log possa validar que o utilizador pertence ao workspace.
            $workspace->users()->syncWithoutDetaching([
                $user->id => ['role' => 'employee'],
            ]);

            $user->update([
                'current_workspace_id' => $workspace->id,
            ]);

            // A aceitação de um convite é uma operação de onboarding especial:
            // depois de o utilizador entrar como employee, o permissionamento
            // normal de Employee::saving() não lhe permite alterar o próprio
            // registo Employee. Fazemos a transição atómica diretamente na BD,
            // mantendo todas as condições de consumo do convite.
            $updated = DB::table('employees')
                ->where('id', $employee->id)
                ->whereNull('user_id')
                ->whereNull('invite_used_at')
                ->whereNotNull('portal_token')
                ->update([
                    'user_id' => $user->id,
                    'invite_used_at' => now(),
                    'portal_token' => null,
                    'updated_at' => now(),
                ]);

            if ($updated !== 1) {
                throw new \RuntimeException('O convite já foi utilizado ou deixou de estar disponível.');
            }

            ActivityLog::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'action' => 'updated',
                'description' => 'Aceitou o convite de colaborador.',
                'model_type' => 'Employee',
                'model_id' => $employee->id,
                'properties' => [
                    'new' => [
                        'user_id' => $user->id,
                        'invite_used_at' => now()->toDateTimeString(),
                        'portal_token' => '[REDACTED]',
                    ],
                ],
                'metadata' => [
                    'workspace_id' => $workspace->id,
                    'ip' => request()?->ip(),
                    'user_agent' => request()?->userAgent(),
                ],
            ]);

            return $employee;
        });

        if ($employee) {
            RateLimiter::clear($rateLimitKey);

            return redirect()->route('hub.business.dashboard');
        }

        $this->addError('accessCode', 'Código inválido ou convite já utilizado.');
    }

    public function render()
    {
        return view('livewire.business.business-gateway')
            ->layout('components.layouts.app');
    }
}

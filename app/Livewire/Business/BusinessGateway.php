<?php

namespace App\Livewire\Business;

use App\Models\Employee;
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
        // Agora o CEO vai para o Onboarding em vez de criar logo
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
            $candidates = Employee::whereNull('user_id')
                ->where('active', true)
                ->where('suspended', false)
                ->whereNull('terminated_at')
                ->whereNull('invite_used_at')
                ->whereNull('invite_revoked_at')
                ->where(function ($query) {
                    $query->whereNull('invite_expires_at')
                        ->orWhere('invite_expires_at', '>', now());
                })
                ->lockForUpdate()
                ->get();

            $employee = $candidates->first(fn (Employee $candidate) => Hash::check($code, $candidate->portal_token));

            if (! $employee) {
                return null;
            }

            $employee->update([
                'user_id' => $user->id,
                'invite_used_at' => now(),
                'portal_token' => null,
            ]);

            $workspace = $employee->workspace;
            $workspace->users()->syncWithoutDetaching([$user->id => ['role' => 'editor']]);
            $user->update(['current_workspace_id' => $workspace->id]);

            return $employee;
        });

        if ($employee) {
            RateLimiter::clear($rateLimitKey);

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

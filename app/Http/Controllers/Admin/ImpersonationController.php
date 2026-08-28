<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    private const SESSION_KEY = 'admin_impersonation';

    private const TTL_MINUTES = 30;

    public function start(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($admin?->isAdminRole(), 403);
        abort_if($admin->id === $user->id, 403);
        abort_if($user->isAdminRole() || ! $user->isActive(), 403);
        abort_if($request->session()->has(self::SESSION_KEY), 409);

        $startedAt = now();
        $log = ImpersonationLog::create([
            'actor_user_id' => $admin->id,
            'target_user_id' => $user->id,
            'action' => 'started',
            'started_at' => $startedAt,
        ]);

        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, [
            'actor_id' => $admin->id,
            'target_id' => $user->id,
            'started_at' => $startedAt->toIso8601String(),
            'expires_at' => $startedAt->copy()->addMinutes(self::TTL_MINUTES)->toIso8601String(),
            'log_id' => $log->id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function stop(Request $request): RedirectResponse
    {
        $context = $request->session()->get(self::SESSION_KEY);

        if (! is_array($context) || ! isset($context['actor_id'], $context['target_id'])) {
            return redirect()->route('dashboard');
        }

        $actor = User::query()->find($context['actor_id']);
        $target = $request->user();

        abort_unless($actor?->isAdminRole(), 403);
        abort_unless($target && (int) $target->id === (int) $context['target_id'], 403);

        $this->finishLog($context);
        Auth::login($actor);
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->regenerate();

        return redirect()->route('admin.users');
    }

    public static function sessionKey(): string
    {
        return self::SESSION_KEY;
    }

    private function finishLog(array $context): void
    {
        if (! empty($context['log_id'])) {
            ImpersonationLog::query()->whereKey($context['log_id'])->update([
                'action' => 'ended',
                'ended_at' => now(),
            ]);
        }
    }
}

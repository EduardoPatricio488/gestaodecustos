<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\ImpersonationController;
use App\Models\ImpersonationLog;
use App\Models\User;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureImpersonationIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = $request->session()->get(ImpersonationController::sessionKey());

        if (! is_array($context)) {
            return $next($request);
        }

        $expiresAt = isset($context['expires_at']) ? CarbonImmutable::parse($context['expires_at']) : null;
        $actor = isset($context['actor_id']) ? User::query()->find($context['actor_id']) : null;
        $target = $request->user();

        if (! $expiresAt || $expiresAt->isPast() || ! $actor?->isAdminRole() || ! $target || (int) $target->id !== (int) ($context['target_id'] ?? 0)) {
            if (! empty($context['log_id'])) {
                ImpersonationLog::query()->whereKey($context['log_id'])->update([
                    'action' => 'expired',
                    'ended_at' => now(),
                ]);
            }

            if ($actor?->isAdminRole()) {
                Auth::login($actor);
            } else {
                Auth::logout();
            }

            $request->session()->forget(ImpersonationController::sessionKey());

            return redirect()->route('admin.users');
        }

        return $next($request);
    }
}

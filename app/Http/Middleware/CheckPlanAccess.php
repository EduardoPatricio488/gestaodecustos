<?php

namespace App\Http\Middleware;

use App\Models\SubscriptionPlan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanAccess
{
    public function handle(Request $request, Closure $next, string $planRequired): Response
    {
        $user = auth()->user();

        // Se não houver utilizador (apesar do middleware auth estar antes), bloqueia
        if (! $user) {
            return redirect()->route('login');
        }

        $userPlan = $user->currentPlanSlug();
        $catalog = null;
        try {
            $catalog = SubscriptionPlan::where('slug', $userPlan)->where('is_active', true)->first();
        } catch (\Throwable $e) {
            $catalog = null;
        }
        $hasAccess = false;

        if (in_array($planRequired, ['premium', 'pro'], true)) {
            $hasAccess = $user->isPaidPlan()
                || ($catalog && ($catalog->price > 0 || $catalog->hasFeature('ia_access')));
        }

        if ($planRequired === 'business') {
            $hasAccess = $user->isBusinessPlan()
                || ($catalog && $catalog->hasFeature('business_mode'));
        }

        if (! $hasAccess) {
            // Envia o utilizador para a página de planos com um aviso
            return redirect()->route('hub.pricing')
                ->with('toast', ['variant' => 'error', 'text' => 'O seu plano atual não permite aceder a esta funcionalidade.']);
        }

        return $next($request);
    }
}

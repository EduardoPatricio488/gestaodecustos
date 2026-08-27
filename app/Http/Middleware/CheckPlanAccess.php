<?php

namespace App\Http\Middleware;

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

        $userPlan = $user->plan ?? 'free'; // 'free', 'plus', 'pro'

        $hasAccess = false;

        // Lógica de Hierarquia:
        // Se a rota pedir 'premium': Utilizadores 'plus' e 'pro' entram.
        if ($planRequired === 'premium') {
            $hasAccess = in_array($userPlan, ['plus', 'pro']) || $user->isStar() || $user->isDiamond();
        }

        // Se a rota pedir 'business': Apenas utilizadores 'pro' (ou Diamond) entram.
        if ($planRequired === 'business') {
            $hasAccess = ($userPlan === 'pro') || $user->isDiamond();
        }

        if (! $hasAccess) {
            // Envia o utilizador para a página de planos com um aviso
            return redirect()->route('hub.pricing')
                ->with('toast', ['variant' => 'error', 'text' => 'O seu plano atual não permite aceder a esta funcionalidade.']);
        }

        return $next($request);
    }
}

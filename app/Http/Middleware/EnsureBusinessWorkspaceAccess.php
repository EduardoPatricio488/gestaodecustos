<?php

namespace App\Http\Middleware;

use App\Services\BusinessAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessWorkspaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // O gateway e o onboarding são precisamente os pontos onde o utilizador
        // ainda pode não ter um workspace empresarial selecionado.
        if (! str_starts_with($request->path(), 'empresa/')) {
            return $next($request);
        }

        if (in_array($request->path(), ['empresa/acesso', 'empresa/onboarding'], true)) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user) abort(401);

        app(BusinessAccessService::class)->assertWorkspace($user);

        return $next($request);
    }
}

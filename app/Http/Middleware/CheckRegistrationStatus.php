<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $isRegisterRoute = $request->is('register') || $request->is('register/*');

        if ($isRegisterRoute) {
            $allowRegistration = SiteSetting::where('key', 'allow_registration')->value('value');

            // Se for '0' ou se a chave nem existir, bloqueamos
            if ($allowRegistration === '0' || $allowRegistration === null) {
                return response()->view('errors.registration-closed', [], 403);
            }
        }

        return $next($request);
    }
}

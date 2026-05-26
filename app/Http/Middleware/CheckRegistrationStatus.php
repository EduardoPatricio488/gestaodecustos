<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // TESTE: Se vires um ecrã preto com "Middleware a funcionar", então o erro é na lógica abaixo.
        // Se NÃO vires o ecrã preto, o erro é no bootstrap/app.php
        // dd('Middleware a funcionar');

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

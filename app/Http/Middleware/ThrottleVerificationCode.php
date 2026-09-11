<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleVerificationCode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('verificar-codigo')) {
            $userId = (string) ($request->user()?->id ?? 'guest');
            $key = 'verify-code:'.sha1($userId.'|'.$request->ip());

            if (RateLimiter::tooManyAttempts($key, 5)) {
                return back()->withErrors([
                    'code' => 'Demasiadas tentativas. Tenta novamente dentro de um minuto.',
                ]);
            }

            RateLimiter::hit($key, 60);
        }

        return $next($request);
    }
}

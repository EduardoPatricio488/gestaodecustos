<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Se o utilizador estiver logado
        if (Auth::check()) {
            $user = Auth::user();

            // Só atualizamos a base de dados se o último registo for superior a 5 minutos
            // para não sobrecarregar o servidor em cada clique.
            if (!$user->last_login_at || $user->last_login_at->diffInMinutes(now()) >= 5) {
                $user->forceFill([
                    'last_login_at' => now(),
                    'last_ip' => $request->ip(),
                ])->save();
            }
        }

        return $next($request);
    }
}

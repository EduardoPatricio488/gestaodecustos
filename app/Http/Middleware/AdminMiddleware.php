<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Manipula uma requisição de entrada.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verifica se o utilizador está logado
        // 2. Verifica se a coluna 'is_admin' é verdadeira (true) na base de dados
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'moderator', 'analyst'])) {
            return $next($request);
        }

        // Se não for admin, redireciona para o dashboard normal com uma mensagem de erro silenciosa
        return redirect()->route('dashboard');
    }
}

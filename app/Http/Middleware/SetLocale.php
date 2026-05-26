<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Aplica o idioma preferido do utilizador globalmente.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verifica se o utilizador está logado
        if (auth()->check()) {
            $userLocale = auth()->user()->locale;

            // 2. Se o user tiver um idioma definido, aplica-o à aplicação
            if ($userLocale) {
                App::setLocale($userLocale);

                // 3. Opcional: Ajusta também o idioma das datas (Carbon)
                setlocale(LC_TIME, $userLocale);
            }
        }

        return $next($request);
    }
}

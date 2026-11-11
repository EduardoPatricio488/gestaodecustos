<?php

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Symfony\\Component\\HttpFoundation\\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');

        // Permitir o servidor Vite apenas em ambiente local, sem alargar a CSP em produção.
        $viteDevSources = app()->environment('local')
            ? ' http://localhost:5173 http://127.0.0.1:5173'
            : '';
        $viteDevConnections = app()->environment('local')
            ? ' http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173'
            : '';

        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self' https:",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self' https:",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https:" . $viteDevSources,
            "style-src 'self' 'unsafe-inline' https:" . $viteDevSources,
            // 'unsafe-eval' é necessário para o Alpine (Livewire 3 / Flux) avaliar x-data, x-show, @click
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:" . $viteDevSources,
            "connect-src 'self' https: wss:" . $viteDevConnections,
            "frame-src 'self' https:",
        ]));

        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. CONFIAR NO PROXY (NGROK)
        $middleware->trustProxies(at: '*');

        // 2. MIDDLEWARES DA CAMADA WEB
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\CheckRegistrationStatus::class,
            \App\Http\Middleware\UpdateUserActivity::class, // <-- ADICIONA ESTA LINHA AQUI
        ]);

        // 3. ATALHOS DE MIDDLEWARE (ALIAS)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

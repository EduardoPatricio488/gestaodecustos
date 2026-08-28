<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckPlanAccess;
use App\Http\Middleware\CheckRegistrationStatus;
use App\Http\Middleware\EnsureImpersonationIsValid;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\UpdateUserActivity;
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
            SetLocale::class,
            CheckMaintenanceMode::class,
            CheckRegistrationStatus::class,
            UpdateUserActivity::class,
            EnsureImpersonationIsValid::class,
        ]);

        // 🔥 PERMITIR WEBHOOKS DO STRIPE (POST)
        $middleware->validateCsrfTokens(except: [
            'api/whatsapp/webhook',
            'stripe/*',
        ]);

        // 3. ATALHOS DE MIDDLEWARE (ALIAS)
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'plan' => CheckPlanAccess::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

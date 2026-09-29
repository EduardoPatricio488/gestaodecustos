<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckPlanAccess;
use App\Http\Middleware\CheckRegistrationStatus;
use App\Http\Middleware\EnsureBusinessWorkspaceAccess;
use App\Http\Middleware\EnsureImpersonationIsValid;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ThrottleVerificationCode;
use App\Http\Middleware\UpdateUserActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->trustHosts(at: fn () => [
            '^financepro[.]com(:[0-9]+)?$',
            '^www[.]financepro[.]com(:[0-9]+)?$',
            '^gestaodecustos[.]onrender[.]com(:[0-9]+)?$',
            '^localhost(:[0-9]+)?$',
            '^127[.]0[.]0[.]1(:[0-9]+)?$',
        ]);

        $middleware->web(append: [
            ForceHttps::class,
            SecurityHeaders::class,
            SetLocale::class,
            CheckMaintenanceMode::class,
            CheckRegistrationStatus::class,
            UpdateUserActivity::class,
            EnsureImpersonationIsValid::class,
            EnsureBusinessWorkspaceAccess::class,
            ThrottleVerificationCode::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/whatsapp/webhook',
            'stripe/*',
        ]);

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'plan' => CheckPlanAccess::class,
            'business.workspace' => EnsureBusinessWorkspaceAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! app()->environment('production')) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($status >= 500) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Ocorreu um erro interno. Tenta novamente mais tarde.'], 500)
                    : response()->view('errors.500', [], 500);
            }

            if ($status === 429) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Demasiados pedidos. Tenta novamente mais tarde.'], 429)
                    : response()->view('errors.429', [], 429);
            }

            return null;
        });
    })->create();

<?php

namespace AppHttpMiddleware;

use Closure;
use IlluminateHttpRequest;
use SymfonyComponentHttpFoundationResponse;

class AdminOnlyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return $next($request);
    }
}

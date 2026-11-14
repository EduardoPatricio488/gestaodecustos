<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceMobileOnlyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user()
            && $request->session()->get('mobile_access_mode') === 'mobile'
            && ! $request->routeIs('mobile.home', 'logout')
            && ! $request->is('livewire/*')
            && ! $request->is('api/*')
        ) {
            return redirect()->route('mobile.home');
        }

        return $next($request);
    }
}

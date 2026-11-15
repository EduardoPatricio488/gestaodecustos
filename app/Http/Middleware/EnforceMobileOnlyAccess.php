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
            && ! $request->routeIs(
                'mobile.home',
                'logout',
                'device.mode',
                'device.access',
                'app.launch',
                'app.launch.choose',
                'expenses.edit',
                'hub.category',
                'hub.incomes',
                'hub.investments',
                'hub.goals',
                'hub.reminders',
                'hub.subscriptions',
                'hub.banco',
                'hub.fitness',
                'hub.calendar',
                'social.hub',
            )
            && ! $request->is('livewire/*')
            && ! $request->is('api/*')
        ) {
            return redirect()->route('mobile.home');
        }

        return $next($request);
    }
}

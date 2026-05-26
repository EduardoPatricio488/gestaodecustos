<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Trata o pedido recebido.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Obtém o estado do modo de manutenção
        $isMaintenance = SiteSetting::get('maintenance_mode', '0') === '1';

        if ($isMaintenance) {

            $user = Auth::user();
            $isAdmin = $user && $user->is_admin;

            /**
             * EXCEÇÕES (Rotas que funcionam mesmo em manutenção):
             * - / (Página inicial/Landing page)
             * - login / logout
             * - admin/* (Para tu poderes entrar e desligar o modo)
             * - livewire/* (Essencial para o funcionamento do site)
             */
            $isEssentialRoute = $request->is('/', 'login', 'logout', 'admin/*', 'livewire/*');

            // Se não for admin e tentar aceder a qualquer outra página (Dashboard, Finanças, etc.)
            if (!$isAdmin && !$isEssentialRoute) {
                return response()->view('errors.maintenance', [], 503);
            }
        }

        return $next($request);
    }
}

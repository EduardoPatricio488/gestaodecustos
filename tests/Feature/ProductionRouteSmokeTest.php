<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProductionRouteSmokeTest extends TestCase
{
    /**
     * These are critical entry points that must remain registered after
     * refactors, deployments and route/provider changes.
     */
    public function test_critical_application_routes_are_registered(): void
    {
        $paths = [
            '/dashboard',
            '/empresa/pagamentos',
            '/empresa/reconciliacao',
            '/empresa/centros-custo',
            '/empresa/equipa/permissoes',
        ];

        foreach ($paths as $path) {
            $request = Request::create($path, 'GET');
            $route = Route::getRoutes()->match($request);

            $this->assertNotNull($route, "A rota crítica {$path} não está registada.");
        }
    }
}

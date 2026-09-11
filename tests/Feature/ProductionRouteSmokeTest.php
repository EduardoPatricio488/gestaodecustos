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
    private function criticalPaths(): array
    {
        return [
            '/dashboard',
            '/empresa/pagamentos',
            '/empresa/reconciliacao',
            '/empresa/centros-custo',
            '/empresa/equipa/permissoes',
        ];
    }

    public function test_critical_application_routes_are_registered(): void
    {
        foreach ($this->criticalPaths() as $path) {
            $request = Request::create($path, 'GET');
            $route = Route::getRoutes()->match($request);

            $this->assertNotNull($route, "A rota crítica {$path} não está registada.");
        }
    }

    public function test_critical_application_routes_require_authentication(): void
    {
        foreach ($this->criticalPaths() as $path) {
            $response = $this->get($path);

            $this->assertTrue(
                in_array($response->getStatusCode(), [302, 303], true),
                "A rota crítica {$path} deveria exigir autenticação, mas devolveu {$response->getStatusCode()}."
            );
        }
    }
}

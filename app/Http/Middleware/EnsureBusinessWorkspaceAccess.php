<?php

namespace App\Http\Middleware;

use App\Services\BusinessAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessWorkspaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();
        $access = app(BusinessAccessService::class);

        // Gateway e onboarding são acessíveis antes de existir um workspace empresarial ativo.
        if (str_starts_with($path, 'empresa/')) {
            if (in_array($path, ['empresa/acesso', 'empresa/onboarding'], true)) {
                return $next($request);
            }

            $access->assertWorkspace($request->user());
            return $next($request);
        }

        // As atualizações Livewire não passam pelo URI /empresa/...; o nome do componente
        // chega no snapshot. Reaplicamos aqui a fronteira multi-tenant e as permissões
        // mínimas para impedir que uma chamada Livewire manipulada contorne o acesso da página.
        if ($request->is('livewire/update')) {
            foreach ((array) $request->input('components', []) as $component) {
                $snapshot = $component['snapshot'] ?? null;
                if (! is_string($snapshot)) continue;

                $decoded = json_decode($snapshot, true);
                $name = (string) data_get($decoded, 'memo.name', '');
                if (! str_starts_with($name, 'business.')) continue;

                if (in_array($name, ['business.business-gateway', 'business.business-onboarding'], true)) {
                    continue;
                }

                $workspace = $access->assertWorkspace($request->user());
                $permission = match ($name) {
                    'business.team-hub' => 'manage_team',
                    'business.business-settings' => 'manage_settings',
                    'business.business-dashboard',
                    'business.business-pnl-hub',
                    'business.tax-hub' => 'view_financials',
                    'business.invoicing-hub' => 'view_financials',
                    'business.company-expenses' => 'view_business',
                    default => 'view_business',
                };

                $access->assert($permission, $request->user(), $workspace);
            }
        }

        return $next($request);
    }
}

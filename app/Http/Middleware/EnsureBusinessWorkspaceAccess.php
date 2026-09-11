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

        if (str_starts_with($path, 'empresa/')) {
            if (in_array($path, ['empresa/acesso', 'empresa/onboarding'], true)) return $next($request);

            $workspace = $access->assertWorkspace($request->user());
            $routePermission = match (true) {
                str_starts_with($path, 'empresa/equipa/permissoes'), str_starts_with($path, 'empresa/equipa') => 'manage_team',
                str_starts_with($path, 'empresa/perfil') => 'manage_settings',
                str_starts_with($path, 'empresa/ia-estrategista'), str_starts_with($path, 'empresa/resultados'), str_starts_with($path, 'empresa/impostos'),
                str_starts_with($path, 'empresa/faturacao'), str_starts_with($path, 'empresa/fluxo-caixa'), str_starts_with($path, 'empresa/arquivo') => 'view_financials',
                str_starts_with($path, 'empresa/contas') => 'view_bank_accounts',
                default => 'view_business',
            };
            $access->assert($routePermission, $request->user(), $workspace);
            return $next($request);
        }

        if ($request->is('livewire/update')) {
            foreach ((array) $request->input('components', []) as $component) {
                $snapshot = $component['snapshot'] ?? null;
                if (! is_string($snapshot)) continue;

                $decoded = json_decode($snapshot, true);
                $name = (string) data_get($decoded, 'memo.name', '');
                if (! str_starts_with($name, 'business.')) continue;
                if (in_array($name, ['business.business-gateway', 'business.business-onboarding'], true)) continue;

                $workspace = $access->assertWorkspace($request->user());
                $permission = match ($name) {
                    'business.team-hub', 'business.business-roles-hub' => 'manage_team',
                    'business.business-settings' => 'manage_settings',
                    'business.business-dashboard', 'business.business-pnl-hub', 'business.tax-hub', 'business.invoicing-hub', 'business.cash-flow-hub', 'business.business-ai-hub' => 'view_financials',
                    'business.bank-account-hub' => 'view_bank_accounts',
                    'business.company-expenses' => 'view_business',
                    default => 'view_business',
                };

                $access->assert($permission, $request->user(), $workspace);
            }
        }

        return $next($request);
    }
}

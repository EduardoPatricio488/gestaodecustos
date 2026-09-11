<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use RuntimeException;

class ContextEngine
{
    /**
     * Builds the minimum safe context required by the AI Brain.
     * Frontend page context is treated as UX metadata only and never as an authorization source.
     */
    public function build(User $user, array $pageContext = []): array
    {
        $workspace = $this->resolveWorkspace($user);

        $role = $workspace
            ? $user->workspaces()->whereKey($workspace->id)->first()?->pivot?->role
            : null;

        $route = Route::current();
        $serverRouteName = $route?->getName();
        $routeName = $pageContext['route'] ?? $serverRouteName;
        $path = $pageContext['path'] ?? $route?->uri();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'locale' => app()->getLocale(),
            ],
            'workspace' => $workspace ? [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'type' => $workspace->type,
                'currency' => strtoupper((string) ($workspace->currency ?: 'EUR')),
                'role' => $role,
                'plan' => $workspace->plan ?: $user->currentPlanSlug(),
            ] : null,
            'plan' => [
                'slug' => $user->currentPlanSlug(),
                'paid' => $user->isPaidPlan(),
                'business' => $user->isBusinessPlan(),
            ],
            'page' => [
                'route' => $routeName,
                'url' => $path,
                'module' => $pageContext['module'] ?? $this->moduleFromRoute($routeName),
                'params' => $this->safeRouteParams($route?->parameters() ?? []),
                'context' => Arr::only($pageContext, [
                    'module', 'route', 'path', 'entity', 'entity_type', 'action', 'filters', 'period', 'state',
                ]),
            ],
            'time' => [
                'date' => now()->toDateString(),
                'timezone' => config('app.timezone'),
            ],
        ];
    }

    public function resolveWorkspace(User $user): ?Workspace
    {
        $workspaceId = $user->current_workspace_id;

        if (! $workspaceId) {
            return null;
        }

        $workspace = $user->workspaces()->whereKey($workspaceId)->first();

        if (! $workspace) {
            throw new RuntimeException('Current workspace is not accessible by the authenticated user.');
        }

        return $workspace;
    }

    private function safeRouteParams(array $params): array
    {
        $safe = [];

        foreach ($params as $key => $value) {
            if (is_scalar($value) && preg_match('/^(id|slug|category|expense|invoice|client|supplier|project|product|goal|subscription)$/i', (string) $key)) {
                $safe[$key] = (string) $value;
            }
        }

        return $safe;
    }

    private function moduleFromRoute(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        return match (true) {
            str_contains($routeName, 'business') => 'business',
            str_contains($routeName, 'expense') => 'expenses',
            str_contains($routeName, 'income') => 'incomes',
            str_contains($routeName, 'investment') => 'investments',
            str_contains($routeName, 'subscription') => 'subscriptions',
            str_contains($routeName, 'goal') => 'goals',
            str_contains($routeName, 'budget') => 'budget',
            str_contains($routeName, 'store') => 'store',
            str_contains($routeName, 'social') => 'finance-connect',
            str_contains($routeName, 'bank') => 'bank',
            str_contains($routeName, 'report') => 'reports',
            str_contains($routeName, 'ai') || str_contains($routeName, 'insight') => 'ai',
            $routeName === 'dashboard' => 'dashboard',
            default => 'other',
        };
    }
}

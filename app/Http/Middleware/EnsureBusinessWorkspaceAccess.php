<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessWorkspaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) abort(401);

        $workspaceId = $user->current_workspace_id;
        abort_unless($workspaceId, 403);

        $workspace = $user->workspaces()->whereKey($workspaceId)->first();
        abort_unless($workspace, 403);
        abort_unless(in_array($workspace->type, ['business', 'company'], true), 403);

        return $next($request);
    }
}

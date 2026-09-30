<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkspaceContextController extends Controller
{
    public function switch(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $workspace = $user->workspaces()->findOrFail($id);

        $user->forceFill(['current_workspace_id' => $workspace->id])->save();

        if (in_array($workspace->type, ['business', 'company'], true)) {
            session()->put('last_business_workspace_id', $workspace->id);
        }

        return $workspace->type === 'personal'
            ? redirect()->route('dashboard')
            : redirect()->route('hub.business.dashboard');
    }

    public function exitBusiness(Request $request): RedirectResponse
    {
        $user = $request->user();
        $personal = $user->workspaces()->where('type', 'personal')->first();

        if ($personal) {
            $user->forceFill(['current_workspace_id' => $personal->id])->save();
        }

        return redirect()->route('dashboard');
    }

    public function switchFast(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $workspace = $user->workspaces()->findOrFail($id);

        $user->forceFill(['current_workspace_id' => $workspace->id])->save();

        if (in_array($workspace->type, ['business', 'company'], true)) {
            session()->put('last_business_workspace_id', $workspace->id);
        }

        return in_array($workspace->type, ['business', 'company'], true)
            ? redirect()->route('hub.business.dashboard')
            : redirect()->route('dashboard');
    }
}

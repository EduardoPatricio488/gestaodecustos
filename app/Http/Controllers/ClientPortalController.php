<?php

namespace App\Http\Controllers;

use App\Models\ClientPortalToken;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\Project;

class ClientPortalController extends Controller
{
    public function show(string $token)
    {
        $portal = ClientPortalToken::where('token', $token)->firstOrFail();

        if (! $portal->isValid()) {
            abort(403, 'Este link expirou ou foi desativado.');
        }

        $client = $portal->client;
        $workspace = $portal->workspace;

        $invoices = Invoice::where('workspace_id', $workspace->id)
            ->where('client_name', $client->name)
            ->orderByDesc('created_at')
            ->get();

        $proposals = Proposal::where('workspace_id', $workspace->id)
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->get();

        $projects = Project::where('workspace_id', $workspace->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('client-portal', compact('client', 'workspace', 'invoices', 'proposals', 'projects', 'portal'));
    }
}

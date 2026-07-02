<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QuickCommandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $token = config('services.whatsapp.verify_token');
        if ($request->get('hub_verify_token') === $token) {
            return response($request->get('hub_challenge'));
        }

        abort(403);
    }

    public function handle(Request $request, QuickCommandService $commands)
    {
        $entry = $request->input('entry.0.changes.0.value.messages.0');
        if (! $entry) {
            return response('OK');
        }

        $from = $entry['from'] ?? null;
        $text = $entry['text']['body'] ?? '';

        Log::info('WhatsApp message', ['from' => $from, 'text' => $text]);

        // Mapear número → user requer configuração; por agora log apenas
        // Quando WHATSAPP_USER_MAP estiver configurado, processar comando

        return response('OK');
    }
}

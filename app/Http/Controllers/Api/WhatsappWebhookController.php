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
        $configuredToken = (string) config('services.whatsapp.verify_token');
        $providedToken = (string) $request->get('hub_verify_token', '');

        // Never accept verification when the secret is missing or empty.
        if ($configuredToken !== '' && $providedToken !== '' && hash_equals($configuredToken, $providedToken)) {
            return response((string) $request->get('hub_challenge', ''));
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

        // Do not persist phone numbers or message contents in application logs.
        // They are user-provided personal data and are not required for this
        // webhook's current no-op processing path.
        Log::info('WhatsApp webhook message received', [
            'has_sender' => filled($from),
            'has_text' => filled($text),
        ]);

        // Mapear número → user requer configuração; por agora log apenas
        // Quando WHATSAPP_USER_MAP estiver configurado, processar comando

        return response('OK');
    }
}

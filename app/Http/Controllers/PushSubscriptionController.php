<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PushSubscriptionController extends Controller
{
    /**
     * Regista ou atualiza a assinatura Push do telemóvel na base de dados.
     */
    public function update(Request $request)
    {
        // Validação moderna do Laravel
        $request->validate([
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required'
        ]);

        $endpoint = $request->endpoint;
        $key = $request->keys['p256dh'];
        $token = $request->keys['auth'];
        $contentEncoding = $request->content_encoding ?? 'aesgcm';

        // O Trait HasPushSubscriptions no Model User trata de guardar na tabela push_subscriptions
        $request->user()->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

        return response()->json([
            'success' => true,
            'message' => 'Dispositivo registado com sucesso.'
        ]);
    }
}

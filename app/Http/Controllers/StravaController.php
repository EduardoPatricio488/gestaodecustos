<?php

namespace App\Http\Controllers;

use App\Models\ConnectedDevice;
use App\Services\StravaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StravaController extends Controller
{
    public function __construct(private StravaService $strava) {}

    /**
     * Redireciona para a página de autorização do Strava.
     */
    public function connect()
    {
        session(['strava_state' => $state = bin2hex(random_bytes(16))]);

        $url = $this->strava->authUrl() . '&state=' . $state;

        return redirect($url);
    }

    /**
     * Callback do Strava após autorização OAuth.
     */
    public function callback(Request $request)
    {
        // Verifica state CSRF
        if ($request->input('state') !== session('strava_state')) {
            return redirect()->route('hub.fitness')
                ->with('error', 'Autorização Strava inválida (state mismatch).');
        }

        if ($request->has('error')) {
            return redirect()->route('hub.fitness')
                ->with('error', 'Acesso negado pelo utilizador.');
        }

        $code = $request->input('code');
        $data = $this->strava->exchangeCode($code);

        if (empty($data['access_token'])) {
            return redirect()->route('hub.fitness')
                ->with('error', 'Erro ao obter token do Strava.');
        }

        $athlete = $data['athlete'] ?? [];

        // Remove ligação anterior (se existir)
        ConnectedDevice::where('user_id', Auth::id())
            ->where('provider', 'strava')
            ->delete();

        ConnectedDevice::create([
            'user_id'          => Auth::id(),
            'name'             => 'Strava — ' . ($athlete['firstname'] ?? '') . ' ' . ($athlete['lastname'] ?? ''),
            'brand'            => 'Strava',
            'emoji'            => '🚴',
            'provider'         => 'strava',
            'provider_user_id' => (string)($athlete['id'] ?? ''),
            'access_token'     => $data['access_token'],
            'refresh_token'    => $data['refresh_token'],
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 21600),
            'is_active'        => true,
            'meta'             => [
                'athlete_id'   => $athlete['id'] ?? null,
                'username'     => $athlete['username'] ?? null,
                'firstname'    => $athlete['firstname'] ?? null,
                'lastname'     => $athlete['lastname'] ?? null,
                'profile'      => $athlete['profile'] ?? null,
                'city'         => $athlete['city'] ?? null,
                'country'      => $athlete['country'] ?? null,
                'follower_count' => $athlete['follower_count'] ?? 0,
                'friend_count'   => $athlete['friend_count'] ?? 0,
            ],
        ]);

        return redirect()->route('hub.fitness')
            ->with('success', '✅ Strava ligado com sucesso! Os teus dados estão disponíveis.');
    }

    /**
     * Desliga a conta Strava.
     */
    public function disconnect()
    {
        ConnectedDevice::where('user_id', Auth::id())
            ->where('provider', 'strava')
            ->delete();

        return redirect()->route('hub.fitness')
            ->with('success', 'Strava desligado.');
    }
}

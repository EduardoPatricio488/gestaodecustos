<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log; // Adiciona isto

class UpdateLastLogin
{
    public function handle(Login $event): void
    {
        // Isto vai escrever no ficheiro storage/logs/laravel.log
        Log::info('O Listener de Login funcionou para o user: ' . $event->user->email);

        $event->user->forceFill([
            'last_login_at' => now(),
            'last_ip' => request()->ip(),
        ])->save();
    }
}

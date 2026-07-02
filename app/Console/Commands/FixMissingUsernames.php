<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixMissingUsernames extends Command
{
    protected $signature = 'users:fix-usernames';

    protected $description = 'Atribui um username único a qualquer utilizador que ainda não tenha um.';

    public function handle(): int
    {
        $users = User::whereNull('username')->orWhere('username', '')->get();

        if ($users->isEmpty()) {
            $this->info('Todos os utilizadores já têm username. Nada a fazer.');
            return self::SUCCESS;
        }

        foreach ($users as $user) {
            $username = User::generateUniqueUsername($user->name ?? 'user'.$user->id);
            $user->username = $username;
            $user->save();
            $this->line("Utilizador #{$user->id} ({$user->name}) -> @{$username}");
        }

        $this->info("Concluído. {$users->count()} utilizador(es) corrigido(s).");
        return self::SUCCESS;
    }
}

<?php

namespace Database\Seeders;

use App\Models\CommunityChallenge;
use Illuminate\Database\Seeder;

class CommunityChallengeSeeder extends Seeder
{
    public function run(): void
    {
        CommunityChallenge::firstOrCreate(
            ['title' => 'Desafio Poupança '.now()->translatedFormat('F')],
            [
                'description' => 'Poupa mais este mês e partilha o teu progresso com a comunidade!',
                'badge_name' => 'Poupador do Mês',
                'type' => 'savings',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'is_active' => true,
            ]
        );
    }
}

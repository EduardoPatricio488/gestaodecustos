<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Badge::create(['name' => 'Poupador Fiel', 'description' => 'Registou despesas por 7 dias seguidos', 'icon' => '🔥', 'color' => '#f59e0b']);
        Badge::create(['name' => 'Mestre do Orçamento', 'description' => 'Terminou o mês sem exceder limites', 'icon' => '🏆', 'color' => '#10b981']);
        Badge::create(['name' => 'Investidor Estrela', 'description' => 'Adicionou o primeiro ativo financeiro', 'icon' => '💎', 'color' => '#6366f1']);
    }
}

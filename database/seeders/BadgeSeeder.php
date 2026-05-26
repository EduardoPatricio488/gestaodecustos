<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run() {
    \App\Models\Badge::create(['name' => 'Poupador Fiel', 'description' => 'Registou despesas por 7 dias seguidos', 'icon' => '🔥', 'color' => '#f59e0b']);
    \App\Models\Badge::create(['name' => 'Mestre do Orçamento', 'description' => 'Terminou o mês sem exceder limites', 'icon' => '🏆', 'color' => '#10b981']);
    \App\Models\Badge::create(['name' => 'Investidor Estrela', 'description' => 'Adicionou o primeiro ativo financeiro', 'icon' => '💎', 'color' => '#6366f1']);
}
}

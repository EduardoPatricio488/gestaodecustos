<?php

namespace Database\Seeders;

use App\Models\StoreProduct;
use Illuminate\Database\Seeder;

class StoreProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Pack Gestão Business Pro',
                'title' => 'Pack Gestão Business Pro', // 🔥 Adicionado
                'description' => 'Desbloqueia todas as funcionalidades empresariais, faturação ilimitada e gestão de equipa.',
                'price' => 29.90,
                'category' => 'packs',
                'type' => 'module', // 🔥 Importante para o filtro
                'slug' => 'pack-business-pro',
                'image' => '📦', // 🔥 O emoji que aparece no card
                'is_active' => true,
            ],
            [
                'name' => 'Scanner IA de Recibos',
                'title' => 'Scanner IA de Recibos',
                'description' => 'Módulo de leitura automática de faturas com 99% de precisão usando visão computacional.',
                'price' => 14.90,
                'category' => 'ia',
                'type' => 'module',
                'slug' => 'ia-scanner',
                'image' => '📷',
                'is_active' => true,
            ],
            [
                'name' => 'Automação de Categorias',
                'title' => 'Automação de Categorias',
                'description' => 'Deixa o sistema organizar os teus gastos sozinho com base no teu histórico.',
                'price' => 9.90,
                'category' => 'automation',
                'type' => 'module',
                'slug' => 'auto-categorization',
                'image' => '🤖',
                'is_active' => true,
            ],
            [
                'name' => 'Dashboard de Investimentos',
                'title' => 'Dashboard de Investimentos',
                'description' => 'Widget extra para o módulo banco com gráficos de velas e histórico de dividendos.',
                'price' => 19.90,
                'category' => 'widgets',
                'type' => 'module',
                'slug' => 'invest-widget-pro',
                'image' => '📈',
                'is_active' => true,
            ],
        ];

        foreach ($products as $p) {
            // updateOrCreate evita duplicados e garante que as colunas novas são preenchidas
            StoreProduct::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}

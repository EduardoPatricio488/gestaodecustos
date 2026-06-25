<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use Illuminate\Console\Command;

class EnsureFixedCategories extends Command
{
    protected $signature = 'categories:ensure-fixed';

    protected $description = 'Garante que todas as categorias fixas existem na BD para todos os utilizadores';

    protected array $fixed = [
    'alimentacao'    => ['name' => 'Alimentação',   'icon' => 'shopping-cart', 'color' => '#f59e0b', 'order' => 1],
    'carro'          => ['name' => 'Carro',          'icon' => 'truck',         'color' => '#3b82f6', 'order' => 2],
    'casa'           => ['name' => 'Casa',           'icon' => 'home',          'color' => '#10b981', 'order' => 3],
    'educacao'       => ['name' => 'Educação',       'icon' => 'academic-cap',  'color' => '#06b6d4', 'order' => 4],
    'emprestimos'    => ['name' => 'Empréstimos',    'icon' => 'banknotes',     'color' => '#84cc16', 'order' => 5],
    'entretenimento' => ['name' => 'Entretenimento', 'icon' => 'film',          'color' => '#a855f7', 'order' => 6],
    'saude'          => ['name' => 'Saúde',          'icon' => 'heart',         'color' => '#ef4444', 'order' => 7],
    'seguros'        => ['name' => 'Seguros',        'icon' => 'shield-check',  'color' => '#0ea5e9', 'order' => 8],
    'tecnologia'     => ['name' => 'Tecnologia',     'icon' => 'cpu-chip',      'color' => '#6366f1', 'order' => 9],
    'transporte'     => ['name' => 'Transporte',     'icon' => 'bolt',          'color' => '#8b5cf6', 'order' => 10],
];

    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            foreach ($this->fixed as $slug => $data) {
                Category::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'slug'    => $slug,
                    ],
                    [
                        'name'         => $data['name'],
                        'icon'         => $data['icon'],
                        'color'        => $data['color'],
                         'order'        => $data['order'],
                        'is_fixed'     => true,
                        'workspace_id' => $user->currentWorkspace?->id,
                    ]
                );
            }
        }

        $this->info('Categorias fixas garantidas para ' . $users->count() . ' utilizador(es).');
    }
}

<?php
namespace App\Listeners;
use Illuminate\Auth\Events\Registered;
use App\Models\Category;

class CreateDefaultCategories {
    public function handle(Registered $event): void {
        $defaults = [
            ['Casa', '#3b82f6'], ['Carro', '#ef4444'], ['Trabalho', '#8b5cf6'],
            ['Alimentação', '#f59e0b'], ['Saúde', '#10b981'], ['Lazer', '#ec4899'],
            ['Outros', '#64748b'],
        ];
        foreach ($defaults as [$n, $c]) {
            Category::create(['user_id' => $event->user->id, 'name' => $n, 'color' => $c]);
        }
    }
}

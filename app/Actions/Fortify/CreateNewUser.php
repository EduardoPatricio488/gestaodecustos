<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Mail\VerifyAccountMail;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    protected array $fixedCategories = [
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

    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name'              => $input['name'],
            'email'             => $input['email'],
            'password'          => $input['password'],
            'verification_code' => rand(100000, 999999),
        ]);

        Mail::to($user->email)->send(new VerifyAccountMail($user->verification_code));

        // Criar workspace padrão se o teu sistema usa workspaces
        $workspaceId = $user->currentWorkspace?->id;

        foreach ($this->fixedCategories as $slug => $data) {
            Category::firstOrCreate(
                ['user_id' => $user->id, 'slug' => $slug],
                [
                    'name'         => $data['name'],
                    'icon'         => $data['icon'],
                    'color'        => $data['color'],
                    'is_fixed'     => true,
                    'order'        => $data['order'],
                    'workspace_id' => $workspaceId,
                ]
            );
        }

        return $user;
    }
}

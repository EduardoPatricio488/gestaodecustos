<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Workspace, Expense, Project, Task, BankAccount, Category, Client};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. UTILIZADOR ADMIN
        $user = User::updateOrCreate(
            ['email' => 'admin@financepro.com'],
            [
                'name' => 'Eduardo Admin',
                'password' => Hash::make('password'),
                'plan' => 'pro',
                'email_verified_at' => now(),
                'username' => 'eduardoadmin_' . Str::random(4)
            ]
        );

        // 2. WORKSPACE EMPRESARIAL
        $ws = Workspace::updateOrCreate(
            ['owner_id' => $user->id, 'type' => 'business'],
            [
                'name' => 'Finance Pro Systems Lda',
                'invite_code' => 'START2024',
                'plan' => 'pro'
            ]
        );

        // Garantir que o utilizador está ligado ao workspace
        if (!$user->workspaces()->where('workspaces.id', $ws->id)->exists()) {
            $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        }
        $user->update(['current_workspace_id' => $ws->id]);

        // 3. CATEGORIAS BASE
        $categories = [
            ['name' => 'Marketing', 'icon' => 'megaphone', 'color' => '#3b82f6'],
            ['name' => 'Tecnologia', 'icon' => 'cpu-chip', 'color' => '#10b981'],
            ['name' => 'Logística', 'icon' => 'truck', 'color' => '#f59e0b'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['workspace_id' => $ws->id, 'name' => $cat['name']],
                [
                    'user_id' => $user->id,
                    'icon' => $cat['icon'],
                    'color' => $cat['color'],
                    'slug' => Str::slug($cat['name'])
                ]
            );
        }

        $firstCat = Category::where('workspace_id', $ws->id)->first();

        // 4. CONTA BANCÁRIA
        BankAccount::updateOrCreate(
            ['workspace_id' => $ws->id, 'name' => 'Conta Business Millennium'],
            [
                'user_id' => $user->id,
                'bank_name' => 'Millennium BCP',
                'balance' => 25400.00,
                'type' => 'checking',
                'is_business' => true
            ]
        );

        // 5. CLIENTE E PROJETO
        $client = Client::updateOrCreate(
            ['workspace_id' => $ws->id, 'email' => 'geral@cliente.com'],
            ['user_id' => $user->id, 'name' => 'Cliente Internacional SA']
        );

        $project = Project::updateOrCreate(
            ['workspace_id' => $ws->id, 'name' => 'Implementação ERP 2.0'],
            [
                'budget' => 15000,
                'revenue' => 22000,
                'status' => 'em_curso',
                'client_id' => $client->id
            ]
        );

        // 6. GASTOS REAIS (Limpa os antigos para não duplicar valores nos gráficos)
        Expense::where('project_id', $project->id)->delete();

        for ($i = 1; $i <= 12; $i++) {
            Expense::create([
                'workspace_id' => $ws->id,
                'user_id' => $user->id,
                'project_id' => $project->id,
                'description' => 'Serviços Mensais de Consultoria #' . $i,
                'amount' => rand(800, 1200),
                'spent_at' => now()->subMonths($i),
                'category_id' => $firstCat->id,
                'is_company' => true,
                'status' => 'aprovado'
            ]);
        }

        $this->command->info('MySQL Populado! Login: admin@financepro.com / password');
    }
}

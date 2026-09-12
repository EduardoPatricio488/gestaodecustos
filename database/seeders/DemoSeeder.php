<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BusinessDocument;
use App\Models\Category;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! filter_var(env('DEMO_SEED_ENABLED', false), FILTER_VALIDATE_BOOL)) {
            $this->command?->error('DemoSeeder está desactivado. Defina DEMO_SEED_ENABLED=true apenas num ambiente de demonstração controlado.');
            return;
        }

        $adminPassword = env('DEMO_ADMIN_PASSWORD');
        $ceoPassword = env('DEMO_CEO_PASSWORD');
        $memberPassword = env('DEMO_MEMBER_PASSWORD');

        if (! is_string($adminPassword) || strlen($adminPassword) < 12
            || ! is_string($ceoPassword) || strlen($ceoPassword) < 12
            || ! is_string($memberPassword) || strlen($memberPassword) < 12) {
            throw new \RuntimeException('DemoSeeder requer DEMO_ADMIN_PASSWORD, DEMO_CEO_PASSWORD e DEMO_MEMBER_PASSWORD com pelo menos 12 caracteres.');
        }

        // 1. UTILIZADOR ADMINISTRADOR DE DEMONSTRAÇÃO
        $master = User::updateOrCreate(
            ['email' => 'admin@financepro.com'],
            [
                'name' => 'Administrador de Demonstração',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'is_admin' => true,
                'email_verified_at' => now(),
                'username' => 'admin_master',
                'plan' => 'pro',
            ]
        );

        // 2. UTILIZADOR CEO DE DEMONSTRAÇÃO
        $eduardo = User::updateOrCreate(
            ['email' => 'eduardo@financepro.com'],
            [
                'name' => 'Demo Business Owner',
                'password' => Hash::make($ceoPassword),
                'role' => 'user',
                'is_admin' => false,
                'plan' => 'business',
                'email_verified_at' => now(),
                'username' => 'demo_business_owner',
                'xp' => 2500,
                'level' => 12,
            ]
        );

        // 3. UTILIZADOR MEMBRO DE DEMONSTRAÇÃO
        $joao = User::updateOrCreate(
            ['email' => 'joao@financepro.com'],
            [
                'name' => 'Demo Team Member',
                'password' => Hash::make($memberPassword),
                'role' => 'user',
                'email_verified_at' => now(),
                'username' => 'demo_team_member',
            ]
        );

        // 4. CONFIGURAÇÃO DE WORKSPACES
        $personalWs = Workspace::updateOrCreate(
            ['owner_id' => $eduardo->id, 'type' => 'personal'],
            ['name' => 'Demo Personal Workspace', 'plan' => 'pro']
        );
        $eduardo->workspaces()->syncWithoutDetaching([$personalWs->id => ['role' => 'admin']]);

        $businessWs = Workspace::updateOrCreate(
            ['owner_id' => $eduardo->id, 'type' => 'business'],
            [
                'name' => 'Demo Business Workspace',
                'invite_code' => 'DEMO-BUSINESS',
                'plan' => 'business',
                'initial_capital' => 10000.00,
            ]
        );
        $eduardo->workspaces()->syncWithoutDetaching([$businessWs->id => ['role' => 'admin']]);
        $joao->workspaces()->syncWithoutDetaching([$businessWs->id => ['role' => 'member']]);
        $eduardo->update(['current_workspace_id' => $businessWs->id]);

        // 5. CATEGORIAS DE DEMONSTRAÇÃO
        $catData = [
            ['n' => 'Alimentação', 'i' => 'shopping-cart', 'c' => '#ef4444', 't' => 'personal', 'ws' => $personalWs->id],
            ['n' => 'Servidores', 'i' => 'cpu-chip', 'c' => '#10b981', 't' => 'business', 'ws' => $businessWs->id],
            ['n' => 'Marketing', 'i' => 'megaphone', 'c' => '#f59e0b', 't' => 'business', 'ws' => $businessWs->id],
        ];

        foreach ($catData as $cat) {
            Category::updateOrCreate(
                ['workspace_id' => $cat['ws'], 'name' => $cat['n']],
                ['user_id' => $eduardo->id, 'icon' => $cat['i'], 'color' => $cat['c'], 'slug' => Str::slug($cat['n'])]
            );
        }

        // 6. DADOS FINANCEIROS DE DEMONSTRAÇÃO
        for ($m = 0; $m < 3; $m++) {
            $date = Carbon::now()->subMonths($m);

            Income::updateOrCreate(
                ['workspace_id' => $businessWs->id, 'description' => 'Venda de Licença SaaS - '.$date->format('F')],
                ['user_id' => $eduardo->id, 'amount' => rand(4000, 7000), 'received_at' => $date, 'type' => 'business']
            );

            Expense::updateOrCreate(
                ['workspace_id' => $businessWs->id, 'description' => 'Cloud Hosting - '.$date->format('F')],
                [
                    'user_id' => $eduardo->id, 'amount' => rand(300, 600), 'spent_at' => $date,
                    'category_id' => Category::where('name', 'Servidores')->first()->id ?? null,
                    'is_company' => true, 'status' => 'aprovado',
                ]
            );
        }

        // 7. OPERAÇÕES EMPRESARIAIS DE DEMONSTRAÇÃO
        $client = Client::updateOrCreate(
            ['email' => 'demo-client@example.com'],
            ['workspace_id' => $businessWs->id, 'user_id' => $eduardo->id, 'name' => 'Demo Client']
        );

        $project = Project::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'client_id' => $client->id, 'name' => 'Demo Cloud Project'],
            ['budget' => 15000, 'status' => 'em_curso']
        );

        Task::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'project_id' => $project->id, 'title' => 'Configure Stripe Webhook'],
            ['user_id' => $eduardo->id, 'status' => 'pendente']
        );

        BusinessDocument::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'title' => 'Demo Terms Document'],
            ['user_id' => $eduardo->id, 'type' => 'legal', 'file_path' => 'documents/demo.pdf']
        );

        BankAccount::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'name' => 'Demo Business Account'],
            ['user_id' => $eduardo->id, 'bank_name' => 'Demo Bank', 'balance' => 15750.00, 'is_business' => true]
        );

        $this->command?->info('Base de dados de demonstração criada/actualizada.');
        $this->command?->info('As credenciais são as definidas pelas variáveis DEMO_* do ambiente de demonstração.');
    }
}

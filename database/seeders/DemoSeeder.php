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
        // 1. UTILIZADOR ADMINISTRADOR DA PLATAFORMA (O MESTRE)
        // Este utilizador tem acesso à pasta /admin e gere todo o site
        $master = User::updateOrCreate(
            ['email' => 'admin@financepro.com'],
            [
                'name' => 'Administrador do Sistema',
                'password' => Hash::make('password'),
                'role' => 'admin', // 🔥 Status de Administrador Global
                'is_admin' => true,
                'email_verified_at' => now(),
                'username' => 'admin_master',
                'plan' => 'pro',
            ]
        );

        // 2. UTILIZADOR CEO (EDUARDO - PROPRIETÁRIO DE NEGÓCIO)
        $eduardo = User::updateOrCreate(
            ['email' => 'eduardo@financepro.com'],
            [
                'name' => 'Eduardo CEO',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_admin' => false,
                'plan' => 'business',
                'email_verified_at' => now(),
                'username' => 'eduardo_ceo',
                'xp' => 2500,
                'level' => 12,
            ]
        );

        // 3. UTILIZADOR MEMBRO (PARA TESTES DE EQUIPA)
        $joao = User::updateOrCreate(
            ['email' => 'joao@financepro.com'],
            [
                'name' => 'João Membro',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
                'username' => 'joao_member',
            ]
        );

        // 4. CONFIGURAÇÃO DE WORKSPACES
        // Workspace Pessoal do Eduardo
        $personalWs = Workspace::updateOrCreate(
            ['owner_id' => $eduardo->id, 'type' => 'personal'],
            ['name' => 'Cofre do Eduardo', 'plan' => 'pro']
        );
        $eduardo->workspaces()->syncWithoutDetaching([$personalWs->id => ['role' => 'admin']]);

        // Workspace Business (A Empresa que o comprador vai ver)
        $businessWs = Workspace::updateOrCreate(
            ['owner_id' => $eduardo->id, 'type' => 'business'],
            [
                'name' => 'Tech Solutions SaaS',
                'invite_code' => 'BUSINESS2024',
                'plan' => 'business',
                'initial_capital' => 10000.00,
            ]
        );
        $eduardo->workspaces()->syncWithoutDetaching([$businessWs->id => ['role' => 'admin']]);
        $joao->workspaces()->syncWithoutDetaching([$businessWs->id => ['role' => 'member']]);

        // Definir contexto inicial para o Eduardo
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

        // 6. POPULAR DADOS FINANCEIROS (ULTIMOS 3 MESES)
        for ($m = 0; $m < 3; $m++) {
            $date = Carbon::now()->subMonths($m);

            Income::updateOrCreate(
                ['workspace_id' => $businessWs->id, 'description' => 'Venda de Licença SaaS - '.$date->format('F')],
                ['user_id' => $eduardo->id, 'amount' => rand(4000, 7000), 'received_at' => $date, 'type' => 'business']
            );

            Expense::updateOrCreate(
                ['workspace_id' => $businessWs->id, 'description' => 'Cloud Hosting AWS - '.$date->format('F')],
                [
                    'user_id' => $eduardo->id, 'amount' => rand(300, 600), 'spent_at' => $date,
                    'category_id' => Category::where('name', 'Servidores')->first()->id ?? null,
                    'is_company' => true, 'status' => 'aprovado',
                ]
            );
        }

        // 7. OPERAÇÕES EMPRESARIAIS
        $client = Client::updateOrCreate(
            ['email' => 'ads-contact@google.pt'],
            ['workspace_id' => $businessWs->id, 'user_id' => $eduardo->id, 'name' => 'Google Portugal']
        );

        $project = Project::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'client_id' => $client->id, 'name' => 'Expansão Cloud 2024'],
            ['budget' => 15000, 'status' => 'em_curso']
        );

        Task::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'project_id' => $project->id, 'title' => 'Ligar Webhooks Stripe'],
            ['user_id' => $eduardo->id, 'status' => 'pendente']
        );

        BusinessDocument::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'title' => 'Contrato de Termos de Uso'],
            ['user_id' => $eduardo->id, 'type' => 'legal', 'file_path' => 'documents/demo.pdf']
        );

        BankAccount::updateOrCreate(
            ['workspace_id' => $businessWs->id, 'name' => 'Conta Corrente Principal'],
            ['user_id' => $eduardo->id, 'bank_name' => 'Banco Empresa', 'balance' => 15750.00, 'is_business' => true]
        );

        $this->command->info('--------------------------------------------');
        $this->command->info('✅ FINANCE PRO: BASE DE DADOS PRONTA!');
        $this->command->info('--------------------------------------------');
        $this->command->info('1. ACESSO ADMIN MASTER (Site Admin):');
        $this->command->info('   Email: admin@financepro.com / Password: password');
        $this->command->info('--------------------------------------------');
        $this->command->info('2. ACESSO CEO (Para ver o ERP/Dashboard):');
        $this->command->info('   Email: eduardo@financepro.com / Password: password');
        $this->command->info('--------------------------------------------');
    }
}

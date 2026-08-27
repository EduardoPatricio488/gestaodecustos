<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Workspace, Expense, Income, Project, Task, BankAccount, Category, Client, BusinessDocument};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
                'plan' => 'pro'
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
                'plan' => 'pro',
                'email_verified_at' => now(),
                'username' => 'eduardo_ceo',
                'xp' => 2500,
                'level' => 12
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
                'username' => 'joao_member'
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
                'plan' => 'pro',
                'initial_capital' => 10000.00
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

            Income::create([
                'workspace_id' => $businessWs->id, 'user_id' => $eduardo->id,
                'description' => 'Venda de Licença SaaS - ' . $date->format('F'),
                'amount' => rand(4000, 7000), 'received_at' => $date, 'type' => 'business'
            ]);

            Expense::create([
                'workspace_id' => $businessWs->id, 'user_id' => $eduardo->id,
                'description' => 'Cloud Hosting AWS', 'amount' => rand(300, 600), 'spent_at' => $date,
                'category_id' => Category::where('name', 'Servidores')->first()->id ?? null,
                'is_company' => true, 'status' => 'aprovado'
            ]);
        }

        // 7. OPERAÇÕES EMPRESARIAIS
        $client = Client::create([
            'workspace_id' => $businessWs->id, 'user_id' => $eduardo->id,
            'name' => 'Google Portugal', 'email' => 'ads-contact@google.pt'
        ]);

        $project = Project::create([
            'workspace_id' => $businessWs->id, 'client_id' => $client->id,
            'name' => 'Expansão Cloud 2024', 'budget' => 15000, 'status' => 'em_curso'
        ]);

        Task::create([
            'workspace_id' => $businessWs->id, 'project_id' => $project->id, 'user_id' => $eduardo->id,
            'title' => 'Ligar Webhooks Stripe', 'status' => 'pendente'
        ]);

        BusinessDocument::create([
            'workspace_id' => $businessWs->id, 'user_id' => $eduardo->id,
            'title' => 'Contrato de Termos de Uso', 'type' => 'legal', 'file_path' => 'documents/demo.pdf'
        ]);

        BankAccount::create([
            'workspace_id' => $businessWs->id, 'user_id' => $eduardo->id,
            'bank_name' => 'Banco Empresa', 'name' => 'Conta Corrente Principal',
            'balance' => 15750.00, 'is_business' => true
        ]);

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

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BudgetService;
use App\Services\BusinessFinancialMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessMetricsTest extends TestCase
{
    use RefreshDatabase;

    private function workspace(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Empresa', 'owner_id' => $user->id, 'type' => 'business', 'currency' => 'EUR']);
        $workspace->users()->attach($user->id, ['role' => 'admin']);
        $user->update(['current_workspace_id' => $workspace->id]);
        $this->actingAs($user);

        return [$user, $workspace];
    }

    public function test_invoice_total_is_recalculated_from_net_and_vat(): void
    {
        [$user, $workspace] = $this->workspace();

        $invoice = Invoice::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'client_name' => 'Cliente',
            'invoice_number' => 'FT-1',
            'amount_excl_vat' => 100,
            'vat_amount' => 23,
            'total_amount' => 9999,
            'currency' => 'EUR',
            'status' => 'pendente',
        ]);

        $this->assertSame('123.00', $invoice->fresh()->total_amount);
    }

    public function test_business_budget_uses_company_expenses_and_paid_invoice_revenue(): void
    {
        [$user, $workspace] = $this->workspace();
        $category = Category::where('workspace_id', $workspace->id)->firstOrFail();
        $category->update(['budget_limit' => 1000]);

        Expense::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 200,
            'vat_amount' => 46,
            'spent_at' => now()->toDateString(),
            'is_company' => true,
        ]);

        Invoice::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'client_name' => 'Cliente',
            'invoice_number' => 'FT-2',
            'amount_excl_vat' => 500,
            'vat_amount' => 115,
            'currency' => 'EUR',
            'status' => 'paga',
        ]);

        $overview = app(BudgetService::class)->getMonthlyOverview($workspace, now());

        $this->assertSame('business', $overview['context']);
        $this->assertEquals(200.0, $overview['total_spent']);
        $this->assertEquals(500.0, $overview['total_income']);
    }

    public function test_historical_month_does_not_use_current_salary_as_fake_historical_payroll(): void
    {
        [$user, $workspace] = $this->workspace();
        $workspace->employees()->create([
            'name' => 'Colaborador',
            'role' => 'Gestor',
            'salary' => 2000,
            'pay_day' => 25,
            'active' => true,
            'suspended' => false,
        ]);

        $lastMonth = now()->subMonth()->startOfMonth();
        $metrics = app(BusinessFinancialMetrics::class)->forMonth($workspace, $lastMonth);

        $this->assertSame(0.0, $metrics['payroll']);
        $this->assertFalse($metrics['payroll_is_current_run_rate']);
    }
}

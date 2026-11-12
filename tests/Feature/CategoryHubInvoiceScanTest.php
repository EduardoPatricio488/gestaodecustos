<?php

namespace Tests\Feature;

use App\Livewire\CategoryHub;
use App\Livewire\Expenses;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryHubInvoiceScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_hub_opens_prefilled_expense_form_from_scan_cache(): void
    {
        config(['services.openrouter.api_key' => '']);

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $scanId = (string) Str::uuid();
        Cache::put('invoice_scan:'.$user->id.':'.$workspace->id.':'.$scanId, [
            'status' => 'completed',
            'data' => [
                'amount' => 25.50,
                'date' => '2026-11-12',
                'store' => 'Loja Teste',
                'description' => 'Compra de teste',
                'category' => 'Alimentação',
            ],
            'document_path' => null,
            'document_name' => null,
            'document_mime' => null,
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'category_slug' => 'alimentacao',
        ], now()->addMinutes(10));

        Livewire::test(Expenses::class)
            ->set('isScanningInvoice', true)
            ->set('invoiceScanId', $scanId)
            ->call('checkInvoiceScan')
            ->assertRedirect(route('hub.category', [
                'slug' => 'alimentacao',
                'open_scan_review' => 1,
                'scan_id' => $scanId,
            ]));

        session()->forget('pending_expense_scan:'.$workspace->id.':'.$user->id);

        Livewire::withQueryParams([
            'open_scan_review' => 1,
            'scan_id' => $scanId,
        ])->test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->assertSet('openScannedExpenseForm', true)
            ->assertSet('amount', 25.5)
            ->assertSet('spent_at', '2026-11-12')
            ->assertSet('description', 'Compra de teste')
            ->assertDispatched('open-scanned-expense-form')
            ->assertSee('formOpen: true')
            ->assertSee('O que comprei')
            ->assertSee('Confirmar Gasto');
    }

    public function test_category_hub_scanner_keeps_the_current_category_and_uses_its_modal(): void
    {
        config(['services.openrouter.api_key' => '']);

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        Livewire::test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->call('openInvoiceScanner')
            ->assertSet('slug', 'alimentacao')
            ->assertSee('Nova despesa')
            ->assertDontSee('Scanner de fatura')
            ->assertDontSee('Escolher categoria')
            ->assertSee('Alimentação');
    }

    public function test_alimentation_expense_can_be_paid_with_meal_allowance_card(): void
    {
        config(['services.openrouter.api_key' => '']);

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        Livewire::test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->assertSee('Cartão subsídio de alimentação')
            ->set('bankAccountId', 'meal_card')
            ->set('amount', 12.50)
            ->set('spent_at', '2026-11-12')
            ->set('subcategory', 'Supermercado / Hiper')
            ->set('description', 'Compra no cartão de refeição')
            ->call('save')
            ->assertHasNoErrors();

        $expense = \App\Models\Expense::where('workspace_id', $workspace->id)->firstOrFail();
        $this->assertNull($expense->bank_account_id);
        $this->assertSame('meal_card', $expense->metadata['payment_source']);

        Livewire::test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->call('editExpense', $expense->id)
            ->assertSet('bankAccountId', 'meal_card');

        Livewire::withQueryParams(['open_expense' => $expense->id])
            ->test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->assertSet('editingId', $expense->id)
            ->assertSet('amount', 12.5)
            ->assertSet('description', 'Compra no cartão de refeição')
            ->assertSet('bankAccountId', 'meal_card')
            ->assertDispatched('open-add-expense-modal');
    }

    public function test_adding_a_duplicate_continues_to_the_prefilled_form_without_saving(): void
    {
        config(['services.openrouter.api_key' => '']);

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        Livewire::test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->set('amount', 12.50)
            ->set('spent_at', '2026-11-12')
            ->set('description', 'Compra de teste')
            ->set('subcategory', 'Supermercado / Hiper')
            ->set('duplicateExpenseWarning', [
                'id' => 42,
                'product' => 'Produto de teste',
                'amount' => 2.50,
                'date' => '2026-11-12',
            ])
            ->assertSee('Possível despesa duplicada')
            ->assertSee('Produto de teste')
            ->call('confirmDuplicateExpense')
            ->assertSet('duplicateExpenseWarning', null)
            ->assertSet('openScannedExpenseForm', true)
            ->assertSet('allowDuplicateExpense', true)
            ->assertSet('amount', 12.50)
            ->assertSet('description', 'Compra de teste')
            ->assertDispatched('open-scanned-expense-form')
            ->assertSee('formOpen: true')
            ->assertSee('Confirmar Gasto');

        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_category_hub_queues_free_scan_for_the_current_category(): void
    {
        config(['services.openrouter.api_key' => 'test-openrouter-key']);
        Http::fake([
            'https://openrouter.ai/api/v1/key' => Http::response([
                'data' => [
                    'free_model_daily_requests' => [
                        'used' => 10,
                        'limit' => 50,
                        'remaining' => 40,
                    ],
                ],
            ]),
        ]);
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        Livewire::test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->set('scannerReceipt', UploadedFile::fake()->create('fatura.pdf', 10, 'application/pdf'))
            ->call('scanInvoiceForCategory')
            ->assertSet('isScanning', true)
            ->assertSet('slug', 'alimentacao');

        Queue::assertPushed(\App\Jobs\ProcessInvoiceScan::class, function ($job) {
            return $job->optionField === 'subcategory'
                && in_array('Supermercado / Hiper', $job->options, true);
        });
    }

    public function test_completed_category_scan_returns_to_the_same_category_for_review(): void
    {
        config(['services.openrouter.api_key' => '']);

        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $user->forceFill(['current_workspace_id' => $workspace->id])->save();
        $this->actingAs($user);

        $scanId = (string) Str::uuid();
        Cache::put('invoice_scan:'.$user->id.':'.$workspace->id.':'.$scanId, [
            'status' => 'completed',
            'data' => ['amount' => 12.50, 'date' => '2026-11-12', 'description' => 'Compra'],
            'document_path' => 'expenses/pending-scans/fatura.pdf',
            'document_name' => 'fatura.pdf',
            'document_mime' => 'application/pdf',
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
        ], now()->addMinutes(10));

        Livewire::test(CategoryHub::class, ['slug' => 'alimentacao'])
            ->set('isScanning', true)
            ->set('invoiceScanId', $scanId)
            ->call('checkCategoryInvoiceScan')
            ->assertRedirect(route('hub.category', [
                'slug' => 'alimentacao',
                'open_scan_review' => 1,
                'scan_id' => $scanId,
            ]));

    }
}

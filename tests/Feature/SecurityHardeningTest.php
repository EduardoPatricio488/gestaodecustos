<?php

namespace Tests\Feature;

use App\Livewire\ClientPortal;
use App\Livewire\Public\SupplierDashboard;
use App\Livewire\Public\SupplierPortal;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_sensitive_admin_routes_require_full_admin_role(): void
    {
        foreach ([
            'admin.dashboard',
            'admin.stats',
            'admin.productivity',
            'admin.reminders',
            'admin.logs',
            'admin.ai',
        ] as $name) {
            $this->assertContains('admin.only', Route::getRoutes()->getByName($name)->gatherMiddleware());
        }
    }

    public function test_business_management_routes_require_verified_email(): void
    {
        foreach ([
            'hub.business.roles',
            'hub.business.settlements',
            'hub.business.reconciliation',
            'hub.business.cost-centers',
        ] as $name) {
            $this->assertContains('verified', Route::getRoutes()->getByName($name)->gatherMiddleware());
        }
    }

    public function test_supplier_invoice_submission_requires_authenticated_supplier_portal(): void
    {
        Livewire::test(SupplierPortal::class)
            ->call('submitInvoice')
            ->assertForbidden();
    }

    public function test_client_portal_requires_an_authenticated_portal_session(): void
    {
        Livewire::test(ClientPortal::class)
            ->assertUnauthorized();
    }

    public function test_supplier_dashboard_requires_an_authenticated_portal_session(): void
    {
        Livewire::test(SupplierDashboard::class)
            ->assertUnauthorized();
    }

    public function test_client_portal_tokens_are_resolved_from_sha256_hashes(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Workspace Cliente',
            'type' => 'business',
            'owner_id' => $owner->id,
        ]);

        $token = bin2hex(random_bytes(32));

        $client = Client::create([
            'user_id' => $owner->id,
            'workspace_id' => $workspace->id,
            'name' => 'Cliente Seguro',
            'portal_token_hash' => hash('sha256', $token),
        ]);

        $this->assertSame($client->id, Client::findByPortalToken($token)?->id);
        $this->assertNull(Client::findByPortalToken('token-invalido'));
        $this->assertNull(Client::findByPortalToken(substr($token, 0, 63)));
    }

    public function test_supplier_portal_tokens_are_resolved_from_sha256_hashes(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Workspace Fornecedor',
            'type' => 'business',
            'owner_id' => $owner->id,
        ]);

        $token = bin2hex(random_bytes(32));

        $supplier = Supplier::create([
            'user_id' => $owner->id,
            'workspace_id' => $workspace->id,
            'name' => 'Fornecedor Seguro',
            'portal_token_hash' => hash('sha256', $token),
        ]);

        $this->assertSame($supplier->id, Supplier::findByPortalToken($token)?->id);
        $this->assertNull(Supplier::findByPortalToken('token-invalido'));
        $this->assertNull(Supplier::findByPortalToken(substr($token, 0, 63)));
    }

    public function test_legacy_plaintext_portal_token_is_not_mass_assignable(): void
    {
        $this->assertNotContains('portal_token', (new Client)->getFillable());
        $this->assertNotContains('portal_token', (new Supplier)->getFillable());
        $this->assertContains('portal_token_hash', (new Client)->getFillable());
        $this->assertContains('portal_token_hash', (new Supplier)->getFillable());
    }

    public function test_security_headers_include_content_security_policy(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Content-Security-Policy');
    }
}

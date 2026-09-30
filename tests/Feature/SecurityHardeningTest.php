<?php

namespace Tests\Feature;

use App\Livewire\Business\TeamHub;
use App\Livewire\ClientPortal;
use App\Livewire\Public\SupplierDashboard;
use App\Livewire\Public\SupplierPortal;
use App\Models\AppNotification;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
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
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "A rota {$name} não está registada.");
            $this->assertContains('admin.only', $route->gatherMiddleware());
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
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "A rota {$name} não está registada.");
            $this->assertContains('verified', $route->gatherMiddleware());
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

    public function test_plaintext_bank_audit_access_code_column_is_removed(): void
    {
        $this->assertFalse(Schema::hasColumn('workspaces', 'audit_access_code'));
        $this->assertContains('audit_token', (new Workspace)->getFillable());
        $this->assertNotContains('audit_access_code', (new Workspace)->getFillable());
    }

    public function test_bank_audit_token_storage_is_hashed_and_expiring(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Workspace Banco Seguro',
            'type' => 'business',
            'owner_id' => $owner->id,
            'audit_token' => hash('sha256', 'placeholder'),
            'audit_token_expires_at' => now()->addDays(30),
            'audit_token_purpose' => 'bank_audit',
        ]);

        $this->assertNotSame('BancoToken1234567890', $workspace->audit_token);
        $this->assertNotNull($workspace->audit_token_expires_at);
        $this->assertSame('bank_audit', $workspace->audit_token_purpose);
    }

    public function test_security_headers_include_content_security_policy(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_logout_invalidates_the_session(): void
    {
        $user = User::factory()->create();

        $this->withSession(['sensitive_context' => 'should-be-cleared'])
            ->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertFalse(session()->has('sensitive_context'));
    }

    public function test_team_raise_modal_cannot_read_salary_from_another_workspace(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Empresa A',
            'type' => 'business',
            'owner_id' => $owner->id,
            'currency' => 'EUR',
        ]);
        $workspace->users()->attach($owner->id, ['role' => 'admin']);
        $owner->forceFill(['current_workspace_id' => $workspace->id])->save();

        $foreignOwner = User::factory()->create();
        $foreignWorkspace = Workspace::create([
            'name' => 'Empresa B',
            'type' => 'business',
            'owner_id' => $foreignOwner->id,
            'currency' => 'EUR',
        ]);
        $foreignWorkspace->users()->attach($foreignOwner->id, ['role' => 'admin']);

        $foreignEmployee = Employee::create([
            'workspace_id' => $foreignWorkspace->id,
            'name' => 'Colaborador Privado',
            'role' => 'Diretor',
            'salary' => 9876.54,
            'pay_day' => 25,
            'active' => true,
        ]);

        Livewire::actingAs($owner)
            ->test(TeamHub::class)
            ->set('raiseEmployeeId', $foreignEmployee->id)
            ->set('raiseAmount', 0)
            ->assertDontSee('9 876,54');
    }

    public function test_guests_are_redirected_from_admin_routes(): void
    {
        foreach (['admin.dashboard', 'admin.stats', 'admin.logs', 'admin.ai'] as $name) {
            $this->get(route($name))->assertRedirect();
        }
    }

    public function test_regular_users_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();

        foreach (['admin.dashboard', 'admin.stats', 'admin.logs', 'admin.ai'] as $name) {
            $this->actingAs($user)->get(route($name))->assertForbidden();
        }
    }

    public function test_unverified_users_cannot_access_business_management_routes(): void
    {
        $user = User::factory()->unverified()->create();

        foreach (['hub.business.roles', 'hub.business.settlements'] as $name) {
            $this->actingAs($user)
                ->get(route($name))
                ->assertForbidden();
        }
    }

    public function test_user_sensitive_fields_are_not_mass_assignable(): void
    {
        $fillable = (new User)->getFillable();

        foreach (['is_admin', 'role', 'email_verified_at', 'current_workspace_id'] as $field) {
            $this->assertNotContains($field, $fillable, "{$field} não devia ser mass-assignable");
        }
    }

    public function test_user_serialization_hides_secrets(): void
    {
        $array = User::factory()->create()->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_client_serialization_hides_portal_token_hash(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Workspace',
            'type' => 'business',
            'owner_id' => $owner->id,
        ]);

        $client = Client::create([
            'user_id' => $owner->id,
            'workspace_id' => $workspace->id,
            'name' => 'Cliente',
            'portal_token_hash' => hash('sha256', 'token'),
        ]);

        $this->assertArrayNotHasKey('portal_token_hash', $client->toArray());
    }

    public function test_session_cookie_is_http_only_and_same_site(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertContains(config('session.same_site'), ['lax', 'strict']);
    }

    public function test_debug_is_disabled_by_default_in_env_example(): void
    {
        $env = file_get_contents(base_path('.env.example'));

        $this->assertMatchesRegularExpression('/^APP_DEBUG=false$/m', $env);
    }

   public function test_additional_security_headers_are_present(): void
{
    $response = $this->get('/');

    $response->assertHeader('Referrer-Policy');
    $response->assertHeader('Permissions-Policy');

    $csp = $response->headers->get('Content-Security-Policy');
    $this->assertStringContainsString("object-src 'none'", $csp);
    $this->assertStringContainsString("frame-ancestors 'self'", $csp);
    $this->assertStringContainsString("base-uri 'self'", $csp);
}

    public function test_notification_links_cannot_redirect_to_external_hosts(): void
    {
        $user = User::factory()->create();

        $notification = AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Notificação maliciosa',
            'message' => 'Teste',
            'type' => 'danger',
            'link' => 'https://evil.example/phishing',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\NotificationCenter::class)
            ->call('readAndNavigate', $notification->id)
            ->assertNoRedirect();
    }

    public function test_notification_links_can_only_use_internal_paths(): void
    {
        $user = User::factory()->create();

        $notification = AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Notificação interna',
            'message' => 'Teste',
            'type' => 'info',
            'link' => '/dashboard?from=notification',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\NotificationCenter::class)
            ->call('readAndNavigate', $notification->id)
            ->assertRedirect('/dashboard?from=notification');
    }
}

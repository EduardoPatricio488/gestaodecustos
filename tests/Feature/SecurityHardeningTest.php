<?php

namespace Tests\Feature;

use App\Livewire\Public\SupplierPortal;
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

    public function test_security_headers_include_content_security_policy(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Content-Security-Policy');
    }
}

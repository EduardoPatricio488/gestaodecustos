<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase; // RefreshDatabase limpa a BD em cada teste

    protected function setUp(): void
    {
        parent::setUp();

        // Ignora os middlewares que dependem de dados na BD
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\CheckRegistrationStatus::class,
            \App\Http\Middleware\AdminMiddleware::class,
        ]);
    }
}

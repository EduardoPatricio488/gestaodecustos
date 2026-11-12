<?php

namespace Tests\Unit;

use App\Services\InvoiceScanService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InvoiceScanServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.openrouter.api_key' => 'test-openrouter-key',
            'services.openrouter.requests_per_minute' => 18,
            'services.openrouter.connect_timeout' => 1,
            'services.openrouter.request_timeout' => 2,
        ]);
    }

    public function test_it_uses_only_openrouter_free_and_normalises_a_valid_invoice(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://openrouter.ai/api/v1/key' => Http::response([
                'data' => [
                    'free_model_daily_requests' => [
                        'used' => 2,
                        'limit' => 50,
                        'remaining' => 48,
                    ],
                ],
            ]),
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => [
                        'content' => json_encode([
                            'amount' => '23,45',
                            'date' => '2026-11-12',
                            'store' => 'Continente',
                            'description' => 'Compras',
                        ], JSON_UNESCAPED_UNICODE),
                    ],
                ]],
            ]),
        ]);

        $path = tempnam(sys_get_temp_dir(), 'invoice-scan-');
        file_put_contents($path, 'fake image bytes');

        try {
            $result = app(InvoiceScanService::class)->analyse(
                $path,
                'fatura.jpg',
                'image/jpeg'
            );
        } finally {
            @unlink($path);
        }

        $this->assertSame(23.45, $result['amount']);
        $this->assertSame('2026-11-12', $result['date']);
        $this->assertSame('Continente', $result['store']);

        Http::assertSent(fn (Request $request) =>
            $request->url() === 'https://openrouter.ai/api/v1/chat/completions'
            && $request['model'] === 'openrouter/free'
        );
    }

    public function test_it_rejects_an_unreadable_or_incomplete_model_response(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://openrouter.ai/api/v1/key' => Http::response([
                'data' => [
                    'free_model_daily_requests' => [
                        'used' => 0,
                        'limit' => 50,
                        'remaining' => 50,
                    ],
                ],
            ]),
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"amount": null,'],
                ]],
            ]),
        ]);

        $path = tempnam(sys_get_temp_dir(), 'invoice-scan-');
        file_put_contents($path, 'fake image bytes');

        try {
            $this->expectException(\RuntimeException::class);
            app(InvoiceScanService::class)->analyse($path, 'fatura.jpg', 'image/jpeg');
        } finally {
            @unlink($path);
        }
    }
}

<?php

use App\Services\InvoiceScanService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    config(['services.openrouter.api_key' => 'test-openrouter-key']);
    RateLimiter::clear('openrouter:invoice-scans:global');
});

test('invoice scanner uses only openrouter free and returns normalised data', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 2, 'limit' => 50, 'remaining' => 48]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [[
                'finish_reason' => 'stop',
                'message' => ['content' => json_encode([
                    'amount' => '1.234,56',
                    'date' => '12/11/2026',
                    'store' => 'Loja de Teste',
                    'description' => 'Compra de teste',
                    'category' => 'Alimentação',
                ])],
            ]],
        ]),
    ]);

    $result = app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
        [['name' => 'Alimentação', 'slug' => 'alimentacao']],
        'category',
    );

    expect($result['amount'])->toBe(1234.56)
        ->and($result['date'])->toBe('2026-11-12')
        ->and($result['category'])->toBe('Alimentação');

    Http::assertSent(fn (Request $request) =>
        str_ends_with($request->url(), '/chat/completions')
        && $request['model'] === 'openrouter/free'
    );
    Http::assertSentCount(2);
});

test('invoice scanner rejects an unreadable response without retrying', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [[
                'finish_reason' => 'stop',
                'message' => ['content' => '{"amount":null,"date":null,"store":null,"description":null}'],
            ]],
        ]),
    ]);

    expect(fn () => app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    ))->toThrow(RuntimeException::class);

    Http::assertSentCount(2);
});

test('invoice scanner enables the pdf parser for pdf files', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [[
                'finish_reason' => 'stop',
                'message' => [
                    'content' => '{"amount":25.50,"date":"2026-11-12","store":"Teste PDF"}',
                    'annotations' => [[
                        'type' => 'file',
                        'file' => ['content' => [['type' => 'text', 'text' => 'Fatura de teste, total 25.50 EUR.']]],
                    ]],
                ],
            ]],
        ]),
    ]);

    app(InvoiceScanService::class)->analyse(
        '%PDF-1.4 fake pdf bytes',
        'fatura.pdf',
        'application/pdf',
    );

    Http::assertSent(fn (Request $request) =>
        str_ends_with($request->url(), '/chat/completions')
        && $request['model'] === 'openrouter/free'
        && data_get($request->data(), 'plugins.0.id') === 'file-parser'
        && data_get($request->data(), 'plugins.0.pdf.engine') === 'cloudflare-ai'
    );
});


test('invoice scanner refuses pdfs that the free parser could not read', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [[
                'finish_reason' => 'stop',
                'message' => [
                    'content' => '{"amount":25.50,"date":"2026-11-12","store":"Inventado pelo modelo"}',
                    'annotations' => [],
                ],
            ]],
        ]),
    ]);

    expect(fn () => app(InvoiceScanService::class)->analyse(
        '%PDF-1.4 scanned fake pdf',
        'fatura-digitalizada.pdf',
        'application/pdf',
    ))->toThrow(RuntimeException::class, 'parser gratuito');

    Http::assertSentCount(2);
});

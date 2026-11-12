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
        && data_get($request->data(), 'provider.allow_fallbacks') === true
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && $request['max_tokens'] === 1200
        && ! array_key_exists('reasoning', $request->data())
        && ! array_key_exists('response_format', $request->data())
        && ! array_key_exists('models', $request->data())
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


test('invoice scanner reports free-router capacity errors without retrying or paid fallback', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'error' => ['message' => 'Provider temporarily rate-limited upstream.'],
        ], 429),
    ]);

    expect(fn () => app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    ))->toThrow(RuntimeException::class, 'temporariamente indisponível');

    Http::assertSent(fn (Request $request) =>
        str_ends_with($request->url(), '/chat/completions')
        && $request['model'] === 'openrouter/free'
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && ! array_key_exists('models', $request->data())
    );
    Http::assertSentCount(2);
});

test('invoice scanner extracts selectable PDF text locally without a hosted parser', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML(
        '<html><body>Fatura de teste. Total pago 25.50 EUR. Loja Teste. Data 12/11/2026.</body></html>'
    )->output();

    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [[
                'finish_reason' => 'stop',
                'message' => ['content' => '{"amount":25.50,"date":"2026-11-12","store":"Teste PDF"}'],
            ]],
        ]),
    ]);

    app(InvoiceScanService::class)->analyse($pdf, 'fatura.pdf', 'application/pdf');

    Http::assertSent(fn (Request $request) =>
        str_ends_with($request->url(), '/chat/completions')
        && $request['model'] === 'openrouter/free'
        && ! array_key_exists('plugins', $request->data())
        && str_contains((string) data_get($request->data(), 'messages.0.content.1.text'), 'Fatura de teste')
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
    );
});

test('invoice scanner rejects scanned or unreadable PDFs locally without external parsing', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
    ]);

    expect(fn () => app(InvoiceScanService::class)->analyse(
        '%PDF-1.4 scanned fake pdf without selectable text',
        'fatura-digitalizada.pdf',
        'application/pdf',
    ))->toThrow(RuntimeException::class, 'texto selecionável');

    Http::assertNotSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions'));
});

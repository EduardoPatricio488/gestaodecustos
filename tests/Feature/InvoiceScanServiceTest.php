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

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && in_array($request['model'], ['nvidia/nemotron-3.5-super-vl:free', 'google/gemma-4-31b-it:free', 'openrouter/free'], true)
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && data_get($request->data(), 'provider.max_price.image') === 0
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

test('invoice scanner extracts selectable pdf text locally without a hosted parser', function () {
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
                'message' => [
                    'content' => '{"amount":25.50,"date":"2026-11-12","store":"Teste PDF"}',
                ],
            ]],
        ]),
    ]);

    app(InvoiceScanService::class)->analyse(
        $pdf,
        'fatura.pdf',
        'application/pdf',
    );

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && in_array($request['model'], ['nvidia/nemotron-3.5-super-vl:free', 'google/gemma-4-31b-it:free', 'openrouter/free'], true)
        && ! array_key_exists('plugins', $request->data())
        && str_contains((string) data_get($request->data(), 'messages.0.content.1.text'), 'Fatura de teste')
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && data_get($request->data(), 'provider.max_price.image') === 0
    );
});

test('invoice scanner rejects scanned pdfs locally without calling OpenRouter', function () {
    Http::fake();

    expect(fn () => app(InvoiceScanService::class)->analyse(
        '%PDF-1.4 scanned fake pdf',
        'fatura-digitalizada.pdf',
        'application/pdf',
    ))->toThrow(RuntimeException::class, 'texto selecionável');

    Http::assertNothingSent();
});

test('invoice scanner uses a zero-cost fallback after a free route is limited', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['error' => ['message' => 'Provider temporarily rate-limited upstream.']], 429)
            ->push([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"amount":12.5,"date":"2026-11-12","store":"Loja Teste"}'],
                ]],
            ]),
    ]);

    $result = app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    );

    expect($result['amount'])->toBe(12.5);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && in_array($request['model'], ['nvidia/nemotron-3.5-super-vl:free', 'google/gemma-4-31b-it:free', 'openrouter/free'], true)
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && data_get($request->data(), 'provider.max_price.image') === 0
    );
    Http::assertSentCount(3);
});

test('invoice scanner uses a zero-cost fallback after a free route times out', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->pushFailedConnection('Provider timed out.')
            ->push([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"amount":12.5,"date":"2026-11-12","store":"Loja Teste"}'],
                ]],
            ]),
    ]);

    $result = app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    );

    expect($result['amount'])->toBe(12.5);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && in_array($request['model'], ['nvidia/nemotron-3.5-super-vl:free', 'google/gemma-4-31b-it:free', 'openrouter/free'], true)
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && data_get($request->data(), 'provider.max_price.image') === 0
    );
    Http::assertSentCount(3);
});

test('invoice scanner falls back when a free model has no permitted endpoint', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['error' => ['message' => 'No endpoint allowed by account settings.']], 404)
            ->push([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"amount":12.5,"date":"2026-11-12","store":"Loja Teste"}'],
                ]],
            ]),
    ]);

    $result = app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    );

    expect($result['amount'])->toBe(12.5);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && in_array($request['model'], ['nvidia/nemotron-3.5-super-vl:free', 'google/gemma-4-31b-it:free', 'openrouter/free'], true)
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && data_get($request->data(), 'provider.max_price.image') === 0
    );
    Http::assertSentCount(3);
});

test('invoice scanner retries another free model when a response is not valid JSON', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => 'I cannot provide valid JSON.'],
                ]],
            ])
            ->push([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"amount":12.5,"date":"2026-11-12","store":"Loja Teste"}'],
                ]],
            ]),
    ]);

    $result = app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    );

    expect($result['amount'])->toBe(12.5);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && in_array($request['model'], ['google/gemma-4-31b-it:free', 'nvidia/nemotron-3.5-super-vl:free', 'openrouter/free'], true)
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && data_get($request->data(), 'provider.max_price.image') === 0
    );
    Http::assertSentCount(3);
});

test('invoice scanner retries another free model when a response is truncated', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push([
                'choices' => [[
                    'finish_reason' => 'length',
                    'message' => ['content' => null],
                ]],
            ])
            ->push([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"amount":12.5,"date":"2026-11-12","store":"Loja Teste"}'],
                ]],
            ]),
    ]);

    $result = app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    );

    expect($result['amount'])->toBe(12.5);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && in_array($request['model'], ['google/gemma-4-31b-it:free', 'nvidia/nemotron-3.5-super-vl:free', 'openrouter/free'], true)
        && data_get($request->data(), 'provider.max_price.prompt') === 0
        && data_get($request->data(), 'provider.max_price.completion') === 0
        && data_get($request->data(), 'provider.max_price.image') === 0
    );
    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && $request['model'] === 'nvidia/nemotron-3.5-super-vl:free'
        && data_get($request->data(), 'reasoning.enabled') === false
    );
    Http::assertSentCount(3);
});

test('invoice scanner accepts complete JSON when the provider marks the response truncated', function () {
    Http::fake([
        'https://openrouter.ai/api/v1/key' => Http::response([
            'data' => ['free_model_daily_requests' => ['used' => 0, 'limit' => 50, 'remaining' => 50]],
        ]),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [[
                'finish_reason' => 'length',
                'message' => ['content' => '{"amount":12.5,"date":"2026-11-12","store":"Loja Teste"}'],
            ]],
        ]),
    ]);

    $result = app(InvoiceScanService::class)->analyse(
        'fake image bytes',
        'fatura.jpg',
        'image/jpeg',
    );

    expect($result['amount'])->toBe(12.5);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/chat/completions')
        && data_get($request->data(), 'max_tokens') === 4000
    );
    Http::assertSentCount(2);
});

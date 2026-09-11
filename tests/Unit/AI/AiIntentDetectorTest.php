<?php

use App\Services\AI\AiIntentDetector;

test('detects financial summary intent', function () {
    $result = app(AiIntentDetector::class)->detect('Como está a minha situação financeira?');

    expect($result['intent'])->toBe(AiIntentDetector::FINANCIAL_SUMMARY)
        ->and($result['confidence'])->toBeGreaterThanOrEqual(80);
});

test('detects transaction creation intent', function () {
    $result = app(AiIntentDetector::class)->detect('Regista uma despesa de 25 euros em alimentação');

    expect($result['intent'])->toBe(AiIntentDetector::TRANSACTION_CREATION);
});

test('detects comparison and maps it to a deterministic backend tool', function () {
    $detector = app(AiIntentDetector::class);
    $intent = $detector->detect('Compara os meus gastos deste mês com o mês passado');

    expect($intent['intent'])->toBe(AiIntentDetector::COMPARISON)
        ->and($detector->toolFor($intent))->toBe('get_financial_snapshot');
});

test('does not treat normal conversation as a financial tool request', function () {
    $detector = app(AiIntentDetector::class);
    $intent = $detector->detect('Olá, tudo bem?');

    expect($intent['intent'])->toBe(AiIntentDetector::GENERAL_CHAT)
        ->and($detector->toolFor($intent))->toBeNull();
});

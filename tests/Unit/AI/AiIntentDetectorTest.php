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

test('routes budget forecast and saving analysis to the deterministic financial snapshot', function () {
    $detector = app(AiIntentDetector::class);

    foreach ([
        ['Quanto posso gastar este mês?', AiIntentDetector::BUDGET_ANALYSIS],
        ['Se continuar assim quanto vou gastar até ao fim do mês?', AiIntentDetector::FORECAST],
        ['Onde posso poupar este mês?', AiIntentDetector::SAVING_ADVICE],
    ] as [$question, $expectedIntent]) {
        $intent = $detector->detect($question);

        expect($intent['intent'])->toBe($expectedIntent)
            ->and($detector->toolFor($intent))->toBe('get_financial_snapshot');
    }
});

test('transaction update is read first and does not select a write tool', function () {
    $detector = app(AiIntentDetector::class);
    $intent = $detector->detect('Altera a despesa do supermercado');

    expect($intent['intent'])->toBe(AiIntentDetector::TRANSACTION_UPDATE)
        ->and($detector->toolFor($intent))->toBe('list_expenses');
});

test('does not treat normal conversation as a financial tool request', function () {
    $detector = app(AiIntentDetector::class);
    $intent = $detector->detect('Olá, tudo bem?');

    expect($intent['intent'])->toBe(AiIntentDetector::GENERAL_CHAT)
        ->and($detector->toolFor($intent))->toBeNull();
});

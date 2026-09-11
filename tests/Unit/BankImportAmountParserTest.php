<?php

use App\Services\BankImportService;

it('parses common monetary formats without changing magnitude', function (string $input, float $expected) {
    $service = new BankImportService();
    $method = new \ReflectionMethod($service, 'parseAmount');
    $method->setAccessible(true);

    expect($method->invoke($service, $input))->toBe($expected);
})->with([
    ['10.50', 10.50],
    ['10,50', 10.50],
    ['1.234,56', 1234.56],
    ['1,234.56', 1234.56],
    ['1234.56', 1234.56],
    ['1234,56', 1234.56],
    ['-10,50', -10.50],
    ['(10.50)', -10.50],
    ['€ 10,50', 10.50],
]);

it('returns null for non monetary text instead of guessing', function () {
    $service = new BankImportService();
    $method = new \ReflectionMethod($service, 'parseAmount');
    $method->setAccessible(true);

    expect($method->invoke($service, 'não é um valor'))->toBeNull();
});

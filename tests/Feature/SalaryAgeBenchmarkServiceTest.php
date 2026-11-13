<?php

use App\Models\SalaryPayment;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AI\SalaryAgeBenchmarkService;
use Illuminate\Support\Carbon;

test('salary benchmark uses work country and age with a qualified gross comparison', function () {
    Carbon::setTestNow('2026-11-13');
    $user = new User(['date_of_birth' => '1990-01-15', 'country_of_origin' => 'Brasil', 'work_country' => 'PT']);
    $workspace = new Workspace(['currency' => 'EUR']);
    $payment = new SalaryPayment(['salary_base_gross' => 1800, 'net_received' => 1300]);

    $result = app(SalaryAgeBenchmarkService::class)->forReport($user, $workspace, collect([$payment]));

    expect($result['work_country'])->toBe('Portugal')
        ->and($result['age_group'])->toBe('30 a 49 anos')
        ->and($result['mean_monthly_gross_eur'])->toBe(1476)
        ->and($result['recorded_monthly_base_gross'])->toBe(1800.0)
        ->and($result['base_minus_historical_mean_eur'])->toBe(324.0)
        ->and($result['scope'])->toContain('2022')
        ->and($result['limitations'])->toContain('líquido');

    Carbon::setTestNow();
});

test('salary benchmark handles unavailable data and incomparable currencies', function () {
    $service = app(SalaryAgeBenchmarkService::class);
    $user = new User(['date_of_birth' => '1990-01-15', 'work_country' => 'BR']);
    $payment = new SalaryPayment(['salary_base_gross' => 1800]);
    $workspace = new Workspace(['currency' => 'BRL']);

    $result = $service->forReport($user, $workspace, collect([$payment]));
    expect($result['mean_monthly_gross_eur'])->toBeNull()
        ->and($result['availability'])->toContain('Não há referência')
        ->and($result)->not->toHaveKey('base_minus_historical_mean_eur');

    $user->work_country = 'PT';
    $result = $service->forReport($user, $workspace, collect([$payment]));
    expect($result['mean_monthly_gross_eur'])->toBe(1476)
        ->and($result)->not->toHaveKey('base_minus_historical_mean_eur');

    $result = $service->forReport($user, $workspace, collect([$payment, $payment]));
    expect($result)->not->toHaveKey('recorded_monthly_base_gross');

    $user->date_of_birth = null;
    $result = $service->forReport($user, $workspace, collect());
    expect($result['mean_monthly_gross_eur'])->toBeNull()
        ->and($result['availability'])->toContain('data de nascimento');
});

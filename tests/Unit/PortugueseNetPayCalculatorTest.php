<?php

use App\Services\PortugueseNetPayCalculator;

beforeEach(function () {
    $this->calc = new PortugueseNetPayCalculator;
});

it('retains IRS for 1500€ single with no dependents using 2026 table I', function () {
    $result = $this->calc->calculate(1500);

    expect($result['table'])->toBe('I')
        ->and($result['irs'])->toBe(168.17)
        ->and($result['social_security'])->toBe(165.00)
        ->and($result['net'])->toBe(1166.83);
});

it('matches the CGD 1300€ single example', function () {
    expect($this->calc->calculate(1300)['irs'])->toBe(119.97);
});

it('matches the CGD married unique titular with one dependent at 2000€', function () {
    $result = $this->calc->calculate(2000, 'casado_1', 1);

    expect($result['table'])->toBe('III')
        ->and($result['irs'])->toBe(131.21);
});

it('switches to table II and subtracts 34.29 per dependent', function () {
    $result = $this->calc->calculate(1500, 'solteiro', 1);

    expect($result['table'])->toBe('II')
        ->and($result['irs'])->toBe(133.88);
});

it('uses table I for married two earners even with dependents', function () {
    $result = $this->calc->calculate(1500, 'casado_2', 1);

    expect($result['table'])->toBe('I')
        ->and($result['irs'])->toBe(146.74);
});

it('does not withhold IRS up to the 2026 minimum wage', function () {
    expect($this->calc->calculate(920)['irs'])->toEqual(0);
});

it('adds exempt meal allowance to net without extra IRS or SS', function () {
    $result = $this->calc->calculate(
        gross: 1500,
        mealPerDay: 7.5,
        workingDays: 22,
        mealPayment: 'cartao',
    );

    expect($result['meal_total'])->toBe(165.00)
        ->and($result['meal_taxable'])->toBe(0.0)
        ->and($result['irs'])->toBe(168.17)
        ->and($result['social_security'])->toBe(165.00)
        ->and($result['net'])->toBe(1331.83);
});

it('taxes meal allowance above the 2026 cash exemption', function () {
    $result = $this->calc->calculate(
        gross: 1500,
        mealPerDay: 8.15,
        workingDays: 20,
        mealPayment: 'numerario',
    );

    expect($result['meal_taxable'])->toBe(40.00)
        ->and($result['social_security'])->toBe(169.40);
});

it('zeroes IRS withholding in IRS Jovem year 1 within the monthly cap', function () {
    $result = $this->calc->calculate(1500, irsJovem: true, irsJovemYear: 1);

    expect($result['irs'])->toBe(0.0)
        ->and($result['irs_before_jovem'])->toBe(168.17)
        ->and($result['net'])->toBe(1335.00);
});

it('applies 75 percent IRS Jovem exemption in year 2', function () {
    $result = $this->calc->calculate(1500, irsJovem: true, irsJovemYear: 2);

    expect($result['irs_jovem_rate'])->toBe(0.75)
        ->and($result['irs'])->toBe(42.04);
});

it('reduces the marginal rate by one point with three dependents', function () {
    $result = $this->calc->calculate(1500, 'solteiro', 3);

    expect($result['marginal_rate'])->toBe(0.231)
        ->and($result['irs'])->toBe(50.30);
});

it('uses disability table IV when applicable', function () {
    $zero = $this->calc->calculate(1694, 'solteiro', 0, true);
    $taxed = $this->calc->calculate(1800, 'solteiro', 0, true);

    expect($zero['table'])->toBe('IV')
        ->and($zero['irs'])->toEqual(0)
        ->and($taxed['irs'])->toBe(22.47);
});

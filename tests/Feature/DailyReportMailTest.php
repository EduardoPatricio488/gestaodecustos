<?php

use App\Mail\DailyReportMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

it('renders only the selected daily report sections', function () {
    $mail = new DailyReportMail(User::factory()->make(['name' => 'Ana']), [
        'date' => Carbon::parse('2026-08-31'),
        'earned' => 125.50,
        'spent' => 80.00,
        'balance' => 45.50,
        'categoryStats' => new Collection([
            (object) ['name' => 'Alimentação', 'total' => 80.00],
        ]),
        'sections' => ['earned'],
    ]);

    $mail->assertSeeInHtml('125,50');
    $mail->assertDontSeeInHtml('80,00');
    $mail->assertDontSeeInHtml('45,50');
    $mail->assertDontSeeInHtml('Despesas por categoria');
});

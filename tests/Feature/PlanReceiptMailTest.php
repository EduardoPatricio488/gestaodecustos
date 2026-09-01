<?php

use App\Mail\PlanReceiptMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

it('treats the pro plan as non-business', function () {
    $user = User::factory()->create([
        'name' => 'Ana Silva',
        'email' => 'ana@example.com',
        'plan' => 'pro',
    ]);

    expect($user->isBusinessPlan())->toBeFalse();
    expect($user->isPro())->toBeTrue();
});

it('sends a receipt email when a subscription is paid and when the monthly invoice is charged', function () {
    Mail::fake();

    $user = User::factory()->create([
        'name' => 'Ana Silva',
        'email' => 'ana@example.com',
        'plan' => 'free',
    ]);

    event(new WebhookReceived([
        'type' => 'checkout.session.completed',
        'data' => ['object' => [
            'id' => 'cs_test_initial',
            'client_reference_id' => $user->id,
            'amount_total' => 1999,
            'currency' => 'eur',
            'metadata' => ['plan_slug' => 'pro'],
        ]],
    ]));

    Mail::assertSent(PlanReceiptMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->planSlug === 'pro'
            && $mail->amount === 19.99
            && $mail->invoiceReference === 'cs_test_initial';
    });

    $user->forceFill(['stripe_id' => 'cus_test_123'])->save();
    $user->update(['plan' => 'pro']);

    event(new WebhookReceived([
        'type' => 'invoice.paid',
        'data' => ['object' => [
            'id' => 'in_test_recurring',
            'customer' => 'cus_test_123',
            'amount_paid' => 1999,
            'currency' => 'eur',
            'hosted_invoice_url' => 'https://pay.stripe.com/invoice/test',
            'lines' => ['data' => [['price' => ['nickname' => 'Pro']]]],
        ]],
    ]));

    Mail::assertSent(PlanReceiptMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->planSlug === 'pro'
            && $mail->amount === 19.99
            && $mail->invoiceReference === 'in_test_recurring';
    });
});

<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(5)->get();
        foreach ($users as $user) {
            Payment::create([
                'user_id' => $user->id,
                'invoice_id' => 'INV-'.strtoupper(str()->random(6)),
                'amount' => rand(0, 1) ? 5.00 : 10.00,
                'status' => 'paid',
                'plan_type' => User::normalizePlan($user->plan),
                'paid_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}

<?php
use App\Models\User;
use App\Mail\MonthlyReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Schedule::call(function () {
    $users = User::all();
    $lastMonth = now()->subMonth();

    foreach ($users as $user) {
        // Prepara os dados do mês anterior
        $spent = (float) $user->expenses()->whereMonth('spent_at', $lastMonth->month)->sum('amount');
        $earned = (float) $user->incomes()->whereMonth('received_at', $lastMonth->month)->sum('amount')
                + (float) $user->recurringIncomes()->sum('amount');

        $categoryStats = $user->expenses()
            ->whereMonth('spent_at', $lastMonth->month)
            ->select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->get();

        $data = [
            'spent' => $spent,
            'earned' => $earned,
            'categoryStats' => $categoryStats,
            'monthName' => $lastMonth->translatedFormat('F'),
            'year' => $lastMonth->year,
        ];

        Mail::to($user->email)->send(new MonthlyReportMail($user, $data));
    }
})->monthlyOn(1, '08:00'); // Envia no dia 1 às 08:00

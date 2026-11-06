<?php

namespace App\Console\Commands;

use App\Services\SalaryPaymentAutomationService;
use Illuminate\Console\Command;

class ProcessMonthlySalaryPayments extends Command
{
    protected $signature = 'salary:process-monthly-payments';

    protected $description = 'Regista e paga automaticamente os salários vencidos este mês';

    public function handle(SalaryPaymentAutomationService $service): int
    {
        $created = $service->processDuePayments();
        $this->info("Pagamentos salariais criados: {$created}.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\CompanySubscriptionPaymentService;
use Illuminate\Console\Command;

class ProcessCompanySubscriptionPayments extends Command
{
    protected $signature = 'subscriptions:process-company-payments';

    protected $description = 'Regista automaticamente as mensalidades de assinaturas pagas pela empresa';

    public function handle(CompanySubscriptionPaymentService $service): int
    {
        $created = $service->processDuePayments();
        $this->info("Mensalidades pagas pela empresa registadas: {$created}.");

        return self::SUCCESS;
    }
}

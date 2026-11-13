<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class SalaryAgeBenchmarkService
{
    public function forReport(User $user, Workspace $workspace, Collection $payments): array
    {
        $age = $user->date_of_birth?->age;
        $country = $user->work_country;
        $ageGroup = $age === null ? null : match (true) {
            $age < 30 => 'Y_LT30',
            $age < 50 => 'Y30-49',
            default => 'Y_GE50',
        };
        $eurostatCountry = $country === 'GR' ? 'EL' : $country;
        $mean = $ageGroup && $eurostatCountry
            ? data_get(config('eurostat_earnings_2022'), $eurostatCountry.'.'.$ageGroup)
            : null;

        $benchmark = [
            'work_country' => $country ? config('countries.'.$country) : null,
            'age_group' => match ($ageGroup) {
                'Y_LT30' => 'menos de 30 anos',
                'Y30-49' => '30 a 49 anos',
                'Y_GE50' => '50 anos ou mais',
                default => null,
            },
            'source' => 'Eurostat, EARN_SES22_20, inquérito de 2022',
            'source_url' => 'https://ec.europa.eu/eurostat/databrowser/product/page/EARN_SES22_20',
            'mean_monthly_gross_eur' => $mean,
            'scope' => 'Média salarial mensal bruta de 2022; empresas com pelo menos 10 trabalhadores, setores B-S exceto administração pública; inclui tempo inteiro e parcial.',
            'limitations' => 'Valores históricos e nominais. O salário base não inclui necessariamente complementos ou subsídios; não é diretamente equivalente à média. A moeda, horário, profissão e setor afetam a comparação. Não comparar salário líquido com média bruta.',
        ];

        if ($age === null || ! $country) {
            $benchmark['availability'] = 'Indica a data de nascimento e o país de trabalho no perfil para procurar uma referência salarial.';
        } elseif ($mean === null) {
            $benchmark['availability'] = 'Não há referência disponível para este país e faixa etária na fonte utilizada.';
        } else {
            $benchmark['availability'] = 'Referência disponível apenas para comparação indicativa, não é uma avaliação salarial atual.';
        }

        if ($payments->count() === 1 && (float) $payments->first()->salary_base_gross > 0) {
            $benchmark['recorded_monthly_base_gross'] = (float) $payments->first()->salary_base_gross;
            $benchmark['recorded_currency'] = strtoupper((string) ($workspace->currency ?: 'EUR'));
            if ($mean !== null && $benchmark['recorded_currency'] === 'EUR') {
                $benchmark['base_minus_historical_mean_eur'] = round($benchmark['recorded_monthly_base_gross'] - $mean, 2);
            }
        }

        return $benchmark;
    }
}

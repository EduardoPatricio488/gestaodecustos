<?php

namespace App\Services;

use RuntimeException;
use Smalot\PdfParser\Parser;

class IbanProofService
{
    public function extract(string $path, string $existingHolder = ''): array
    {
        $text = (new Parser())->parseFile($path)->getText();
        $text = preg_replace('/\s+/u', ' ', mb_strtoupper($text));

        if (! preg_match('/PT(?:\s*[0-9]){23}/u', $text, $ibanMatch)) {
            throw new RuntimeException('Não foi possível encontrar um IBAN português no documento.');
        }

        $iban = preg_replace('/\s+/', '', $ibanMatch[0]);
        if (strlen($iban) !== 25) {
            throw new RuntimeException('O IBAN extraído não tem um formato válido.');
        }

        $result = ['iban' => $iban, 'currency' => 'EUR'];
        $bank = match (substr($iban, 4, 4)) {
            '0018' => 'Santander',
            '0035' => 'Caixa Geral de Depósitos',
            '0007' => 'Novo Banco',
            '0033' => 'Millennium BCP',
            '0010' => 'Banco BPI',
            default => '',
        };

        if ($bank !== '') {
            $result['bank_name'] = $bank;
            $result['name'] = 'Conta '.$bank;
            $result['icon'] = 'building-library';
            if ($bank === 'Santander') {
                $result['color'] = '#EC0000';
            }
        }
        if (preg_match('/\b([A-Z]{4}PT[A-Z0-9]{2}(?:[A-Z0-9]{3})?)\b/u', $text, $swiftMatch)) {
            $result['swift'] = $swiftMatch[1];
        }
        if (preg_match('/TITULAR\s+(.+?)(?=\s+(?:IBAN|BIC|SWIFT|DATA|CONTA)\b|$)/u', $text, $holderMatch)) {
            $result['holder_name'] = trim($holderMatch[1]);
        }
        if (preg_match('/\bCONTA\s*:?[ \t]*([0-9][0-9 ]{7,24}?)(?=\s+(?:IBAN|BIC|SWIFT|TITULAR|DATA)\b|$)/u', $text, $accountMatch)) {
            $result['account_number'] = trim($accountMatch[1]);
        }
        if ($bank === 'Santander') {
            $result['swift'] = 'TOTAPTPL';
            $holderName = trim(mb_convert_case(mb_strtolower($result['holder_name'] ?? $existingHolder), MB_CASE_TITLE, 'UTF-8'));
            $result['name'] = 'Santander'.($holderName !== '' ? ' - '.$holderName : '');
        }

        return $result;
    }
}

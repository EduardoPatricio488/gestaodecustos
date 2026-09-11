<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

final class AiIntentDetector
{
    public const FINANCIAL_SUMMARY = 'FINANCIAL_SUMMARY';

    public const EXPENSE_ANALYSIS = 'EXPENSE_ANALYSIS';

    public const INCOME_ANALYSIS = 'INCOME_ANALYSIS';

    public const CATEGORY_ANALYSIS = 'CATEGORY_ANALYSIS';

    public const BUDGET_ANALYSIS = 'BUDGET_ANALYSIS';

    public const SAVING_ADVICE = 'SAVING_ADVICE';

    public const TRANSACTION_SEARCH = 'TRANSACTION_SEARCH';

    public const TRANSACTION_CREATION = 'TRANSACTION_CREATION';

    public const TRANSACTION_UPDATE = 'TRANSACTION_UPDATE';

    public const TRANSACTION_DELETE = 'TRANSACTION_DELETE';

    public const SUBSCRIPTION_ANALYSIS = 'SUBSCRIPTION_ANALYSIS';

    public const INVESTMENT_ANALYSIS = 'INVESTMENT_ANALYSIS';

    public const FORECAST = 'FORECAST';

    public const COMPARISON = 'COMPARISON';

    public const BUSINESS_ANALYSIS = 'BUSINESS_ANALYSIS';

    public const GENERAL_FINANCE = 'GENERAL_FINANCE';

    public const GENERAL_CHAT = 'GENERAL_CHAT';

    public const HELP = 'HELP';

    public const UNKNOWN = 'UNKNOWN';

    public function detect(string $input): array
    {
        $text = Str::lower(Str::ascii(trim($input)));

        if ($text === '') {
            return $this->result(self::UNKNOWN, 100);
        }

        $rules = [
            self::TRANSACTION_DELETE => ['apaga', 'apagar', 'elimina', 'eliminar', 'remove', 'remover'],
            self::TRANSACTION_CREATION => ['adiciona', 'adicionar', 'regista', 'registar', 'cria', 'criar', 'insere', 'inserir', 'lanca', 'lancar', 'introduz', 'introduzir', 'guarda', 'guardar', 'anota', 'anotar'],
            self::TRANSACTION_UPDATE => ['altera', 'alterar', 'atualiza', 'atualizar', 'corrige', 'corrigir', 'muda', 'mudar', 'edita', 'editar'],
            self::COMPARISON => ['compara', 'comparar', 'comparacao', 'face ao mes passado', 'vs mes passado', 'versus', 'diferenca entre'],
            self::FORECAST => ['preve', 'prever', 'previsao', 'se continuar', 'ate ao fim do mes', 'final do mes', 'quanto vou gastar'],
            self::CATEGORY_ANALYSIS => ['categoria', 'alimentacao', 'restaurantes', 'casa', 'transporte', 'saude', 'educacao'],
            self::SUBSCRIPTION_ANALYSIS => ['subscricao', 'subscricoes', 'assinatura', 'assinaturas', 'netflix', 'spotify', 'recorrente'],
            self::INVESTMENT_ANALYSIS => ['investimento', 'investimentos', 'acoes', 'etf', 'cripto', 'crypto', 'carteira'],
            self::BUDGET_ANALYSIS => ['orcamento', 'budget', 'limite', 'quanto posso gastar'],
            self::SAVING_ADVICE => ['poupar', 'poupanca', 'economizar', 'onde posso poupar', 'plano para poupar'],
            self::EXPENSE_ANALYSIS => ['gastei', 'gastos', 'despesas', 'despesa', 'quanto gastei', 'maiores despesas', 'onde estou a gastar'],
            self::INCOME_ANALYSIS => ['receita', 'receitas', 'rendimento', 'rendimentos', 'salario', 'ordenado', 'quanto recebi'],
            self::TRANSACTION_SEARCH => ['procura', 'procurar', 'encontra', 'encontrar', 'mostra a despesa', 'mostra as despesas', 'transacao', 'transacoes'],
            self::BUSINESS_ANALYSIS => ['empresa', 'empresarial', 'negocio', 'faturacao', 'margem', 'clientes', 'fornecedores', 'faturas', 'cash flow', 'runway'],
            self::FINANCIAL_SUMMARY => ['situacao financeira', 'resumo financeiro', 'analisa as minhas financas', 'como estao as minhas financas', 'quanto dinheiro tenho', 'saldo disponivel'],
            self::HELP => ['como funciona', 'ajuda', 'o que posso fazer', 'como faco'],
            self::GENERAL_FINANCE => ['financas', 'financeiro', 'investir', 'impostos', 'credito', 'divida', 'emprestimo'],
        ];

        foreach ($rules as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($text, $keyword)) {
                    return $this->result($intent, $this->confidence($intent, $text, $keyword));
                }
            }
        }

        return $this->result(self::GENERAL_CHAT, 55);
    }

    public function toolFor(array $intent): ?string
    {
        return match ($intent['intent'] ?? self::UNKNOWN) {
            self::FINANCIAL_SUMMARY,
            self::CATEGORY_ANALYSIS,
            self::BUDGET_ANALYSIS,
            self::SAVING_ADVICE,
            self::FORECAST,
            self::COMPARISON,
            self::BUSINESS_ANALYSIS => 'get_financial_snapshot',
            self::EXPENSE_ANALYSIS,
            self::TRANSACTION_SEARCH,
            self::TRANSACTION_UPDATE => 'list_expenses',
            self::INCOME_ANALYSIS => 'list_incomes',
            self::SUBSCRIPTION_ANALYSIS => 'list_subscriptions',
            self::INVESTMENT_ANALYSIS => 'list_investments',
            default => null,
        };
    }

    private function result(string $intent, int $confidence): array
    {
        return [
            'intent' => $intent,
            'confidence' => max(0, min(100, $confidence)),
            'source' => 'deterministic_intent_detector',
        ];
    }

    private function confidence(string $intent, string $text, string $keyword): int
    {
        $base = match ($intent) {
            self::TRANSACTION_CREATION, self::TRANSACTION_DELETE => 96,
            self::FINANCIAL_SUMMARY, self::EXPENSE_ANALYSIS, self::INCOME_ANALYSIS => 92,
            self::COMPARISON, self::FORECAST => 90,
            default => 84,
        };

        return Str::length($text) <= Str::length($keyword) + 12 ? min(99, $base + 3) : $base;
    }
}

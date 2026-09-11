<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes used by the personal dashboard.
     *
     * The dashboard is workspace-scoped and performs many COUNT/SUM queries.
     * These indexes are intentionally conditional so the migration remains
     * safe across installations that have older/newer schema variants.
     */
    public function up(): void
    {
        $workspaceTables = [
            'categories',
            'expenses',
            'incomes',
            'investments',
            'subscriptions',
            'goals',
            'debts',
            'reminders',
            'bank_accounts',
            'fitness_activities',
            'activity_logs',
            'social_notifications',
            'recurring_incomes',
            'bank_statement_imports',
            'expense_splits',
        ];

        foreach ($workspaceTables as $table) {
            $this->addIndexIfMissing($table, ['workspace_id'], "dashboard_{$table}_workspace_idx");
        }

        $this->addIndexIfMissing(
            'expenses',
            ['workspace_id', 'spent_at'],
            'dashboard_expenses_workspace_spent_at_idx'
        );

        $this->addIndexIfMissing(
            'incomes',
            ['workspace_id', 'received_at'],
            'dashboard_incomes_workspace_received_at_idx'
        );

        $this->addIndexIfMissing(
            'activity_logs',
            ['workspace_id', 'created_at'],
            'dashboard_activity_logs_workspace_created_at_idx'
        );
    }

    public function down(): void
    {
        $indexes = [
            'categories' => ['dashboard_categories_workspace_idx'],
            'expenses' => ['dashboard_expenses_workspace_idx', 'dashboard_expenses_workspace_spent_at_idx'],
            'incomes' => ['dashboard_incomes_workspace_idx', 'dashboard_incomes_workspace_received_at_idx'],
            'investments' => ['dashboard_investments_workspace_idx'],
            'subscriptions' => ['dashboard_subscriptions_workspace_idx'],
            'goals' => ['dashboard_goals_workspace_idx'],
            'debts' => ['dashboard_debts_workspace_idx'],
            'reminders' => ['dashboard_reminders_workspace_idx'],
            'bank_accounts' => ['dashboard_bank_accounts_workspace_idx'],
            'fitness_activities' => ['dashboard_fitness_activities_workspace_idx'],
            'activity_logs' => ['dashboard_activity_logs_workspace_idx', 'dashboard_activity_logs_workspace_created_at_idx'],
            'social_notifications' => ['dashboard_social_notifications_workspace_idx'],
            'recurring_incomes' => ['dashboard_recurring_incomes_workspace_idx'],
            'bank_statement_imports' => ['dashboard_bank_statement_imports_workspace_idx'],
            'expense_splits' => ['dashboard_expense_splits_workspace_idx'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($tableIndexes as $index) {
                if ($this->indexExists($table, $index)) {
                    Schema::table($table, function (Blueprint $blueprint) use ($index): void {
                        $blueprint->dropIndex($index);
                    });
                }
            }
        }
    }

    private function addIndexIfMissing(string $table, array $columns, string $index): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        if ($this->indexExists($table, $index)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $index): void {
            $blueprint->index($columns, $index);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        foreach (Schema::getIndexes($table) as $existingIndex) {
            if (($existingIndex['name'] ?? null) === $index) {
                return true;
            }
        }

        return false;
    }
};

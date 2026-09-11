<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            ['expenses', ['workspace_id', 'spent_at'], 'expenses_workspace_spent_at_index'],
            ['incomes', ['workspace_id', 'received_at'], 'incomes_workspace_received_at_index'],
            ['categories', ['workspace_id', 'budget_limit'], 'categories_workspace_budget_index'],
            ['bank_accounts', ['workspace_id', 'current_balance'], 'bank_accounts_workspace_balance_index'],
            ['investments', ['workspace_id'], 'investments_workspace_index'],
            ['subscriptions', ['workspace_id', 'status', 'is_active'], 'subscriptions_workspace_status_active_index'],
            ['debts', ['workspace_id', 'type', 'is_paid'], 'debts_workspace_type_paid_index'],
            ['goals', ['workspace_id'], 'goals_workspace_index'],
            ['reminders', ['workspace_id', 'is_completed', 'updated_at'], 'reminders_workspace_completed_updated_index'],
            ['fitness_activities', ['workspace_id', 'user_id', 'activity_date'], 'fitness_workspace_user_date_index'],
            ['social_notifications', ['user_id', 'created_at'], 'social_notifications_user_created_index'],
        ];

        foreach ($indexes as [$table, $columns, $name]) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $missingColumn = collect($columns)->first(fn (string $column) => ! Schema::hasColumn($table, $column));
            if ($missingColumn !== null) {
                continue;
            }

            $existingIndexes = collect(Schema::getIndexes($table))->pluck('name');
            if ($existingIndexes->contains($name)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                $blueprint->index($columns, $name);
            });
        }
    }

    public function down(): void
    {
        $indexes = [
            ['expenses', 'expenses_workspace_spent_at_index'],
            ['incomes', 'incomes_workspace_received_at_index'],
            ['categories', 'categories_workspace_budget_index'],
            ['bank_accounts', 'bank_accounts_workspace_balance_index'],
            ['investments', 'investments_workspace_index'],
            ['subscriptions', 'subscriptions_workspace_status_active_index'],
            ['debts', 'debts_workspace_type_paid_index'],
            ['goals', 'goals_workspace_index'],
            ['reminders', 'reminders_workspace_completed_updated_index'],
            ['fitness_activities', 'fitness_workspace_user_date_index'],
            ['social_notifications', 'social_notifications_user_created_index'],
        ];

        foreach ($indexes as [$table, $name]) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $existingIndexes = collect(Schema::getIndexes($table))->pluck('name');
            if (! $existingIndexes->contains($name)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($name) {
                $blueprint->dropIndex($name);
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            'expenses' => [
                ['workspace_id', 'spent_at', 'id'],
                ['workspace_id', 'category_id'],
            ],
            'incomes' => [
                ['workspace_id', 'received_at', 'id'],
            ],
            'workspace_user' => [
                ['user_id', 'workspace_id'],
            ],
            'ai_messages' => [
                ['user_id', 'created_at'],
            ],
            'ai_action_logs' => [
                ['user_id', 'workspace_id', 'status'],
            ],
            'activity_logs' => [
                ['workspace_id', 'created_at'],
            ],
        ];

        foreach ($indexes as $tableName => $definitions) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($definitions as $columns) {
                $name = $tableName.'_'.implode('_', $columns).'_idx';

                try {
                    Schema::table($tableName, function (Blueprint $table) use ($columns, $name) {
                        $table->index($columns, $name);
                    });
                } catch (\Throwable) {
                    // Existing deployments may already have an equivalent index.
                }
            }
        }
    }

    public function down(): void
    {
        $indexes = [
            'expenses' => [
                'expenses_workspace_id_spent_at_id_idx',
                'expenses_workspace_id_category_id_idx',
            ],
            'incomes' => [
                'incomes_workspace_id_received_at_id_idx',
            ],
            'workspace_user' => [
                'workspace_user_user_id_workspace_id_idx',
            ],
            'ai_messages' => [
                'ai_messages_user_id_created_at_idx',
            ],
            'ai_action_logs' => [
                'ai_action_logs_user_id_workspace_id_status_idx',
            ],
            'activity_logs' => [
                'activity_logs_workspace_id_created_at_idx',
            ],
        ];

        foreach ($indexes as $tableName => $names) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($names as $name) {
                try {
                    Schema::table($tableName, function (Blueprint $table) use ($name) {
                        $table->dropIndex($name);
                    });
                } catch (\Throwable) {
                    // Ignore indexes that are not present.
                }
            }
        }
    }
};
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflineExpenseController extends Controller
{
    public function sync(Request $request)
    {
        $request->validate([
            'expenses' => 'required|array|max:50',
            'expenses.*.amount' => 'required|numeric|min:0.01',
            'expenses.*.title' => 'required|string|max:255',
            'expenses.*.spent_at' => 'required|date',
            'expenses.*.client_id' => 'nullable|string|max:100',
            'expenses.*.category_slug' => 'nullable|string|max:100',
            'expenses.*.description' => 'nullable|string|max:1000',
            'expenses.*.payment_method' => 'nullable|string|max:50',
            'expenses.*.notes' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        abort_unless($user, 401);

        $workspace = $user->currentWorkspace;
        abort_unless($workspace, 422, 'Não existe um espaço de trabalho ativo.');

        $synced = [];
        $failed = [];

        foreach ($request->expenses as $item) {
            try {
                $clientId = $item['client_id'] ?? null;

                if ($clientId) {
                    $existing = Expense::where('workspace_id', $workspace->id)
                        ->where('user_id', $user->id)
                        ->where('metadata->offline_client_id', $clientId)
                        ->first();

                    if ($existing) {
                        $synced[] = [
                            'client_id' => $clientId,
                            'server_id' => $existing->id,
                            'duplicate' => true,
                        ];

                        continue;
                    }
                }

                $categoryId = null;
                if (! empty($item['category_slug'])) {
                    $categoryId = Category::where('workspace_id', $workspace->id)
                        ->where('slug', $item['category_slug'])
                        ->value('id');
                }

                $expense = Expense::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'category_id' => $categoryId,
                    'title' => $item['title'],
                    'amount' => $item['amount'],
                    'description' => $item['description'] ?? null,
                    'spent_at' => $item['spent_at'],
                    'metadata' => [
                        'offline_client_id' => $clientId,
                        'payment_method' => $item['payment_method'] ?? null,
                        'notes' => $item['notes'] ?? null,
                        'synced_at' => now()->toIso8601String(),
                    ],
                ]);

                $synced[] = [
                    'client_id' => $clientId,
                    'server_id' => $expense->id,
                    'duplicate' => false,
                ];
            } catch (\Throwable $exception) {
                $failed[] = [
                    'client_id' => $item['client_id'] ?? null,
                    'message' => 'Não foi possível sincronizar este registo.',
                ];
            }
        }

        return response()->json([
            'synced' => $synced,
            'failed' => $failed,
            'count' => count($synced),
        ]);
    }
}

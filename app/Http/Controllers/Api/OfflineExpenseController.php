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
            'expenses.*.client_id' => 'nullable|string',
        ]);

        $user = Auth::user();
        $workspace = $user->currentWorkspace;
        $synced = [];

        foreach ($request->expenses as $item) {
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
                'metadata' => ['offline_client_id' => $item['client_id'] ?? null, 'synced_at' => now()->toIso8601String()],
            ]);

            $synced[] = [
                'client_id' => $item['client_id'] ?? null,
                'server_id' => $expense->id,
            ];
        }

        return response()->json(['synced' => $synced, 'count' => count($synced)]);
    }
}

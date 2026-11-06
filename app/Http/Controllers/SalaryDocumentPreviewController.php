<?php

namespace App\Http\Controllers;

use App\Models\RecurringIncome;
use App\Models\SalaryDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SalaryDocumentPreviewController extends Controller
{
    public function __invoke(SalaryDocument $salaryDocument): BinaryFileResponse
    {
        $user = auth()->user();
        abort_unless(
            (int) $salaryDocument->user_id === (int) $user->id
                && (int) $salaryDocument->workspace_id === (int) $user->current_workspace_id,
            404
        );

        RecurringIncome::where('id', $salaryDocument->recurring_income_id)
            ->where('user_id', $user->id)
            ->where('workspace_id', $user->current_workspace_id)
            ->where('source', 'emprego')
            ->whereNull('metadata->income_type')
            ->firstOrFail();

        abort_unless(Storage::disk('local')->exists($salaryDocument->file_path), 404);

        return response()->file(Storage::disk('local')->path($salaryDocument->file_path), [
            'Content-Type' => $salaryDocument->mime_type,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

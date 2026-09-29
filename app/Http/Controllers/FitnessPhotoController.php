<?php

namespace App\Http\Controllers;

use App\Models\FitnessActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FitnessPhotoController extends Controller
{
    public function show(int $id): BinaryFileResponse
    {
        $activity = FitnessActivity::where('user_id', Auth::id())->findOrFail($id);

        abort_unless(
            $activity->photo_path && Storage::disk('local')->exists($activity->photo_path),
            404
        );

        return response()->file(Storage::disk('local')->path($activity->photo_path));
    }
}

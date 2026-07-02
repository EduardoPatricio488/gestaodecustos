<?php

namespace App\Http\Controllers;

use App\Models\StorePurchase;
use App\Services\StoreDownloadService;
use App\Services\StorePurchaseService;
use Illuminate\Http\Request;

class StoreDownloadController extends Controller
{
    public function requestToken(Request $request, StorePurchase $purchase, StoreDownloadService $downloads, StorePurchaseService $activity)
    {
        abort_unless($downloads->canDownload($purchase, auth()->id()), 403);

        $license = $purchase->license ?? app(\App\Services\StoreLicenseService::class)->issue($purchase);

        if ($downloads->hourlyDownloadsExceeded($license)) {
            return back()->with('error', 'Limite de downloads por hora atingido. Tenta novamente mais tarde.');
        }

        $token = $downloads->generateToken($license);

        $moduleNumber = $request->query('module');

        $activity->logActivity('store_download_request', "Pediu download: {$purchase->product->title}".($moduleNumber ? " (módulo {$moduleNumber})" : ''), [
            'purchase_id' => $purchase->id,
            'module' => $moduleNumber,
        ]);

        return redirect()->route('store.download', array_filter([
            'purchase' => $purchase->id,
            'token' => $token,
            'module' => $moduleNumber,
        ]));
    }

    public function download(Request $request, StorePurchase $purchase, StoreDownloadService $downloads, StorePurchaseService $activity)
    {
        abort_unless($downloads->canDownload($purchase, auth()->id()), 403);

        $license = $purchase->license ?? app(\App\Services\StoreLicenseService::class)->issue($purchase);

        $token = $request->query('token', '');
        abort_unless($downloads->validateToken($license, $token), 403, 'Token inválido ou expirado.');

        if ($downloads->hourlyDownloadsExceeded($license)) {
            abort(429, 'Limite de downloads por hora atingido.');
        }

        $moduleNumber = $request->query('module') ? (int) $request->query('module') : null;

        $downloads->logDownload($license, auth()->id(), $token);

        $activity->logActivity('store_download', "Descarregou: {$purchase->product->title}".($moduleNumber ? " (módulo {$moduleNumber})" : ''), [
            'purchase_id' => $purchase->id,
            'license_id' => $license->id,
            'module' => $moduleNumber,
        ]);

        return $downloads->streamFile($purchase, $moduleNumber);
    }
}

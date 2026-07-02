<?php

namespace App\Services;

use App\Models\StoreDownloadLog;
use App\Models\StoreLicense;
use App\Models\StorePurchase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoreDownloadService
{
    public const HOURLY_LIMIT = 5;

    public function canDownload(StorePurchase $purchase, int $userId): bool
    {
        return $purchase->user_id === $userId
            && $purchase->payment_status === 'completed';
    }

    public function hourlyDownloadsExceeded(StoreLicense $license): bool
    {
        $count = StoreDownloadLog::where('license_id', $license->id)
            ->where('downloaded_at', '>=', now()->subHour())
            ->count();

        return $count >= self::HOURLY_LIMIT;
    }

    public function generateToken(StoreLicense $license): string
    {
        $token = Str::random(64);
        session(["store_download_token_{$license->id}" => Hash::make($token)]);

        return $token;
    }

    public function validateToken(StoreLicense $license, string $token): bool
    {
        $hash = session("store_download_token_{$license->id}");

        return $hash && Hash::check($token, $hash);
    }

    public function logDownload(StoreLicense $license, int $userId, ?string $token = null): void
    {
        StoreDownloadLog::create([
            'user_id' => $userId,
            'license_id' => $license->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'token_hash' => $token ? Hash::make($token) : null,
            'downloaded_at' => now(),
        ]);

        $license->increment('download_count');
        session()->forget("store_download_token_{$license->id}");
    }

    public function resolvePath(StorePurchase $purchase, ?int $moduleNumber = null): ?string
    {
        $product = $purchase->product;

        if ($moduleNumber !== null && $product->type === 'course' && $product->course_modules) {
            $module = collect($product->course_modules)->firstWhere('number', $moduleNumber);

            return $module['pdf_path'] ?? null;
        }

        return $product->download_path;
    }

    public function streamFile(StorePurchase $purchase, ?int $moduleNumber = null): StreamedResponse
    {
        $product = $purchase->product;
        $path = $this->resolvePath($purchase, $moduleNumber);

        if ($path && Storage::disk('local')->exists($path)) {
            $filename = Str::slug($product->title);
            if ($moduleNumber) {
                $filename .= '-modulo-'.$moduleNumber;
            }
            $filename .= '.pdf';

            return Storage::disk('local')->download($path, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        $content = $this->generatePlaceholderPdf($product->title, $purchase->license?->license_key ?? 'N/A', $moduleNumber);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, Str::slug($product->title).'.txt', [
            'Content-Type' => 'text/plain',
        ]);
    }

    private function generatePlaceholderPdf(string $title, string $licenseKey, ?int $moduleNumber): string
    {
        $module = $moduleNumber ? " (Módulo {$moduleNumber})" : '';

        return implode("\n", [
            'Finance Hub PRO - Recurso PDF',
            '================================',
            "Produto: {$title}{$module}",
            "Licença: {$licenseKey}",
            'Data: '.now()->toDateTimeString(),
            '',
            'PDF em geração. Executa: php artisan db:seed --class=StoreSeeder',
        ]);
    }
}

<?php

namespace App\Jobs;

use App\Services\InvoiceScanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessInvoiceScan implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 130;

    public int $uniqueFor = 1200;

    public bool $failOnTimeout = true;

    public function __construct(
        public string $cacheKey,
        public string $storedPath,
        public string $filename,
        public string $mimeType,
        public array $options,
        public int $userId,
        public int $workspaceId,
    ) {
        $this->onConnection('database');
        $this->onQueue('default');
    }

    public function uniqueId(): string
    {
        return $this->cacheKey;
    }

    public function handle(InvoiceScanService $scanner): void
    {
        $scan = Cache::get($this->cacheKey);
        if (! is_array($scan) || ($scan['status'] ?? null) !== 'processing') {
            return;
        }

        try {
            $disk = Storage::disk('public');
            if (! $disk->exists($this->storedPath)) {
                throw new \RuntimeException('O ficheiro temporário da fatura já não está disponível.');
            }

            $result = $scanner->analyse(
                $disk->path($this->storedPath),
                $this->filename,
                $this->mimeType,
                $this->options
            );

            Cache::put($this->cacheKey, [
                'status' => 'completed',
                'data' => $result,
                'category_slug' => $result['category_slug'] ?? null,
                'document_path' => $this->storedPath,
                'document_name' => $this->filename,
                'document_mime' => $this->mimeType,
                'user_id' => $this->userId,
                'workspace_id' => $this->workspaceId,
                'completed_at' => now()->timestamp,
            ], now()->addMinutes(30));
        } catch (Throwable $exception) {
            Log::warning('Invoice scan job failed.', [
                'message' => $exception->getMessage(),
                'user_id' => $this->userId,
                'workspace_id' => $this->workspaceId,
                'cache_key_hash' => hash('sha256', $this->cacheKey),
            ]);

            Cache::put($this->cacheKey, [
                'status' => 'failed',
                'error' => $exception->getMessage(),
                'document_path' => $this->storedPath,
                'document_name' => $this->filename,
                'document_mime' => $this->mimeType,
                'user_id' => $this->userId,
                'workspace_id' => $this->workspaceId,
                'failed_at' => now()->timestamp,
            ], now()->addMinutes(30));
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Invoice scan job exhausted or timed out.', [
            'message' => $exception?->getMessage(),
            'user_id' => $this->userId,
            'workspace_id' => $this->workspaceId,
            'cache_key_hash' => hash('sha256', $this->cacheKey),
        ]);

        Cache::put($this->cacheKey, [
            'status' => 'failed',
            'error' => 'A análise foi interrompida ou excedeu o tempo limite. O ficheiro temporário foi mantido; podes tentar novamente.',
            'document_path' => $this->storedPath,
            'document_name' => $this->filename,
            'document_mime' => $this->mimeType,
            'user_id' => $this->userId,
            'workspace_id' => $this->workspaceId,
            'failed_at' => now()->timestamp,
        ], now()->addMinutes(30));
    }
}

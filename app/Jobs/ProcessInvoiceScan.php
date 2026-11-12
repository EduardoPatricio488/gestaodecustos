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
    public int $timeout = 120;
    public int $uniqueFor = 300;

    public function __construct(
        public string $cacheKey,
        public string $storedPath,
        public string $filename,
        public string $mimeType,
        public array $options,
        public int $userId,
        public int $workspaceId,
        public string $optionField = 'category',
    ) {
        $this->onConnection('database');
        $this->onQueue('default');
    }

    public function uniqueId(): string
    {
        return hash('sha256', $this->cacheKey);
    }

    public function handle(InvoiceScanService $scanner): void
    {
        $disk = Storage::disk('public');
        $state = Cache::get($this->cacheKey);
        if (! is_array($state) || ($state['status'] ?? null) !== 'processing') {
            return;
        }

        try {
            if (! $disk->exists($this->storedPath)) {
                throw new \RuntimeException('O ficheiro temporário da fatura já não está disponível.');
            }

            $contents = $disk->get($this->storedPath);
            if (! is_string($contents) || $contents === '') {
                throw new \RuntimeException('Não foi possível ler o ficheiro temporário da fatura.');
            }

            $data = $scanner->analyse($contents, $this->filename, $this->mimeType, $this->options, $this->optionField);
            $categorySlug = null;

            if ($this->optionField === 'category') {
                $matched = collect($this->options)->first(
                    static fn ($option) => mb_strtolower(trim((string) ($option['name'] ?? ''))) === mb_strtolower(trim((string) ($data['category'] ?? '')))
                );
                $categorySlug = $matched['slug'] ?? null;
                if (! $categorySlug) {
                    throw new \RuntimeException('A IA não associou a fatura a uma categoria válida deste workspace.');
                }
            }

            Cache::put($this->cacheKey, [
                'status' => 'completed',
                'data' => $data,
                'category_slug' => $categorySlug,
                'document_path' => $this->storedPath,
                'document_name' => $this->filename,
                'document_mime' => $this->mimeType,
                'user_id' => $this->userId,
                'workspace_id' => $this->workspaceId,
                'completed_at' => now()->timestamp,
            ], now()->addMinutes(30));
        } catch (Throwable $exception) {
            Log::error('Invoice scan job failed.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
                'user_id' => $this->userId,
                'workspace_id' => $this->workspaceId,
                'cache_key' => hash('sha256', $this->cacheKey),
            ]);

            Cache::put($this->cacheKey, [
                'status' => 'failed',
                'error' => $exception->getMessage(),
                'user_id' => $this->userId,
                'workspace_id' => $this->workspaceId,
                'failed_at' => now()->timestamp,
            ], now()->addMinutes(20));

            // Uma única tentativa evita consumir quota gratuita em chamadas duplicadas.
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Invoice scan job reached terminal failure.', [
            'message' => $exception?->getMessage(),
            'user_id' => $this->userId,
            'workspace_id' => $this->workspaceId,
        ]);

        $state = Cache::get($this->cacheKey);
        if (! is_array($state) || ($state['status'] ?? null) === 'processing') {
            Cache::put($this->cacheKey, [
                'status' => 'failed',
                'error' => 'O processamento foi interrompido. O ficheiro foi mantido temporariamente; tenta novamente.',
                'user_id' => $this->userId,
                'workspace_id' => $this->workspaceId,
                'failed_at' => now()->timestamp,
            ], now()->addMinutes(20));
        }
    }
}

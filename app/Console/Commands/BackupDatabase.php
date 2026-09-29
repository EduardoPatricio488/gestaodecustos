<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
        {--keep-local : Keep the temporary local dump after upload}';

    protected $description = 'Create a compressed MySQL backup and upload it to private S3-compatible storage';

    public function handle(): int
    {
        if (!app()->environment('production')) {
            $this->error('Database backups are only enabled in production.');

            return self::FAILURE;
        }

        if (config('database.default') !== 'mysql') {
            $this->error('The production backup command currently supports MySQL only.');

            return self::FAILURE;
        }

        $bucket = env('AWS_BUCKET');
        $accessKey = env('AWS_ACCESS_KEY_ID');
        $secretKey = env('AWS_SECRET_ACCESS_KEY');
        $region = env('AWS_DEFAULT_REGION', 'us-east-1');

        if (!$bucket || !$accessKey || !$secretKey) {
            $this->error('AWS backup storage is not configured.');

            return self::FAILURE;
        }

        $backupDir = storage_path('app/private/backups');
        File::ensureDirectoryExists($backupDir);

        $filename = 'finance-pro-ai-'.now()->format('Y-m-d_H-i-s').'.sql.gz';
        $localPath = $backupDir.DIRECTORY_SEPARATOR.$filename;
        $remoteKey = trim(env('BACKUP_S3_PREFIX', 'finance-pro-ai/backups'), '/').'/'.$filename;

        $mysql = config('database.connections.mysql');

        $dump = new Process([
            'mysqldump',
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--skip-lock-tables',
            '--host='.$mysql['host'],
            '--port='.(string) $mysql['port'],
            '--user='.$mysql['username'],
            $mysql['database'],
        ], base_path(), [
            'MYSQL_PWD' => (string) $mysql['password'],
        ]);

        $dump->setTimeout(900);

        $this->info('A criar backup da base de dados...');
        $dump->run();

        if (!$dump->isSuccessful()) {
            $this->error('mysqldump falhou: '.$dump->getErrorOutput());

            return self::FAILURE;
        }

        $compressed = gzencode($dump->getOutput(), 9);

        if ($compressed === false || file_put_contents($localPath, $compressed) === false) {
            $this->error('Não foi possível criar o ficheiro comprimido.');

            return self::FAILURE;
        }

        $endpoint = env('AWS_ENDPOINT');
        $awsArgs = [
            'aws',
            's3',
            'cp',
            $localPath,
            's3://'.$bucket.'/'.$remoteKey,
            '--region',
            $region,
            '--sse',
            'AES256',
            '--only-show-errors',
        ];

        if ($endpoint) {
            array_splice($awsArgs, 5, 0, ['--endpoint-url', $endpoint]);
        }

        $upload = new Process($awsArgs, base_path(), [
            'AWS_ACCESS_KEY_ID' => $accessKey,
            'AWS_SECRET_ACCESS_KEY' => $secretKey,
            'AWS_DEFAULT_REGION' => $region,
        ]);

        $upload->setTimeout(900);
        $this->info('A enviar backup para armazenamento privado...');
        $upload->run();

        if (!$upload->isSuccessful()) {
            @unlink($localPath);
            $this->error('Upload do backup falhou: '.$upload->getErrorOutput());

            return self::FAILURE;
        }

        if (!$this->option('keep-local')) {
            @unlink($localPath);
        }

        $this->info('Backup concluído: '.$remoteKey);

        return self::SUCCESS;
    }
}

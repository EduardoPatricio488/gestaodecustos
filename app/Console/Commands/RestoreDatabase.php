<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class RestoreDatabase extends Command
{
    protected $signature = 'backup:restore
        {file : Nome do backup .sql.gz no armazenamento S3 privado}
        {--confirm : Confirmar explicitamente a substituição da base de dados atual}';

    protected $description = 'Restaurar um backup MySQL comprimido a partir do armazenamento privado';

    public function handle(): int
    {
        if (! app()->environment('production')) {
            $this->error('A restauração de backups está limitada ao ambiente de produção.');

            return self::FAILURE;
        }

        if (! $this->option('confirm')) {
            $this->error('Operação destrutiva bloqueada. Executa novamente com --confirm.');

            return self::FAILURE;
        }

        $file = basename((string) $this->argument('file'));
        if (! preg_match('/^finance-pro-ai-[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}-[0-9]{2}\.sql\.gz$/', $file)) {
            $this->error('Nome de backup inválido.');

            return self::FAILURE;
        }

        $bucket = config('backup.s3.bucket');
        $accessKey = config('backup.s3.access_key');
        $secretKey = config('backup.s3.secret_key');
        $region = config('backup.s3.region');

        if (! $bucket || ! $accessKey || ! $secretKey) {
            $this->error('Armazenamento de backups não configurado.');

            return self::FAILURE;
        }

        $dir = storage_path('app/private/restore');
        File::ensureDirectoryExists($dir);
        $gzPath = $dir.DIRECTORY_SEPARATOR.$file;
        $sqlPath = $dir.DIRECTORY_SEPARATOR.pathinfo($file, PATHINFO_FILENAME);

        $remoteKey = trim((string) config('backup.s3.prefix'), '/').'/'.$file;
        $awsArgs = ['aws', 's3', 'cp', 's3://'.$bucket.'/'.$remoteKey, $gzPath, '--region', $region, '--only-show-errors'];
        if ($endpoint = config('backup.s3.endpoint')) {
            array_splice($awsArgs, 6, 0, ['--endpoint-url', $endpoint]);
        }

        $env = [
            'AWS_ACCESS_KEY_ID' => $accessKey,
            'AWS_SECRET_ACCESS_KEY' => $secretKey,
            'AWS_DEFAULT_REGION' => $region,
        ];

        $download = new Process($awsArgs, base_path(), $env);
        $download->setTimeout(900);
        $download->run();

        if (! $download->isSuccessful()) {
            $this->error('Não foi possível descarregar o backup.');

            return self::FAILURE;
        }

        $extract = new Process(['gzip', '-dc', $gzPath], base_path());
        $extract->setTimeout(900);
        $extract->run(function (string $type, string $buffer) use ($sqlPath): void {
            if ($type === Process::OUT) {
                File::append($sqlPath, $buffer);
            }
        });

        if (! $extract->isSuccessful() || ! File::exists($sqlPath)) {
            $this->error('Não foi possível descomprimir o backup.');

            return self::FAILURE;
        }

        $mysql = config('database.connections.mysql');
        $restore = Process::fromShellCommandline(
            'mysql --host='.escapeshellarg((string) $mysql['host'])
            .' --port='.escapeshellarg((string) $mysql['port'])
            .' --user='.escapeshellarg((string) $mysql['username'])
            .' '.escapeshellarg((string) $mysql['database'])
            .' < '.escapeshellarg($sqlPath),
            base_path(),
            ['MYSQL_PWD' => (string) $mysql['password']]
        );
        $restore->setTimeout(1800);
        $this->warn('A restaurar a base de dados. Esta operação pode substituir dados atuais...');
        $restore->run();

        File::delete([$gzPath, $sqlPath]);

        if (! $restore->isSuccessful()) {
            $this->error('A restauração falhou. Consulta os logs para diagnóstico.');

            return self::FAILURE;
        }

        $this->info('Restauração concluída com sucesso.');

        return self::SUCCESS;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/** Dumps the MySQL database to storage/app/backups and keeps only the newest 14 files. */
class BackupDatabase extends Command
{
    private const KEEP = 14;

    protected $signature = 'app:backup';

    protected $description = 'Back up the database (mysqldump) into storage/app/backups, keeping the newest 14.';

    public function handle(): int
    {
        $mysqldump = $this->findMysqldump();

        if (! $mysqldump) {
            $this->error('mysqldump was not found on PATH or in the usual XAMPP/MySQL install locations.');
            return self::FAILURE;
        }

        $connection = config('database.connections.mysql');
        $dir = storage_path('app/backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $final = $dir.'/backup-'.now()->format('Y-m-d_H-i-s').'.sql';
        $tmp = $final.'.tmp';

        $command = [
            $mysqldump,
            '--single-transaction',
            '-h', (string) $connection['host'],
            '-P'.(string) $connection['port'],
            '-u'.(string) $connection['username'],
        ];

        if (! empty($connection['password'])) {
            $command[] = '-p'.$connection['password'];
        }

        $command[] = (string) $connection['database'];

        $process = new Process($command);
        $process->setTimeout(600);

        $handle = fopen($tmp, 'w');
        $process->run(function (string $type, string $buffer) use ($handle) {
            if ($type === Process::OUT) {
                fwrite($handle, $buffer);
            }
        });
        fclose($handle);

        if (! $process->isSuccessful()) {
            @unlink($tmp);
            $this->error('mysqldump failed: '.$process->getErrorOutput());
            return self::FAILURE;
        }

        // Only a finished dump gets the real name, so a crash mid-dump never leaves a partial
        // file that the retention pruning below would count as a real backup.
        rename($tmp, $final);

        $this->info('Backup written: '.$final);

        $this->prune($dir);

        return self::SUCCESS;
    }

    private function findMysqldump(): ?string
    {
        $which = new Process(['where', 'mysqldump']);
        $which->run();
        if ($which->isSuccessful()) {
            $first = trim(strtok($which->getOutput(), "\r\n"));
            if ($first !== '' && file_exists($first)) {
                return $first;
            }
        }

        $candidates = array_merge(
            glob('C:\\xampp\\mysql\\bin\\mysqldump.exe') ?: [],
            glob('C:\\Program Files\\MySQL\\MySQL Server *\\bin\\mysqldump.exe') ?: []
        );

        return $candidates[0] ?? null;
    }

    private function prune(string $dir): void
    {
        $files = glob($dir.'/backup-*.sql') ?: [];
        rsort($files); // newest filename first (timestamp is in the name)

        foreach (array_slice($files, self::KEEP) as $old) {
            @unlink($old);
        }
    }
}

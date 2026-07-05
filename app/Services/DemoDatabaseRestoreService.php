<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;

class DemoDatabaseRestoreService
{
    public function restore(): void
    {
        $backup = storage_path('app/backups/demo_backup.sql');

        if (!file_exists($backup)) {
            throw new \Exception('Demo backup file not found.');
        }

        $host = Config::get('database.connections.mysql.host');
        $port = Config::get('database.connections.mysql.port');
        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');

        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($backup)
        );

        $process = Process::fromShellCommandline($command);

        $process->setTimeout(0);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }
    }
}
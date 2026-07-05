<?php

namespace App\Console\Commands;

use App\Services\DemoDatabaseRestoreService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResetDemoDatabase extends Command
{
    protected $signature = 'demo:reset';

    protected $description = 'Reset Demo Database';

    public function handle(DemoDatabaseRestoreService $restore)
    {
        $this->info('Demo Reset Started');

        Cache::forever('demo_resetting', true);

        try {

            $restore->restore();

            $this->info('Demo Database Restored Successfully.');

        } catch (\Throwable $e) {

            $this->error($e->getMessage());

        } finally {

            Cache::forget('demo_resetting');

        }

        return Command::SUCCESS;
    }
}
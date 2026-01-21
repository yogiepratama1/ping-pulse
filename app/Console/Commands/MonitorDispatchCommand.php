<?php

namespace App\Console\Commands;

use App\Jobs\PerformUptimeCheck;
use App\Models\Monitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MonitorDispatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs for monitors due for check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lock = Cache::lock('monitor:dispatch:lock', 60);

        if (!$lock->get()) {
            $this->warn("The command is already running.");
            return;
        }

        try {
            Cache::put('monitor:dispatch:running', true, 60);
            $count = 0;
            Monitor::dueForCheck()->lazyById(100)->each(function ($monitor) use (&$count) {
                PerformUptimeCheck::dispatch($monitor);
                $count++;
            });

            $this->info("Dispatched {$count} monitor checks.");
        } finally {
            Cache::forget('monitor:dispatch:running');
            $lock->release();
        }
    }
}
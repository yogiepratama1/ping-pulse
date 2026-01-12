<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $count = 0;
        \App\Models\Monitor::dueForCheck()->lazyById(100)->each(function ($monitor) use (&$count) {
            \App\Jobs\PerformUptimeCheck::dispatch($monitor);
            $count++;
        });

        $this->info("Dispatched {$count} monitor checks.");
    }
}

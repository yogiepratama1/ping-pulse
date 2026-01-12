<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PerformUptimeCheck implements ShouldQueue
{
    use Queueable;

    public function __construct(public Monitor $monitor)
    {
    }

    public function handle(): void
    {
        $success = false;
        $error = null;

        try {
            $binary = $this->getPingBinaryPath();

            if (!file_exists($binary)) {
                throw new \RuntimeException("Ping binary not found: {$binary}");
            }

            $cmd = sprintf(
                '"%s" %s',
                $binary,
                escapeshellarg($this->monitor->url)
            );

            $output = shell_exec($cmd);

            if ($output === null) {
                throw new \RuntimeException('Failed to execute ping binary');
            }

            if (!$output) {
                throw new \RuntimeException('Ping binary returned empty output');
            }

            $result = json_decode($output, true, 512, JSON_THROW_ON_ERROR);

            $lastLatency = $result['last_ms'];
            $avgLatency  = $result['avg_ms'];

            $success = true;
        } catch (\Throwable $e) {
            $lastLatency = 0;
            $avgLatency  = 0;
            $error = $e->getMessage();
        }

        // Save log
        $this->monitor->monitorLogs()->create([
            'status_code' => $success ? 200 : null,
            'response_time_ms' => $lastLatency,
            'avg_response_time_ms' => $avgLatency,
            'is_success' => $success,
            'error_message' => $error
        ]);

        $this->monitor->update([
            'status' => $success ? 'up' : 'down',
            'last_checked_at' => now(),
        ]);

        // Status handling
        if ($success) {
            if ($this->monitor->status !== 'up') {
                $this->monitor->update(['status' => 'up']);
                $this->monitor->incidents()->open()->get()->each->resolve();
            }
        } else {
            if ($this->monitor->status !== 'down') {
                $this->monitor->update(['status' => 'down']);

                if ($this->monitor->incidents()->open()->count() === 0) {
                    $this->monitor->incidents()->create([
                        'started_at' => now(),
                    ]);
                }
            }
        }

        $this->monitor->scheduleNextCheck();
    }

    private function getPingBinaryPath(): string
    {
        $base = base_path('go');

        return match (PHP_OS_FAMILY) {
            'Windows' => $base . DIRECTORY_SEPARATOR . 'ping.exe',
            'Linux', 'Darwin' => $base . DIRECTORY_SEPARATOR . 'ping',
            default => throw new \RuntimeException('Unsupported OS: ' . PHP_OS_FAMILY),
        };
    }

}

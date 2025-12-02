<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class AesMetricsCommand extends Command
{
    protected $signature = 'aes:metrics {type?}';
    protected $description = 'Show AES callback performance metrics';

    public function handle()
    {
        $type = $this->argument('type');

        if ($type) {
            $this->showCommandMetrics($type);
        } else {
            $this->showAllMetrics();
        }
    }

    protected function showCommandMetrics(string $command)
    {
        $date = now()->format('Y-m-d-H');
        $key = "aes:metrics:{$command}:{$date}";

        try {
            $redis = Redis::connection()->client();
            $data = $redis->hgetall($key);
        } catch (\Exception $e) {
            $this->error('Redis connection error');
            return;
        }

        if (empty($data)) {
            $this->warn('No metrics for this hour');
            return;
        }

        $count = $data['count'] ?? 0;
        $totalTime = $data['total_time'] ?? 0;
        $avgTime = $count > 0 ? $totalTime / $count : 0;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total requests', number_format($count)],
                ['Avg response time', round($avgTime, 2) . ' ms'],
                ['Total time', round($totalTime, 2) . ' ms'],
            ]
        );
    }

    protected function showAllMetrics()
    {
        $commands = ['bet', 'win', 'cancel', 'balance', 'status'];
        $date = now()->format('Y-m-d-H');

        try {
            $redis = Redis::connection()->client();
        } catch (\Exception $e) {
            $this->error('Redis connection error');
            return;
        }

        $rows = [];
        foreach ($commands as $command) {
            $key = "aes:metrics:{$command}:{$date}";

            try {
                $data = $redis->hgetall($key);
            } catch (\Exception $e) {
                continue;
            }

            if (empty($data)) continue;

            $count = $data['count'] ?? 0;
            $totalTime = $data['total_time'] ?? 0;
            $avgTime = $count > 0 ? $totalTime / $count : 0;

            $rows[] = [
                $command,
                number_format($count),
                round($avgTime, 2) . ' ms',
                $this->getStatus($avgTime),
            ];
        }

        if (empty($rows)) {
            $this->warn('No metrics available for the current hour');
            return;
        }

        $this->table(['Command', 'Requests', 'Avg Time', 'Status'], $rows);
    }

    protected function getStatus(float $avgTime): string
    {
        if ($avgTime < 10) return '✅ Good';
        if ($avgTime < 20) return '⚠️  Warning';
        return '❌ Slow';
    }
}

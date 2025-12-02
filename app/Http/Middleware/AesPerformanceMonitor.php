<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AesPerformanceMonitor
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000;
        $command = $request->input('command');

        if ($duration > 20) {
            Log::warning('Slow AES callback', [
                'command' => $command,
                'duration_ms' => round($duration, 2)
            ]);
        }

        $this->recordMetrics($command, $duration);

        return $response;
    }

    protected function recordMetrics(string $command, float $duration): void
    {
        try {
            $date = now()->format('Y-m-d-H');
            $key = "aes:metrics:{$command}:{$date}";

            $redis = Redis::connection()->client();
            $redis->hincrby($key, 'count', 1);
            $redis->hincrbyfloat($key, 'total_time', $duration);
            $redis->expire($key, 86400);
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}

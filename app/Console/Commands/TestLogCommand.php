<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestLogCommand extends Command
{
    protected $signature = 'test:log';
    protected $description = 'Send a test message to the log';

    public function handle()
    {
        Log::info('Test command executed at ' . now());
    }
}

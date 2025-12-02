<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    protected $signature = 'logs:clear';
    protected $description = 'Clear the Laravel log file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        file_put_contents(storage_path('logs/laravel.log'), '');
        file_put_contents(storage_path('logs/oborot.log'), '');
        file_put_contents(storage_path('logs/slots.log'), '');
        $this->info('Logs have been cleared!');
    }
}

<?php

namespace App\Providers;

use App\Broadcasting\WSocketBroadcaster;
use Illuminate\Support\ServiceProvider;

class WSocketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('custom-wsocket', function ($app) {
            return new WSocketBroadcaster(config('wsocket.host'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

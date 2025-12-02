<?php

namespace App\Providers;

use App\Contracts\NotifyServiceContract;
use App\Services\NotifyService;
use App\Services\User\ExternalAuthService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if (config('app.env') === 'production' || request()->isSecure()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        $directory = public_path('/assets/images/emoj/');

        $emoj_arr = [];
        foreach (glob($directory."*.{jpg,jpeg,png,gif}", GLOB_BRACE) as $filename) {
            $name_file = basename($filename);
            $name_teg = explode('.', $name_file)[0];
            $emoj_arr[':'.$name_teg.':'] = $name_file;
        }

        View::share('chat_emoj', json_encode($emoj_arr));
        View::share('img_emoj', $emoj_arr);

        // Глобальные категории для sidebar
        View::composer('*', function ($view) {
            if (!$view->offsetExists('sidebarCategories')) {
                $view->with('sidebarCategories', \App\Models\GameCategory::where('is_active', true)
                    ->orderBy('order')
                    ->get());
            }
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ExternalAuthService::class, function ($app) {
            return new ExternalAuthService(config('services.ulogin.endpoint'));
        });


        $this->app->bind(NotifyServiceContract::class, function ($app) {
            return new NotifyService();
        });
    }
}

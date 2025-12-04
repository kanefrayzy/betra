<?php

namespace App\Providers;

use App\Contracts\NotifyServiceContract;
use App\Services\NotifyService;
use App\Services\User\ExternalAuthService;
use App\Models\Settings;
use App\Models\GameCategory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * ═══════════════════════════════════════════════════════════
     *  Bootstrap Application Services
     * ═══════════════════════════════════════════════════════════
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        if (config('app.env') === 'production' || request()->isSecure()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $this->shareChatEmojis();
        $this->shareGlobalSettings();
        $this->shareGameCategories();
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Share Chat Emojis (Cached)
     * ═══════════════════════════════════════════════════════════
     */
    protected function shareChatEmojis(): void
    {
        $emojis = Cache::remember('chat_emojis', 86400, function () {
            $directory = public_path('/assets/images/emoj/');
            $emoj_arr = [];
            
            foreach (glob($directory."*.{jpg,jpeg,png,gif}", GLOB_BRACE) as $filename) {
                $name_file = basename($filename);
                $name_teg = explode('.', $name_file)[0];
                $emoj_arr[':'.$name_teg.':'] = $name_file;
            }
            
            return $emoj_arr;
        });

        View::share('chat_emoj', json_encode($emojis));
        View::share('img_emoj', $emojis);
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Share Global Settings (Cached)
     * ═══════════════════════════════════════════════════════════
     */
    protected function shareGlobalSettings(): void
    {
        View::composer('*', function ($view) {
            $settings = Cache::remember('app_settings', 86400, function () {
                return Settings::first();
            });
            
            if (!$settings) {
                $settings = new Settings();
            }
            
            $view->with('settings', $settings);
        });
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Share Game Categories (Cached)
     * ═══════════════════════════════════════════════════════════
     */
    protected function shareGameCategories(): void
    {
        View::composer('*', function ($view) {
            if (!$view->offsetExists('sidebarCategories')) {
                $categories = Cache::remember('sidebar_categories', 86400, function () {
                    return GameCategory::select('id', 'name', 'slug', 'icon', 'order')
                        ->where('is_active', true)
                        ->orderBy('order', 'asc')
                        ->get();
                });
                
                $view->with('sidebarCategories', $categories);
            }
        });
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Register Application Services
     * ═══════════════════════════════════════════════════════════
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
<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\SlotegratorGame;
use App\Http\Controllers\Games\SlotsController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UnifiedSlotsController extends Controller
{
    /**
     * Универсальный запуск игры - определяет провайдера и перенаправляет
     */
     public function LaunchGame($slug)
     {
         // Быстрое кэширование с минимальными полями
         $game = Cache::remember("game:{$slug}", 86400, function () use ($slug) {
             return SlotegratorGame::select([
                 'id', 'name', 'slug', 'provider_type', 'provider', 'game_code', 'is_active'
             ])
             ->where('slug', $slug)
             ->where('is_active', 1)
             ->first();
         });

         if (!$game) {
             Log::warning("Game not found: {$slug}");
             return redirect()->route('home')->with('error', __('Игра не найдена'));
         }

         // Прямой запуск без лишних проверок
         return $this->routeToProvider($game, false);
     }

     public function LaunchdemoGame($slug)
     {
         // Быстрое кэширование
         $game = Cache::remember("game:{$slug}", 86400, function () use ($slug) {
             return SlotegratorGame::select([
                 'id', 'name', 'slug', 'provider_type', 'provider', 'game_code', 'is_active'
             ])
             ->where('slug', $slug)
             ->where('is_active', 1)
             ->first();
         });

         if (!$game) {
             return redirect()->route('home')->with('error', __('Игра не найдена'));
         }

         // Прямой вызов без лишних проверок
         return $this->routeToProvider($game, true);
     }
     private function routeToProvider(SlotegratorGame $game, bool $isDemo = false)
     {
         // Убираем лишнее логирование для скорости
         switch ($game->provider_type) {
             case 'tbs2':
                 if ($isDemo) {
                     return app(Tbs2Controller::class)->launchDemoGameDirect($game);
                 }
                 return app(Tbs2Controller::class)->launchGameDirect($game);

             case 'slotegrator':
                 if ($isDemo) {
                     return app(SlotsController::class)->launchDemoGameDirect($game);
                 }
                 return app(SlotsController::class)->launchGameDirect($game);

             case 'aes':
                 if ($isDemo) {
                     return redirect()->back()->with('error', __('Demo mode not available for this provider'));
                 }
                 return app(AesController::class)->launchGameDirect($game);

            case 'betvio':
                if ($isDemo) {
                    return redirect()->back()->with('error', __('Demo mode not available for this provider'));
                }
                return app(BetvioController::class)->launchGameDirect($game);

             default:
                 throw new \Exception(__('Неподдерживаемый провайдер:') . ' ' . $game->provider_type);
         }
     }

     /**
      * Очистить кэш игры (вызывать при обновлении игр в БД)
      */
     public static function clearGameCache(string $slug = null)
     {
         if ($slug) {
             Cache::forget("game:{$slug}");
         } else {
             // Очистить кэш игр по паттерну
             $keys = Cache::get('game_cache_keys', []);
             foreach ($keys as $key) {
                 Cache::forget($key);
             }
         }
     }
}

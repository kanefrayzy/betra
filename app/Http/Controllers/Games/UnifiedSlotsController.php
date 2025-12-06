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
                 'id', 'uuid', 'name', 'slug', 'provider_type', 'provider', 'game_code', 'is_active', 'image'
             ])
             ->where('slug', $slug)
             ->where('is_active', 1)
             ->first();
         });

         if (!$game) {
             Log::warning("Game not found: {$slug}");
             return redirect()->route('home')->with('error', __('Игра не найдена'));
         }

         // МГНОВЕННЫЙ ОТВЕТ - возвращаем страницу без ожидания URL
         // URL будет загружен через Alpine.js асинхронно
         return view('games.play', [
             'game' => $game,
             'gameSlug' => $slug,
             'settings' => \App\Models\Settings::first()
         ]);
     }
     
     /**
      * API endpoint для получения URL игры (вызывается асинхронно)
      * ОПТИМИЗИРОВАНО: напрямую вызывает Slotegrator без промежуточных слоев
      */
     public function getGameUrl($slug)
     {
         $game = Cache::remember("game:{$slug}", 86400, function () use ($slug) {
             return SlotegratorGame::select([
                 'id', 'uuid', 'name', 'slug', 'provider_type', 'provider', 'game_code', 'is_active'
             ])
             ->where('slug', $slug)
             ->where('is_active', 1)
             ->first();
         });

         if (!$game) {
             return response()->json(['error' => 'Game not found'], 404);
         }

         try {
             // ПРЯМОЙ ВЫЗОВ SLOTEGRATOR без лишних проверок провайдера
             $slotsController = app(SlotsController::class);
             $result = $slotsController->launchGameDirect($game);
             
             // Извлекаем данные из view response
             if ($result instanceof \Illuminate\View\View) {
                 $gameUrl = $result->getData()['gameUrl'] ?? null;
                 
                 return response()->json([
                     'success' => true,
                     'url' => $gameUrl,
                     'name' => $game->name,
                     'provider' => $game->provider
                 ]);
             }
             
             return response()->json(['error' => 'Failed to get game URL'], 500);
             
         } catch (\Exception $e) {
             Log::error('Game URL error', [
                 'slug' => $slug,
                 'error' => $e->getMessage()
             ]);
             
             return response()->json([
                 'error' => 'Game temporarily unavailable',
                 'message' => $e->getMessage()
             ], 500);
         }
     }

     public function LaunchdemoGame($slug)
     {
         // Быстрое кэширование
         $game = Cache::remember("game:{$slug}", 86400, function () use ($slug) {
             return SlotegratorGame::select([
                 'id', 'uuid', 'name', 'slug', 'provider_type', 'provider', 'game_code', 'is_active'
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

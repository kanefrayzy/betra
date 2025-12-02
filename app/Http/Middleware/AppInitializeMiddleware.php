<?php
namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\PaymentHandler;
use App\Models\Settings;
use App\Models\User;
use App\Models\DepositBonus;
use App\Models\UserDepositBonus;
use App\Services\RankService;
use App\Services\UserShareDataService;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AppInitializeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        View::share('u', $user);

        // Кэширование payment_handlers на 1 день
        $payment_handlers = Cache::remember('payment_handlers', now()->addDay(), function () {
            return PaymentHandler::all();
        });

        if ($user) {
            // Логика для обычных пользователей
            [$matchingHandlers, $otherHandlers] = $this->handlersFilter($payment_handlers, $user);
            [$matchingSystems, $otherSystems] = $this->systemFilter($payment_handlers, $user);
            $availableBonuses = $this->getBonusesForUser($user);
            View::share('ref', UserShareDataService::getRef($user));
            View::share(RankService::progress($user));
            View::share('matchingHandlers', $matchingHandlers);
            View::share('otherHandlers', $otherHandlers);
            View::share('matchingSystems', $matchingSystems);
            View::share('otherSystems', $otherSystems);
            View::share('rakeback_balance', moneyFormat(toUSD($user->rakeback, $user->currency->symbol)));
            View::share('availableBonuses', $availableBonuses);

            if (strpos($request->getPathInfo(), '/games/') === 0) {
              return $next($request);
          }
              // Пропускаем проверку для маршрута входа
              if ($request->routeIs('login') || $request->routeIs('login.post')) {
                  return $next($request);
              }

              if (Auth::check() && Auth::user()->last_login_at === null) {
                  Auth::logout();
                  return redirect()->route('home')->with('error', 'Ваша сессия была завершена. Пожалуйста, войдите снова.');
              }
        }

        Carbon::setLocale('ru');

        // Кэширование настроек на 1 день
        $settings = Cache::remember('app_settings', now()->addDay(), function () {
            return Settings::first();
        });
        
        if (!$settings) {
            $settings = new Settings();
        }
        
        View::share('settings', $settings);

        return $next($request);
    }

    protected function handlersFilter(Collection $payment_handlers, User|Authenticatable|null $user): array
    {
        $matchingHandlers = $payment_handlers->filter(function ($handler) use ($user) {
            return $handler->currency == $user->currency->symbol;
        });
        $otherHandlers = $payment_handlers->diff($matchingHandlers);
        return [$matchingHandlers, $otherHandlers];
    }

    protected function systemFilter(Collection $payment_handlers, User|Authenticatable|null $user): array
    {
        $matchingSystems = $payment_handlers->filter(function ($system) use ($user) {
            return $system->currency == $user->currency->symbol;
        });
        $otherSystems = $payment_handlers->diff($matchingSystems);
        return [$matchingSystems, $otherSystems];
    }

    protected function getBonusesForUser(User $user): array
    {
        try {
            // Получаем все бонусы для валюты пользователя
            $bonuses = Cache::remember("deposit_bonuses:{$user->currency_id}", now(), function () use ($user) {
                return DepositBonus::where('currency_id', $user->currency_id)
                    ->orderBy('required_amount')
                    ->get()
                    ->toArray(); // Преобразуем коллекцию в массив сразу
            });

            // Получаем использованные бонусы
            $usedBonuses = Cache::remember("used_bonuses:{$user->id}", now(), function () use ($user) {
                return UserDepositBonus::where('user_id', $user->id)
                    ->pluck('deposit_bonus_id')
                    ->toArray();
            });

            // Теперь работаем с массивом, а не с коллекцией
            return array_map(function ($bonus) use ($usedBonuses) {
                return [
                    'id' => $bonus['id'],
                    'required_amount' => $bonus['required_amount'],
                    'bonus_amount' => $bonus['bonus_amount'],
                    'currency_id' => $bonus['currency_id'],
                    'used' => in_array($bonus['id'], $usedBonuses),
                    'first_time' => $bonus['required_amount'] <= 5,
                    'requirements' => [
                        'wagering' => __('Отыграть сумму депозита'),
                        'verification' => $bonus['required_amount'] <= 5
                            ? __('Подтвердить Telegram для вывода')
                            : __('Необходима верификация профиля для вывода')
                    ]
                ];
            }, $bonuses);

        } catch (\Exception $e) {
            \Log::error('Error getting bonuses: ' . $e->getMessage());
            return [];
        }
    }
}

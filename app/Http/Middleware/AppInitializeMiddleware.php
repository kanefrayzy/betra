<?php
namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\PaymentHandler;
use App\Models\Settings;
use App\Models\User;
use App\Models\DepositBonus;
use App\Models\UserDepositBonus;
use App\Services\RankService;
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
            View::share(RankService::progress($user));
            View::share('matchingHandlers', $matchingHandlers);
            View::share('otherHandlers', $otherHandlers);
            View::share('matchingSystems', $matchingSystems);
            View::share('otherSystems', $otherSystems);
            View::share('rakeback_balance', moneyFormat(toUSD($user->rakeback, $user->currency->symbol)));

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

}

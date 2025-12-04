<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\PaymentHandler;
use App\Models\User;
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
    /**
     * ═══════════════════════════════════════════════════════════
     *  Handle Incoming Request
     * ═══════════════════════════════════════════════════════════
     */
    public function handle(Request $request, Closure $next)
    {

        if (!Auth::check()) {
            return $next($request);
        }        
        
        $user = Auth::user();
        
        if ($user) {
            // Кешируем загрузку user relationships
            $this->loadUserRelationships($user);
            $this->shareUserData($user);
            $this->sharePaymentHandlers($user);
            
            if ($this->shouldSkipSessionCheck($request)) {
                return $next($request);
            }
            
            if ($this->isInvalidSession()) {
                return $this->logoutAndRedirect();
            }
        }

        Carbon::setLocale('ru');

        return $next($request);
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Load User Relationships with Caching
     * ═══════════════════════════════════════════════════════════
     */
    protected function loadUserRelationships(User $user): void
    {
        // Кешируем currency на 1 час (редко меняется)
        if (!$user->relationLoaded('currency')) {
            $currency = Cache::remember("user_{$user->id}_currency", 3600, function () use ($user) {
                return Currency::find($user->currency_id);
            });
            $user->setRelation('currency', $currency);
        }

        // Кешируем rank на 1 час
        if (!$user->relationLoaded('rank') && $user->rank_id) {
            $rank = Cache::remember("user_{$user->id}_rank", 3600, function () use ($user) {
                return \App\Models\Rank::find($user->rank_id);
            });
            $user->setRelation('rank', $rank);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Share User Data to Views
     * ═══════════════════════════════════════════════════════════
     */
    protected function shareUserData(User $user): void
    {
        View::share([
            'u' => $user,
            'rakeback_balance' => moneyFormat(toUSD($user->rakeback, $user->currency->symbol))
        ]);
        
        View::share(RankService::progress($user));
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Share Payment Handlers
     * ═══════════════════════════════════════════════════════════
     */
    protected function sharePaymentHandlers(User $user): void
    {
        $paymentHandlers = Cache::remember('payment_handlers', 86400, function () {
            return PaymentHandler::where('active', 1)
                ->get();
        });

        [$matchingHandlers, $otherHandlers] = $this->filterHandlers($paymentHandlers, $user);
        [$matchingSystems, $otherSystems] = $this->filterSystems($paymentHandlers, $user);
        
        View::share([
            'matchingHandlers' => $matchingHandlers,
            'otherHandlers' => $otherHandlers,
            'matchingSystems' => $matchingSystems,
            'otherSystems' => $otherSystems
        ]);
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Filter Handlers by User Currency
     * ═══════════════════════════════════════════════════════════
     */
    protected function filterHandlers(Collection $handlers, User $user): array
    {
        $userCurrency = $user->currency->symbol;
        
        $matching = $handlers->filter(fn($h) => $h->currency === $userCurrency);
        $other = $handlers->diff($matching);
        
        return [$matching, $other];
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Filter Systems by User Currency
     * ═══════════════════════════════════════════════════════════
     */
    protected function filterSystems(Collection $handlers, User $user): array
    {
        return $this->filterHandlers($handlers, $user);
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Check if Should Skip Session Validation
     * ═══════════════════════════════════════════════════════════
     */
    protected function shouldSkipSessionCheck(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/games/') ||
               $request->routeIs('login') ||
               $request->routeIs('login.post');
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Check if Session is Invalid
     * ═══════════════════════════════════════════════════════════
     */
    protected function isInvalidSession(): bool
    {
        return Auth::check() && Auth::user()->last_login_at === null;
    }

    /**
     * ═══════════════════════════════════════════════════════════
     *  Logout and Redirect
     * ═══════════════════════════════════════════════════════════
     */
    protected function logoutAndRedirect()
    {
        Auth::logout();
        
        return redirect()
            ->route('home')
            ->with('error', 'Ваша сессия была завершена. Пожалуйста, войдите снова.');
    }
}
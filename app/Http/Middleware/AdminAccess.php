<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminAccess
{
    protected Guard $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if (!$this->auth->check()) {
            $this->logUnauthorizedAccess($request, 'Неавторизованный доступ');
            return $this->redirectToHome();
        }

        $user = $this->auth->user();

        if (!$this->hasAdminAccess($user)) {
            $this->logUnauthorizedAccess($request, 'Недостаточно прав');
            return $this->redirectToHome();
        }

        if ($this->isRateLimited($request)) {
            $this->logUnauthorizedAccess($request, 'Превышен лимит запросов');
            return $this->redirectToHome();
        }

        // Проверка двухфакторной аутентификации
        if (!$this->isTwoFactorAuthenticated($user)) {
            return redirect()->route('admin.2fa');
        }

        // Обновляем время последней активности
        $this->updateLastActivity($user);

        return $next($request);
    }

    protected function hasAdminAccess($user)
    {
        return $user->is_admin == 1 || $user->is_moder == 1;
    }

    protected function isRateLimited($request)
    {
        $key = 'admin_access_' . $this->auth->id(); // Используем ID пользователя вместо IP
        $maxAttempts = 60; // максимальное количество запросов
        $decayMinutes = 1; // за 1 минуту

        if (Cache::has($key)) {
            $attempts = Cache::increment($key);
            return $attempts > $maxAttempts;
        }

        Cache::put($key, 1, $decayMinutes * 60);
        return false;
    }

    protected function isTwoFactorAuthenticated($user)
    {
        // Здесь реализуйте проверку двухфакторной аутентификации
        // Например, проверка наличия сессии 2FA или специального флага
        return session('2fa_authenticated') === true;
    }

    protected function updateLastActivity($user)
    {
        $user->last_activity = now();
        $user->save();
    }

    protected function logUnauthorizedAccess($request, $reason)
    {
        Log::warning('Попытка несанкционированного доступа к админ-панели', [
            'user_id' => $this->auth->id(),
            'reason' => $reason,
        ]);

    }

    protected function redirectToHome()
    {
        return new RedirectResponse(url('/'));
    }
}

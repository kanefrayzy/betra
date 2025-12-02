<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLastLogout
{
    public function handle(Request $request, Closure $next)
    {
      if (strpos($request->getPathInfo(), '/games/') === 0) {
        return $next($request);
    }
        // Пропускаем проверку для маршрута входа
        if ($request->routeIs('login') || $request->routeIs('login.post')) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->last_login_at === null) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Ваша сессия была завершена. Пожалуйста, войдите снова.');
        }

        return $next($request);
    }
}

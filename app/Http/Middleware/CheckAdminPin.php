<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckAdminPin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $token = $request->route('token');

        Log::info('CheckAdminPin middleware', [
            'user' => $user ? $user->id : 'null',
            'is_admin' => $user ? $user->is_admin : 'null',
            'is_moder' => $user ? $user->is_moder : 'null',
            'token' => $token,
            'admin_pin_verified' => $request->session()->has('admin_pin_verified'),
        ]);

        if (!$user || (!$user->is_admin && !$user->is_moder)) {
            Log::warning('Access denied in CheckAdminPin');
            return redirect('/')->with('error', 'Доступ запрещен.');
        }

        if (!$request->session()->has('admin_pin_verified')) {
            Log::info('Redirecting to PIN form');
            return redirect()->route('admin.pin', ['token' => $token]);
        }

        return $next($request);
    }
}

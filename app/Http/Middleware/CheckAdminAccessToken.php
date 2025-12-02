<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AdminAccessService;
use Illuminate\Support\Facades\Auth;

class CheckAdminAccessToken
{
    protected $adminAccessService;

    public function __construct(AdminAccessService $adminAccessService)
    {
        $this->adminAccessService = $adminAccessService;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->route('token');

        if (!$this->adminAccessService->isValidToken($token)) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login', ['token' => $token])->with('error', 'Недействительный или устаревший токен доступа.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\User\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected $maxAttempts = 5;
    protected $decayMinutes = 10;

    public function __construct(protected AuthService $service) {}

    public function login(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $key = $this->getLimiterKey($request);
        if ($this->checkRateLimit($key)) {
            $seconds = RateLimiter::availableIn($key);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Слишком много попыток входа. Попробуйте через :seconds секунд.', ['seconds' => $seconds])
                ], 429);
            }
            
            return $this->tooManyAttempts($request, $key);
        }

        if ($this->service->login($request->validated())) {
            $user = Auth::user();

            if ($user->ban) {
                Auth::logout();
                
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Ваш аккаунт заблокирован')
                    ], 403);
                }
                
                return back();
            }

            RateLimiter::clear($key);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('С возвращением!'),
                    'redirect' => route('home')
                ]);
            }
            
            return redirect()->intended(route('home'))->with('success', __('С возвращением!'));
        }

        RateLimiter::hit($key, $this->decayMinutes * 60);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('Неверный email или пароль')
            ], 422);
        }

        return $this->failedLogin($key, $request);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('home');
    }

    public function logoutAll(): RedirectResponse
    {
        $this->service->logoutAllUsers();
        return redirect()->route('home')->with('success', 'All users have been logged out successfully');
    }

    private function getLimiterKey(LoginRequest $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }

    private function checkRateLimit(string $key): bool
    {
        return RateLimiter::tooManyAttempts($key, $this->maxAttempts);
    }

    private function tooManyAttempts(LoginRequest $request, string $key): RedirectResponse
    {
        return back()->with('error', __('auth.throttle', [
            'seconds' => RateLimiter::availableIn($key),
        ]));
    }

    private function failedLogin(string $key, LoginRequest $request): RedirectResponse
    {
        RateLimiter::hit($key, $this->decayMinutes * 60);
        return back()
            ->withInput($request->only('email'))
            ->with('error', __('Invalid email or password'));
    }

    private function notifyAboutManyAttempts(LoginRequest $request): void
    {
        //Too many failed login attempts
    }

    private function notifyAboutFailedLogin(LoginRequest $request): void
    {
        //Failed login attempt
    }
}

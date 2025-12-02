<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AesCallbackMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверяем Callback-Token
        $token = $request->header('Callback-Token');
        $expectedToken = config('services.aes.callback_token');

        Log::info('AES Callback Token Check', [
            'received_token' => $token,
            'expected_token' => $expectedToken,
            'ip' => $request->ip()
        ]);

        if ($token !== $expectedToken) {
            Log::error('Invalid AES Callback Token', [
                'received' => $token,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'code' => 1009,
                'message' => 'TOKEN_INVALID',
                'data' => ['balance' => 0]
            ], 401);
        }

        return $next($request);
    }
}

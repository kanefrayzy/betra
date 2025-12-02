<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class GameTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $gameToken = $request->header('X-Game-Token') ?? $request->input('game_token');

        if (!$gameToken) {
            return response()->json([
                'success' => false,
                'message' => 'Game token not provided'
            ], 401);
        }

        $user = User::where('game_token', $gameToken)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid game token'
            ], 401);
        }

        // Добавляем пользователя в запрос
        $request->merge(['user' => $user]);

        return $next($request);
    }
}

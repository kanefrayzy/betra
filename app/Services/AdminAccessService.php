<?php

namespace App\Services;

use App\Models\AdminAccessToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminAccessService
{
    public function generateNewTokenAndPin()
    {
        $token = Str::random(40);
        $pin = sprintf('%06d', mt_rand(0, 999999)); // Генерируем 6-значный PIN
        $expiresAt = Carbon::now()->addDay();

        AdminAccessToken::query()->delete(); // Удаляем старые токены

        $newToken = AdminAccessToken::create([
            'token' => $token,
            'pin' => $pin,
            'expires_at' => $expiresAt,
        ]);

        return $newToken;
    }

    public function isValidToken($token)
    {
        return AdminAccessToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }

    public function isValidPin($token, $pin)
    {
        return AdminAccessToken::where('token', $token)
            ->where('pin', $pin)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }

    public function getCurrentTokenAndPin()
    {
        $currentToken = AdminAccessToken::where('expires_at', '>', Carbon::now())->first();

        if (!$currentToken) {
            $currentToken = $this->generateNewTokenAndPin();
        }

        return $currentToken;
    }
}

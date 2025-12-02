<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Currency;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    public function create(array $data): User
    {
        $userId = $this->generateUserId();
        $currencyId = $this->resolveCurrencyId($data);
        $initialBalance = $this->calculateWelcomeBonus($currencyId);

        return User::create([
            'user_id' => $userId,
            'username' => $data['username'] ?? "User{$userId}",
            'email' => $data['email'] ?? "{$userId}@flashgame.live",
            'password' => Hash::make($data['password'] ?? ''),
            'currency_id' => $currencyId,
            'balance' => $initialBalance,
            'avatar' => $data['avatar'] ?? $this->defaultAvatar(),
            'affiliate_id' => Str::random(10),
            'ip' => getClientIp(),
            'referred_by' => $this->getValidAffiliateId(),
            'network_id' => $data['network_id'] ?? null,
            'network_type' => $data['network_type'] ?? null,
            'telegram_id' => $data['telegram_id'] ?? null,
            'last_login_at' => now(),
        ]);
    }

    public function authorize($user): bool
    {
        if ($user instanceof User) {
            return $this->authorizeUser($user);
        }

        if (is_array($user)) {
            $foundUser = $this->findByCredentials($user);
            return $foundUser ? $this->authorizeUser($foundUser) : false;
        }

        return false;
    }

    public function findByCredentials(array $credentials): ?User
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return $user;
        }

        return null;
    }

    public function updateIp(User $user, ?string $ip): bool
    {
        return $user->update(['ip' => $ip]);
    }

    public function clearAffiliateCookie(?string $referredBy): void
    {
        if ($referredBy && Cookie::has('affiliate_id')) {
            Cookie::forget('affiliate_id');
        }
    }

    public function logFailedAttempt(array $credentials): void
    {
        // Failed login attempt
    }

    public function logoutAllUsers(): void
    {
        User::query()->update(['last_login_at' => null]);
        Cache::tags(['users', 'sessions'])->flush();
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->notifyAboutLogout();
    }

    private function authorizeUser(User $user): bool
    {
        Auth::login($user, true);
        return Auth::check();
    }

    private function generateUserId(): string
    {
        return '00' . rand(100000, 999999);
    }

    private function defaultAvatar(): string
    {
        return '/assets/images/avatar-placeholder.png';
    }

    private function getValidAffiliateId(): ?string
    {
        $affiliateId = Request::cookie('affiliate_id');
        $referrer = $affiliateId ? $this->findByAffiliateId($affiliateId) : null;
        return $referrer ? $referrer->user_id : null;
    }

    private function findByAffiliateId(string $affiliateId): ?User
    {
        return User::where('affiliate_id', $affiliateId)->first();
    }

    private function resolveCurrencyId(array $data): int
    {
        if (isset($data['currency_id']) && is_numeric($data['currency_id'])) {
            return (int) $data['currency_id'];
        }

        return $this->getCurrencyId($data['currency'] ?? 'USD');
    }

    private function getCurrencyId(string $currencyName): int
    {
        $currency = Currency::where('symbol', $currencyName)
            ->orWhere('name', $currencyName)
            ->first();

        if (!$currency) {
            $currency = Currency::first();
        }

        return $currency->id;
    }

    private function calculateWelcomeBonus(int $currencyId): float
    {
        $settings = Settings::first();

        if (!$this->isWelcomeBonusEnabled($settings)) {
            return 0.0;
        }

        $currency = Currency::with('rate')->find($currencyId);
        $bonusAmount = $settings->welcome_bonus_amount;

        if ($this->hasCurrencyRate($currency)) {
            $convertedBonus = $bonusAmount * $currency->rate->price;
            
            $this->logWelcomeBonusApplied($currency, $bonusAmount, $convertedBonus);
            
            return $convertedBonus;
        }

        $this->logWelcomeBonusFallback($currencyId, $bonusAmount, $currency);

        return $bonusAmount;
    }

    private function isWelcomeBonusEnabled(?Settings $settings): bool
    {
        return $settings 
            && $settings->welcome_bonus_enabled 
            && $settings->welcome_bonus_amount > 0;
    }

    private function hasCurrencyRate(?Currency $currency): bool
    {
        return $currency 
            && $currency->rate 
            && $currency->rate->price > 0;
    }

    private function logWelcomeBonusApplied(Currency $currency, float $bonusUsd, float $convertedBonus): void
    {
        Log::info('Welcome bonus applied', [
            'user_currency' => $currency->symbol,
            'bonus_usd' => $bonusUsd,
            'rate' => $currency->rate->price,
            'bonus_converted' => $convertedBonus
        ]);
    }

    private function logWelcomeBonusFallback(int $currencyId, float $bonus, ?Currency $currency): void
    {
        Log::warning('Currency rate not found, using bonus as is', [
            'currency_id' => $currencyId,
            'bonus' => $bonus,
            'rate_exists' => $currency && $currency->rate ? 'yes' : 'no',
            'rate_price' => $currency && $currency->rate ? $currency->rate->price : null
        ]);
    }

    private function notifyAboutLogout(): void
    {
        // Принудительный выход из системы для всех
    }

    public function updatePassword(User $user, string $password): bool
    {
        $user->password = Hash::make($password);
        return $user->save();
    }
}

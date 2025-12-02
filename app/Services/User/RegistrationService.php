<?php

namespace App\Services\User;

use App\Models\User;
use App\Notifications\Notify;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistrationService
{
    protected ?User $user = null;

    public function __construct(protected UserRepository $repository) {}

    public function register(array $userData): bool
    {
        return DB::transaction(function () use ($userData) {
            $user = $this->repository->create($userData);
            $this->setUser($user);
            $this->repository->clearAffiliateCookie($this->user->referred_by);
            
            if ($this->shouldSendWelcomeBonus($user)) {
                $this->sendWelcomeBonusNotification($user);
            }
            
            return $this->repository->authorize($user);
        });
    }

    public function user(): ?User
    {
        return $this->user;
    }

    protected function setUser(User $user): void
    {
        $this->user = $user;
    }
    
    protected function shouldSendWelcomeBonus(User $user): bool
    {
        return $user->balance > 0;
    }
    
    protected function sendWelcomeBonusNotification(User $user): void
    {
        try {
            $message = $this->formatWelcomeBonusMessage($user);
            
            $user->notify(Notify::send('bonus', ['message' => $message]));
            
            $this->logWelcomeBonusNotification($user);
        } catch (\Exception $e) {
            $this->logWelcomeBonusError($user, $e);
        }
    }
    
    protected function formatWelcomeBonusMessage(User $user): string
    {
        $amount = number_format($user->balance, 2);
        $currency = $user->currency->symbol ?? 'USD';
        
        return __('🎉 Добро пожаловать! Вы получили приветственный бонус :amount :currency', [
            'amount' => $amount,
            'currency' => $currency
        ]);
    }
    
    protected function logWelcomeBonusNotification(User $user): void
    {
        Log::info('Welcome bonus notification sent', [
            'user_id' => $user->id,
            'bonus' => number_format($user->balance, 2),
            'currency' => $user->currency->symbol ?? 'USD'
        ]);
    }
    
    protected function logWelcomeBonusError(User $user, \Exception $e): void
    {
        Log::error('Failed to send welcome bonus notification', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);
    }
}

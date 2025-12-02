<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;

class AuthService
{
    public function __construct(protected UserRepository $repository) {}

    public function login(array $credentials): bool
    {
        $user = $this->repository->findByCredentials($credentials);
        if ($user && $this->repository->authorize($user)) {
            $this->updateUserLoginInfo($user);
            return true;
        }
        $this->repository->logFailedAttempt($credentials);
        return false;
    }

    public function uLogin(User $user): bool
    {
        if ($this->repository->authorize($user)) {
            $this->updateUserLoginInfo($user);
            return true;
        }
        return false;
    }

    public function logoutAllUsers(): void
    {
        $this->repository->logoutAllUsers();
        Cache::tags(['users', 'sessions'])->flush();
    }

    private function updateUserLoginInfo(User $user): void
    {
        $this->repository->updateIp($user, getClientIp());
        $this->repository->clearAffiliateCookie($user->referred_by);
        $user->update(['last_login_at' => now()]);
    }
}

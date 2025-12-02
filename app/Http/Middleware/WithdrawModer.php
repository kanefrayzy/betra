<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;

/** @mixin User */

class WithdrawModer
{
    protected Guard $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            if ($this->auth->user()->is_withdraw_moder == 1 || $this->auth->user()->is_admin == 1) {
                return $next($request);
            }
        }

        return new RedirectResponse(url('/'));

    }
}

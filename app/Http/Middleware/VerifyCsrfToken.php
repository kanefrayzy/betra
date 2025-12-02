<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'Admin/*',
        'api/*',
        'Admin/*',
    	'auth/ulogin',
        'auth/telegram-webview',
        'telegram/webhook',
    	'games/slotegrator/callback',
        'games/tbs2/*',
        'gold_api/*',
        'deploy/webhook',
    ];

	protected function tokensMatch($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if ( ! $token && $header = $request->header('X-XSRF-TOKEN'))
        {
            $token = $this->encrypter->decrypt($header);
        }

        return true;
        //return StringUtils::equals($request->session()->token(), $token);
    }
}

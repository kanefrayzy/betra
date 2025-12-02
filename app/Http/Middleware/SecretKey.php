<?php

namespace App\Http\Middleware;

use Closure;

class SecretKey
{

    public function handle($request, Closure $next): mixed
    {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
        if($ip != $_SERVER['SERVER_ADDR']) return response()->json('Invalid Request');
        return $next($request);
    }
}

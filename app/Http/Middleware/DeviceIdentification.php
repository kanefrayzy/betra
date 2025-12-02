<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Facades\Agent;
use Symfony\Component\HttpFoundation\Response;

class DeviceIdentification
{


    public function handle(Request $request, Closure $next): Response
    {

        if (Agent::isMobile()) {
            $deviceType = 'mobile';
        } elseif (Agent::isTablet()) {
            $deviceType = 'tablet';
        } else {
            $deviceType = 'desktop';
        }

        Session::put('device_type', $deviceType);

        return $next($request);
    }
}

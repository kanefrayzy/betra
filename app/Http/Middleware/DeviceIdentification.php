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
        // Проверяем, нужно ли определять устройство
        if (!Session::has('device_type')) {
            $deviceType = $this->detectDeviceType();
            Session::put('device_type', $deviceType);
        }

        return $next($request);
    }

    /**
     * Определяет тип устройства
     */
    private function detectDeviceType(): string
    {
        if (Agent::isMobile()) {
            return 'mobile';
        }
        
        if (Agent::isTablet()) {
            return 'tablet';
        }
        
        return 'desktop';
    }
}
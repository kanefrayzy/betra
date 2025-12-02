<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Settings;

class CheckForMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        $allowed_ips = ['45.10.243.5', '45.10.243.5'];
//
//        if (App::isDownForMaintenance() && !in_array($request->ip(), $allowed_ips)) {
//            return response()->view('errors.503', [], 503);
//        }
        
        $settings = Settings::first();
        $allowed_ips = [];
        
        if ($settings && $settings->ip_maintenance) {
            $allowed_ips = explode(',', $settings->ip_maintenance);
        }
        
        if (App::isDownForMaintenance() && !in_array($request->ip(), $allowed_ips)) {
            return response()->view('errors.503', ['settings' => $settings], 503);
        }

        return $next($request);
    }
}

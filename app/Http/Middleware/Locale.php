<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Services\GeoIPService;

class Locale
{
    protected $geoService;

    public function __construct(GeoIPService $geoService)
    {
        $this->geoService = $geoService;
    }

    public function handle($request, Closure $next)
    {
        $locale = Session::get('locale') ?: Cookie::get('locale');

        if (!$locale) {
            $ip = $request->ip();

            $country = $this->geoService->getCountry($ip);
            $locale = $this->determineLocaleFromLocation($country);

            if (!$locale) {
                $browserLocale = $request->server('HTTP_ACCEPT_LANGUAGE');
                $locale = $this->parseLocale($browserLocale);
            }
        }

        $locale = in_array($locale, Config::get('app.locales')) ? $locale : Config::get('app.locale');

        App::setLocale($locale);
        Session::put('locale', $locale);
        Cookie::queue('locale', $locale, 60 * 24 * 365);

        return $next($request);
    }

    /**
     * Parse language from header HTTP_ACCEPT_LANGUAGE.
     *
     * @param string|null $header
     * @return string
     */
    protected function parseLocale($header)
    {
        if (!$header) {
            return Config::get('app.locale');
        }

        $locales = explode(',', $header);
        $supportedLocales = Config::get('app.locales');

        foreach ($locales as $locale) {
            $locale = strtok($locale, ';');
            $locale = strtok($locale, '-');
            $locale = str_replace('-', '_', $locale);

            if (in_array($locale, $supportedLocales)) {
                return $locale;
            }
        }

        return Config::get('app.locale');
    }

    /**
     * Determine locale from the user's country.
     *
     * @param $country
     * @return string|bool
     */
    protected function determineLocaleFromLocation($country)
    {
        if (!$country) return false;

        $isoCode = $country->isoCode ?? false;

        if (!$isoCode) return false;

        $localeMapping = Config::get('geoip.country_locale');
        $locale = $localeMapping[$isoCode] ?? false;

        if ($locale && in_array($locale, Config::get('app.locales'))) {
            return $locale;
        }

        return false;
    }
}

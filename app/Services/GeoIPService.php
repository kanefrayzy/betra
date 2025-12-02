<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoIPService
{
    protected $countryReader;
    protected $cityReader;

    public function __construct()
    {
        $this->initializeReaders();
    }

    protected function initializeReaders()
    {
        $this->countryReader = $this->createReader(config('geoip.country_database_path'), 'country');
        $this->cityReader = $this->createReader(config('geoip.city_database_path'), 'city');
    }

    protected function createReader($path, $type)
    {
        try {
            return new Reader($path);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCountry($ip)
    {
        try {
            return $this->countryReader->country($ip)->country;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCity($ip)
    {
        try {
            return $this->cityReader->city($ip)->city;
        } catch (\Exception $e) {
            return null;
        }
    }
}

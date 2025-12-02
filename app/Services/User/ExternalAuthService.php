<?php

namespace App\Services\User {

    use Illuminate\Support\Facades\Http;

    class ExternalAuthService
    {
        protected string $endpoint;

        public function __construct(string $endpoint)
        {
            $this->endpoint = $endpoint;
        }

        public function getUserData(string $token): ?object
        {
            $response = Http::get($this->endpoint, [
                'token' => $token,
                'host' => host(),
            ]);

            if ($response->successful()) {
                return $response->object();
            }

            return null;
        }
    }
}

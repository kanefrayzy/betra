<?php

namespace App\Services\Slotegrator;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SlotegratorClient
{
    protected string $baseUrl;
    protected string $merchantId;
    protected string $merchantKey;
    protected Client $client;

    public function __construct()
    {
        $this->baseUrl = config('services.slotegrator.base_url');
        $this->merchantId = config('services.slotegrator.merchant_id');
        $this->merchantKey = config('services.slotegrator.merchant_key');
        $this->client = new Client();
    }

    public function get($endpoint, $params = [])
    {
        $response = $this->client->get($this->baseUrl . $endpoint, [
            'query' => $params,
            'headers' => $this->generateHeaders($params),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function post(string $endpoint, array $params = [])
    {
        try {
            $options = [
                'json' => $params, // Используем json вместо form_params
                'headers' => $this->generateHeaders($params),
                'http_errors' => false // Чтобы обрабатывать ошибки самостоятельно
            ];

            // Добавляем Bearer токен если требуется
            if ($this->merchantKey === null && config('services.slotegrator.api_key')) {
                $options['headers']['Authorization'] = 'Bearer '.config('services.slotegrator.api_key');
            }

            $response = $this->client->post($this->baseUrl . $endpoint, $options);

            // Обработка ошибок
            if ($response->getStatusCode() !== 200) {
                $body = $response->getBody()->getContents();
                Log::error('Slotegrator API error', [
                    'status' => $response->getStatusCode(),
                    'response' => $body,
                    'endpoint' => $endpoint
                ]);
                throw new \Exception("API request failed: ".$body);
            }

            return json_decode($body, true) ?: $body;

        } catch (GuzzleException $e) {
            Log::error('Slotegrator connection error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception("Service unavailable");
        }
    }

    private function generateHeaders(array $requestParams): array
    {
        $timestamp = Carbon::now()->timestamp;
        $nonce = Str::random(16);

        $headers = [
            'X-Merchant-Id' => $this->merchantId,
            'X-Timestamp' => $timestamp,
            'X-Nonce' => $nonce,
        ];

        $mergedParams = array_merge($requestParams, $headers);
        ksort($mergedParams);

        $hashString = http_build_query($mergedParams);
        $xSign = hash_hmac('sha1', $hashString, $this->merchantKey);

        return [
            'X-Merchant-Id' => $this->merchantId,
            'X-Timestamp' => $timestamp,
            'X-Nonce' => $nonce,
            'X-Sign' => $xSign,
        ];
    }


    public function verifySignature(array $headers, array $requestParams): void
    {
        $headers = array_change_key_case($headers, CASE_LOWER);

        if (empty($headers['x-merchant-id'][0]) || empty($headers['x-timestamp'][0]) || empty($headers['x-nonce'][0]) || empty($headers['x-sign'][0])) {
            throw new \Exception('Missing required headers for signature verification');
        }

        $verificationHeaders = [
            'X-Merchant-Id' => $headers['x-merchant-id'][0],
            'X-Timestamp' => $headers['x-timestamp'][0],
            'X-Nonce' => $headers['x-nonce'][0],
        ];

        $xSign = $headers['x-sign'][0];
        $mergedParams = array_merge($requestParams, $verificationHeaders);
        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);
        $expectedSign = hash_hmac('sha1', $hashString, $this->merchantKey);

        if ($xSign !== $expectedSign) {
            throw new \Exception('Invalid sign');
        }
    }

    public function selfValidate()
    {
        $params = [];
        return $this->post('/self-validate', $params);
    }

}

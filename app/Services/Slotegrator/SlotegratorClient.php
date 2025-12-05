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
        
        // Настройки HTTP клиента с timeout
        $this->client = new Client([
            'timeout' => 2.5,
            'connect_timeout' => 1.0,
        ]);
    }

    public function get($endpoint, $params = [])
    {
        try {
            $headers = $this->generateHeaders($params);
            
            // Логируем запрос
            Log::channel('slots')->info('Slotegrator GET Request', [
                'url' => $this->baseUrl . $endpoint,
                'endpoint' => $endpoint,
                'params' => $params,
                'headers' => $headers,
                'timestamp' => now()->toDateTimeString()
            ]);
            
            $response = $this->client->get($this->baseUrl . $endpoint, [
                'query' => $params,
                'headers' => $headers,
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            // Логируем ответ
            Log::channel('slots')->info('Slotegrator GET Response', [
                'endpoint' => $endpoint,
                'status' => $statusCode,
                'response' => $body,
                'response_headers' => $response->getHeaders()
            ]);

            if ($statusCode !== 200) {
                Log::error('Slotegrator GET error', [
                    'endpoint' => $endpoint,
                    'status' => $statusCode,
                    'response' => $body,
                    'request_params' => $params,
                    'request_headers' => $headers
                ]);
                throw new Exception("API GET request failed with status {$statusCode}: {$body}");
            }

            return json_decode($body, true);

        } catch (GuzzleException $e) {
            Log::error('Slotegrator GET connection error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw new Exception("Service unavailable: " . $e->getMessage());
        }
    }

    public function post(string $endpoint, array $params = [])
    {
        try {
            $headers = $this->generateHeaders($params);
            
            // Логируем запрос
            Log::channel('slots')->info('Slotegrator POST Request', [
                'url' => $this->baseUrl . $endpoint,
                'endpoint' => $endpoint,
                'params' => $params,
                'headers' => $headers,
                'timestamp' => now()->toDateTimeString()
            ]);
            
            // application/x-www-form-urlencoded
            $options = [
                'form_params' => $params,
                'headers' => $headers,
                'http_errors' => false
            ];

            $response = $this->client->post($this->baseUrl . $endpoint, $options);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            // Логируем ответ
            Log::channel('slots')->info('Slotegrator POST Response', [
                'endpoint' => $endpoint,
                'status' => $statusCode,
                'response' => $body,
                'response_headers' => $response->getHeaders()
            ]);

            if ($statusCode !== 200) {
                Log::error('Slotegrator POST error', [
                    'endpoint' => $endpoint,
                    'status' => $statusCode,
                    'response' => $body,
                    'request_params' => $params,
                    'request_headers' => $headers
                ]);
                throw new Exception("API POST request failed with status {$statusCode}: {$body}");
            }

            $decoded = json_decode($body, true);
            return $decoded !== null ? $decoded : $body;

        } catch (GuzzleException $e) {
            Log::error('Slotegrator POST connection error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            throw new Exception("Service unavailable: " . $e->getMessage());
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
        
        // Логируем процесс генерации подписи (без ключа!)
        Log::channel('slots')->debug('Slotegrator signature generation', [
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'merchant_id' => $this->merchantId,
            'hash_string' => $hashString,
            'signature' => $xSign,
            'request_params' => $requestParams
        ]);

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
            throw new Exception('Missing required headers for signature verification');
        }

        $verificationHeaders = [
            'X-Merchant-Id' => $headers['x-merchant-id'][0],
            'X-Timestamp' => $headers['x-timestamp'][0],
            'X-Nonce' => $headers['x-nonce'][0],
        ];

        // Проверка timestamp (макс 30 секунд разница по документации)
        $timestamp = (int)$headers['x-timestamp'][0];
        $currentTimestamp = Carbon::now()->timestamp;
        $timeDifference = abs($currentTimestamp - $timestamp);

        if ($timeDifference > 30) {
            Log::warning('Slotegrator request expired', [
                'request_timestamp' => $timestamp,
                'current_timestamp' => $currentTimestamp,
                'difference' => $timeDifference
            ]);
            throw new Exception('Request expired - timestamp difference exceeds 30 seconds');
        }

        // Проверка подписи
        $xSign = $headers['x-sign'][0];
        $mergedParams = array_merge($requestParams, $verificationHeaders);
        ksort($mergedParams);
        $hashString = http_build_query($mergedParams);
        $expectedSign = hash_hmac('sha1', $hashString, $this->merchantKey);

        if ($xSign !== $expectedSign) {
            Log::error('Slotegrator invalid signature', [
                'expected' => $expectedSign,
                'received' => $xSign,
                'merchant_id' => $headers['x-merchant-id'][0] ?? 'unknown'
            ]);
            throw new Exception('Invalid signature');
        }
    }

    public function selfValidate()
    {
        $params = [];
        return $this->post('/self-validate', $params);
    }

}

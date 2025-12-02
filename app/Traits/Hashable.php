<?php

namespace App\Traits;

trait Hashable
{
    public function hash(string $value): string
    {
        return hash('sha256', $value);
    }

    public function check(string $value, string $hash): bool
    {
        return hash_equals(hash('sha256', $value), $hash);
    }

    public function encode(array $data): string
    {
        $json = json_encode($data);
        return hash('sha256', $json);
    }

    public function decode(string $encoded): ?array
    {
        $decoded = json_decode(base64_decode($encoded), true);

        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }
}

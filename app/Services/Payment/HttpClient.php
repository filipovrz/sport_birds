<?php

declare(strict_types=1);

namespace App\Services\Payment;

final class HttpClient
{
    /** @param array<string, string> $headers */
    public static function post(string $url, string $body, array $headers = []): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $response = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $code,
            'body' => is_string($response) ? $response : '',
        ];
    }

    /** @param array<string, string> $headers */
    public static function postJson(string $url, array $data, array $headers = []): array
    {
        $headers[] = 'Content-Type: application/json';

        return self::post($url, json_encode($data, JSON_UNESCAPED_UNICODE), $headers);
    }

    /** @param array<string, string> $headers */
    public static function get(string $url, array $headers = []): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $response = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $code,
            'body' => is_string($response) ? $response : '',
        ];
    }
}

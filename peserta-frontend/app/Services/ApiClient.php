<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Pembungkus pemanggilan HTTP ke API Go.
 * Selalu mengembalikan array seragam: ok, status, message, data, errors.
 */
class ApiClient
{
    public function request(string $method, string $path, array $data = [], array $query = []): array
    {
        $url = rtrim(config('api.base_url'), '/') . '/' . ltrim($path, '/');

        try {
            $http = Http::acceptJson()->timeout(config('api.timeout', 10));

            if ($token = session('api_token')) {
                $http = $http->withToken($token);
            }

            $response = match (strtoupper($method)) {
                'GET'    => $http->get($url, $query),
                'POST'   => $http->post($url, $data),
                'PUT'    => $http->put($url, $data),
                'DELETE' => $http->delete($query ? $url . '?' . http_build_query($query) : $url),
                default  => throw new \InvalidArgumentException('Method tidak didukung'),
            };

            $body = $response->json() ?? [];

            return [
                'ok'      => $response->successful(),
                'status'  => $response->status(),
                'message' => $body['message'] ?? 'Terjadi kesalahan pada server API.',
                'data'    => $body['data'] ?? null,
                'errors'  => is_array($body['errors'] ?? null) ? $body['errors'] : [],
            ];
        } catch (ConnectionException $e) {
            return [
                'ok'      => false,
                'status'  => 0,
                'message' => 'Tidak dapat terhubung ke server API. Pastikan backend Go sedang berjalan.',
                'data'    => null,
                'errors'  => [],
            ];
        }
    }
}

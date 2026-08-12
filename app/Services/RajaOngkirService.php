<?php

namespace App\Services;

use App\Data\ShippingLocation;
use App\Exceptions\RajaOngkirException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    public function provinces(): array
    {
        return $this->rememberRegions('provinces', fn () => $this->get('/destination/province'));
    }

    public function cities(string|int $provinceId): array
    {
        return $this->rememberRegions(
            'cities.'.(string) $provinceId,
            fn () => $this->get('/destination/city/'.rawurlencode((string) $provinceId)),
        );
    }

    public function districts(string|int $cityId): array
    {
        return $this->rememberRegions(
            'districts.'.(string) $cityId,
            fn () => $this->get('/destination/district/'.rawurlencode((string) $cityId)),
        );
    }

    public function subdistricts(string|int $districtId): array
    {
        return $this->rememberRegions(
            'subdistricts.'.(string) $districtId,
            fn () => $this->get('/destination/sub-district/'.rawurlencode((string) $districtId)),
        );
    }

    public function rates(
        ShippingLocation $origin,
        ShippingLocation $destination,
        int $weightGrams,
        string $courier,
    ): array
    {
        if (! in_array($courier, config('rajaongkir.default_couriers', []), true)) {
            throw new RajaOngkirException('Kurir tidak didukung.');
        }

        $response = $this->send(fn (PendingRequest $client) => $client->asForm()->post(
            $this->url('/calculate/domestic-cost'),
            [
                'origin' => $origin->providerId,
                'destination' => $destination->providerId,
                'weight' => max(1, $weightGrams),
                'courier' => $courier,
            ],
        ));

        $this->ensureSuccessful($response, 'menghitung ongkos kirim');

        return $this->responseData($response, 'ongkos kirim');
    }

    public function authoritativeRate(
        ShippingLocation $origin,
        ShippingLocation $destination,
        int $weightGrams,
        string $courier,
        string $serviceCode
    ): array {
        $rate = collect($this->rates($origin, $destination, $weightGrams, $courier))
            ->first(fn (array $item) => (string) ($item['service'] ?? '') === $serviceCode);

        if (! $rate || (int) ($rate['cost'] ?? 0) < 0) {
            throw new RajaOngkirException('Layanan pengiriman yang dipilih tidak lagi tersedia.');
        }

        return $rate;
    }

    private function get(string $path): array
    {
        $response = $this->send(fn (PendingRequest $client) => $client->get($this->url($path)));

        $this->ensureSuccessful($response, 'memuat data wilayah');

        return $this->responseData($response, 'data wilayah');
    }

    private function client(): PendingRequest
    {
        $key = trim((string) config('rajaongkir.api_key'));

        if ($key === '' || $this->isPlaceholderKey($key)) {
            throw new RajaOngkirException('API key RajaOngkir belum diisi. Perbarui RAJAONGKIR_API_KEY pada file .env, lalu bersihkan cache konfigurasi.');
        }

        return Http::acceptJson()
            ->withHeaders(['key' => $key])
            ->timeout(config('rajaongkir.timeout', 15))
            ->retry(2, 250, throw: false);
    }

    private function url(string $path): string
    {
        $baseUrl = rtrim(trim((string) config('rajaongkir.base_url')), '/');

        if (! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            throw new RajaOngkirException('RAJAONGKIR_BASE_URL tidak valid. Gunakan URL API resmi RajaOngkir.');
        }

        return $baseUrl.'/'.ltrim($path, '/');
    }

    private function send(callable $request): Response
    {
        try {
            return $request($this->client());
        } catch (ConnectionException $exception) {
            throw new RajaOngkirException(
                'Server tidak dapat terhubung ke RajaOngkir. Periksa koneksi internet dan konfigurasi SSL/cURL PHP.',
                previous: $exception,
            );
        }
    }

    private function ensureSuccessful(Response $response, string $operation): void
    {
        if ($response->successful()) {
            return;
        }

        Log::warning('Permintaan RajaOngkir gagal.', [
            'operation' => $operation,
            'status' => $response->status(),
            'provider_message' => $response->json('meta.message')
                ?? $response->json('message')
                ?? $response->json('errors'),
        ]);

        $message = match (true) {
            in_array($response->status(), [401, 403], true) => 'API key RajaOngkir tidak valid atau belum memiliki akses ke API Shipping Cost.',
            $response->status() === 404 => 'Endpoint RajaOngkir tidak ditemukan. Periksa RAJAONGKIR_BASE_URL.',
            $response->status() === 422 => 'RajaOngkir menolak data alamat atau pengiriman yang dikirim.',
            $response->status() === 429 => 'Batas permintaan RajaOngkir telah tercapai. Silakan coba kembali beberapa saat lagi.',
            $response->serverError() => 'Layanan RajaOngkir sedang bermasalah. Silakan coba kembali beberapa saat lagi.',
            default => "RajaOngkir gagal {$operation} (HTTP {$response->status()}).",
        };

        throw new RajaOngkirException($message);
    }

    private function responseData(Response $response, string $context): array
    {
        $payload = $response->json();

        if (! is_array($payload)) {
            Log::warning('RajaOngkir mengembalikan respons non-JSON.', [
                'context' => $context,
                'status' => $response->status(),
                'content_type' => $response->header('Content-Type'),
            ]);

            throw new RajaOngkirException(
                'RajaOngkir mengembalikan respons yang tidak dapat dibaca. Periksa RAJAONGKIR_BASE_URL atau coba kembali beberapa saat lagi.'
            );
        }

        $this->ensureProviderSuccessful($payload, $context);

        $data = $this->extractData($payload);

        if (! is_array($data)) {
            Log::warning('Format respons RajaOngkir tidak sesuai.', [
                'context' => $context,
                'status' => $response->status(),
                'content_type' => $response->header('Content-Type'),
                'top_level_keys' => array_keys($payload),
                'meta_code' => data_get($payload, 'meta.code'),
                'meta_status' => data_get($payload, 'meta.status'),
            ]);

            throw new RajaOngkirException("Format respons {$context} dari RajaOngkir tidak valid.");
        }

        return array_values($data);
    }

    private function ensureProviderSuccessful(array $payload, string $context): void
    {
        $status = data_get($payload, 'meta.status');
        $code = data_get($payload, 'meta.code');

        $failedStatus = $status === false
            || in_array(strtolower((string) $status), ['error', 'failed', 'failure'], true);
        $failedCode = is_numeric($code) && (int) $code >= 400;

        if (! $failedStatus && ! $failedCode) {
            return;
        }

        $providerMessage = data_get($payload, 'meta.message')
            ?? data_get($payload, 'message')
            ?? 'Unknown provider error';
        $providerCode = is_numeric($code) ? (int) $code : null;

        Log::warning('RajaOngkir menolak permintaan pada payload.', [
            'context' => $context,
            'provider_code' => $providerCode,
            'provider_status' => $status,
            'provider_message' => $providerMessage,
        ]);

        $normalizedMessage = strtolower((string) $providerMessage);
        $message = match (true) {
            in_array($providerCode, [401, 403], true),
            str_contains($normalizedMessage, 'api key'),
            str_contains($normalizedMessage, 'unauth') => 'API key RajaOngkir tidak valid atau belum memiliki akses ke API Shipping Cost.',
            $providerCode === 404 => "Data RajaOngkir untuk {$context} tidak ditemukan.",
            $providerCode === 429,
            str_contains($normalizedMessage, 'limit'),
            str_contains($normalizedMessage, 'quota') => 'Batas permintaan RajaOngkir telah tercapai. Silakan coba kembali beberapa saat lagi.',
            $providerCode !== null && $providerCode >= 500 => 'Layanan RajaOngkir sedang bermasalah. Silakan coba kembali beberapa saat lagi.',
            default => "RajaOngkir gagal memproses {$context}. Periksa konfigurasi dan data yang dikirim.",
        };

        throw new RajaOngkirException($message);
    }

    private function extractData(array $payload): mixed
    {
        foreach (['data.results', 'data.data', 'data', 'results', 'rajaongkir.results'] as $path) {
            $value = data_get($payload, $path);

            if (is_array($value)) {
                return $value;
            }
        }

        return null;
    }

    private function rememberRegions(string $key, callable $loader): array
    {
        $ttl = max(0, (int) config('rajaongkir.region_cache_ttl', 86400));

        if ($ttl === 0) {
            return $loader();
        }

        return Cache::remember('rajaongkir.regions.v2.'.$key, $ttl, $loader);
    }

    private function isPlaceholderKey(string $key): bool
    {
        return in_array(strtoupper($key), [
            'YOUR_API_KEY_HERE',
            'YOUR_RAJAONGKIR_API_KEY',
            'CHANGE_ME',
        ], true);
    }
}

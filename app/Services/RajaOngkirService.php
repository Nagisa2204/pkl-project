<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RajaOngkirService
{
    public function provinces(): array
    {
        return $this->get('/destination/province');
    }

    public function cities(string|int $provinceId): array
    {
        return $this->get('/destination/city/'.rawurlencode((string) $provinceId));
    }

    public function districts(string|int $cityId): array
    {
        return $this->get('/destination/district/'.rawurlencode((string) $cityId));
    }

    public function subdistricts(string|int $districtId): array
    {
        return $this->get('/destination/sub-district/'.rawurlencode((string) $districtId));
    }

    public function rates(int $destinationId, int $weightGrams, string $courier): array
    {
        if (! in_array($courier, config('rajaongkir.default_couriers', []), true)) {
            throw new RuntimeException('Kurir tidak didukung.');
        }

        $origin = config('rajaongkir.origin_id');

        if (! $origin) {
            throw new RuntimeException('RAJAONGKIR_ORIGIN_ID belum dikonfigurasi.');
        }

        $response = $this->client()->asForm()->post($this->url('/calculate/domestic-cost'), [
            'origin' => $origin,
            'destination' => $destinationId,
            'weight' => max(1, $weightGrams),
            'courier' => $courier,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('RajaOngkir gagal menghitung ongkos kirim.');
        }

        return array_values($response->json('data', []));
    }

    public function authoritativeRate(
        int $destinationId,
        int $weightGrams,
        string $courier,
        string $serviceCode
    ): array {
        $rate = collect($this->rates($destinationId, $weightGrams, $courier))
            ->first(fn (array $item) => (string) ($item['service'] ?? '') === $serviceCode);

        if (! $rate || (int) ($rate['cost'] ?? 0) < 0) {
            throw new RuntimeException('Layanan pengiriman yang dipilih tidak lagi tersedia.');
        }

        return $rate;
    }

    private function get(string $path): array
    {
        $response = $this->client()->get($this->url($path));

        if (! $response->successful()) {
            throw new RuntimeException('RajaOngkir tidak dapat memuat data alamat.');
        }

        return array_values($response->json('data', []));
    }

    private function client(): PendingRequest
    {
        $key = config('rajaongkir.api_key');

        if (! $key) {
            throw new RuntimeException('RAJAONGKIR_API_KEY belum dikonfigurasi.');
        }

        return Http::acceptJson()
            ->withHeaders(['key' => $key])
            ->timeout(config('rajaongkir.timeout', 15))
            ->retry(2, 250, throw: false);
    }

    private function url(string $path): string
    {
        return config('rajaongkir.base_url').'/'.ltrim($path, '/');
    }
}

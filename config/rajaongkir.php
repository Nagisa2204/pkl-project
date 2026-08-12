<?php

return [
    'api_key' => trim((string) env('RAJAONGKIR_API_KEY', '')),
    'base_url' => rtrim((string) env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'), '/'),
    'default_couriers' => array_values(array_filter(explode(':', (string) env('RAJAONGKIR_DEFAULT_COURIERS', 'jne:jnt:sicepat:pos')))),
    'timeout' => (int) env('RAJAONGKIR_TIMEOUT', 15),
    'region_cache_ttl' => (int) env('RAJAONGKIR_REGION_CACHE_TTL', 86400),
];

<?php

return [
    'api_key' => env('RAJAONGKIR_API_KEY'),
    'base_url' => rtrim((string) env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'), '/'),
    'origin_id' => env('RAJAONGKIR_ORIGIN_ID'),
    'default_couriers' => array_values(array_filter(explode(':', (string) env('RAJAONGKIR_DEFAULT_COURIERS', 'jne:jnt:sicepat:pos')))),
    'timeout' => (int) env('RAJAONGKIR_TIMEOUT', 15),
];

<?php

namespace App\Services;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Cache;

class StoreSettingsService
{
    /**
     * Cache raw model attributes instead of an Eloquent model instance.
     *
     * Serializing a model into a persistent cache can produce an
     * __PHP_Incomplete_Class after a deployment or autoload change.
     */
    private const CACHE_KEY = 'store.settings.attributes.v2';

    private const LEGACY_CACHE_KEY = 'store.settings';

    public function get(): StoreSetting
    {
        $attributes = Cache::rememberForever(
            self::CACHE_KEY,
            fn (): array => $this->getStoreSetting()->getAttributes(),
        );

        if (! is_array($attributes)) {
            $this->forget();
            $attributes = $this->getStoreSetting()->getAttributes();
            Cache::forever(self::CACHE_KEY, $attributes);
        }

        return (new StoreSetting)->newFromBuilder($attributes);
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::LEGACY_CACHE_KEY);
    }

    private function getStoreSetting(): StoreSetting
    {
        return StoreSetting::query()->firstOrCreate(['key' => 'default'], [
            'store_name' => config('app.name', 'Online Store'),
        ]);
    }
}

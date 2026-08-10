<?php

namespace App\Livewire\Admin;

use App\Exceptions\RajaOngkirException;
use App\Models\StoreSetting;
use App\Services\RajaOngkirService;
use App\Services\StoreSettingsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class AdminStoreSettings extends Component
{
    use WithFileUploads;

    public string $store_name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $whatsapp = null;
    public ?string $address = null;
    public ?string $province = null;
    public ?string $city = null;
    public ?string $district = null;
    public ?string $subdistrict = null;
    public ?string $postal_code = null;
    public string $selected_province_id = '';
    public string $selected_city_id = '';
    public string $selected_district_id = '';
    public string $selected_subdistrict_id = '';
    public ?int $shipping_origin_id = null;
    public ?string $shipping_origin_label = null;
    public array $provinces = [];
    public array $cities = [];
    public array $districts = [];
    public array $subdistricts = [];
    public ?string $description = null;
    public ?string $operating_hours = null;
    public ?string $website_url = null;
    public array $social_links = [];
    public $logo;
    public $favicon;
    public ?string $logo_path = null;
    public ?string $favicon_path = null;

    public function mount(StoreSettingsService $settings, RajaOngkirService $shipping): void
    {
        $this->authorize('admin');
        $store = $settings->get();
        $this->fill($store->only([
            'store_name', 'email', 'phone', 'whatsapp', 'address', 'province', 'city',
            'district', 'subdistrict', 'postal_code', 'shipping_origin_id',
            'shipping_origin_label', 'description', 'operating_hours', 'website_url',
            'logo_path', 'favicon_path',
        ]));
        $this->social_links = (array) ($store->social_links ?? []);
        $this->selected_province_id = (string) ($store->shipping_province_id ?? '');
        $this->selected_city_id = (string) ($store->shipping_city_id ?? '');
        $this->selected_district_id = (string) ($store->shipping_district_id ?? '');
        $this->selected_subdistrict_id = (string) ($store->shipping_origin_id ?? '');
        $this->loadRegionOptions($shipping);
    }

    public function updatedSelectedProvinceId(string $value, RajaOngkirService $shipping): void
    {
        $this->province = $this->regionName($this->provinces, $value);
        $this->cities = $value !== '' ? $this->loadRegions(fn () => $shipping->cities($value)) : [];
        $this->selected_city_id = '';
        $this->city = null;
        $this->resetDistrictAndSubdistrict();
    }

    public function updatedSelectedCityId(string $value, RajaOngkirService $shipping): void
    {
        $this->city = $this->regionName($this->cities, $value);
        $this->resetDistrictAndSubdistrict();
        $this->districts = $value !== '' ? $this->loadRegions(fn () => $shipping->districts($value)) : [];
    }

    public function updatedSelectedDistrictId(string $value, RajaOngkirService $shipping): void
    {
        $this->district = $this->regionName($this->districts, $value);
        $this->resetSubdistrict();
        $this->subdistricts = $value !== '' ? $this->loadRegions(fn () => $shipping->subdistricts($value)) : [];
    }

    public function updatedSelectedSubdistrictId(string $value): void
    {
        $region = $this->findRegion($this->subdistricts, $value);
        $this->subdistrict = $region['name'] ?? null;
        $this->postal_code = $region['zip_code'] ?? $this->postal_code;
        $this->shipping_origin_id = isset($region['id']) ? (int) $region['id'] : null;
        $this->shipping_origin_label = $region
            ? collect([$this->subdistrict, $this->district, $this->city, $this->province])->filter()->implode(', ')
            : null;
    }

    public function save(StoreSettingsService $settings): void
    {
        $this->authorize('admin');
        $data = $this->validate([
            'store_name' => ['required', 'string', 'max:255'], 'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'], 'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string'], 'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'], 'district' => ['required', 'string', 'max:255'],
            'subdistrict' => ['required', 'string', 'max:255'], 'postal_code' => ['required', 'string', 'max:20'],
            'selected_province_id' => ['required', 'integer'], 'selected_city_id' => ['required', 'integer'],
            'selected_district_id' => ['required', 'integer'], 'selected_subdistrict_id' => ['required', 'integer'],
            'shipping_origin_id' => ['required', 'integer'], 'shipping_origin_label' => ['required', 'string', 'max:1000'],
            'description' => ['nullable', 'string'], 'operating_hours' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'], 'social_links' => ['array'],
            'social_links.*' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:1024'],
        ]);

        $store = StoreSetting::firstOrCreate(['key' => 'default'], ['store_name' => $this->store_name]);
        $data['shipping_province_id'] = (int) $this->selected_province_id;
        $data['shipping_city_id'] = (int) $this->selected_city_id;
        $data['shipping_district_id'] = (int) $this->selected_district_id;
        $data = Arr::except($data, [
            'logo', 'favicon', 'selected_province_id', 'selected_city_id',
            'selected_district_id', 'selected_subdistrict_id',
        ]);
        if ($this->logo) {
            if ($store->logo_path) Storage::disk('public')->delete($store->logo_path);
            $data['logo_path'] = $this->logo->store('store', 'public');
        }
        if ($this->favicon) {
            if ($store->favicon_path) Storage::disk('public')->delete($store->favicon_path);
            $data['favicon_path'] = $this->favicon->store('store', 'public');
        }
        $store->update($data);
        $settings->forget();
        session()->flash('success', 'Pengaturan toko berhasil disimpan.');
        $this->dispatch('toast', variant: 'success', message: 'Pengaturan toko berhasil disimpan.');
    }

    private function loadRegionOptions(RajaOngkirService $shipping): void
    {
        $this->provinces = $this->loadRegions(fn () => $shipping->provinces());

        if ($this->selected_province_id !== '') {
            $this->cities = $this->loadRegions(fn () => $shipping->cities($this->selected_province_id));
        }

        if ($this->selected_city_id !== '') {
            $this->districts = $this->loadRegions(fn () => $shipping->districts($this->selected_city_id));
        }

        if ($this->selected_district_id !== '') {
            $this->subdistricts = $this->loadRegions(fn () => $shipping->subdistricts($this->selected_district_id));
        }
    }

    private function loadRegions(callable $loader): array
    {
        try {
            $regions = $loader();

            return is_array($regions) ? $regions : [];
        } catch (\Throwable $exception) {
            report($exception);
            $message = $exception instanceof RajaOngkirException
                ? $exception->getMessage()
                : 'Data wilayah belum dapat dimuat. Silakan coba kembali atau periksa log aplikasi.';
            $this->addError('shipping_origin', $message);

            return [];
        }
    }

    private function findRegion(array $regions, string $id): ?array
    {
        return collect($regions)->first(fn (array $region) => (string) ($region['id'] ?? '') === $id);
    }

    private function regionName(array $regions, string $id): ?string
    {
        $region = $this->findRegion($regions, $id);

        return isset($region['name']) ? (string) $region['name'] : null;
    }

    private function resetDistrictAndSubdistrict(): void
    {
        $this->selected_district_id = '';
        $this->district = null;
        $this->districts = [];
        $this->resetSubdistrict();
    }

    private function resetSubdistrict(): void
    {
        $this->selected_subdistrict_id = '';
        $this->subdistrict = null;
        $this->subdistricts = [];
        $this->shipping_origin_id = null;
        $this->shipping_origin_label = null;
    }

    public function render() { return view('livewire.admin.admin-store-settings'); }
}

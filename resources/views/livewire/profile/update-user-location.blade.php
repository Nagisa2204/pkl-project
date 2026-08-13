<?php

use App\Models\UserAddress;
use App\Exceptions\RajaOngkirException;
use Illuminate\Support\Facades\Auth;
use App\Services\RajaOngkirService;
use Livewire\Volt\Component;

new class extends Component
{
    public string $address_line = '';
    public string $province_name = '';
    public string $city_name = '';
    public string $district_name = '';
    public string $subdistrict_name = '';
    public string $postal_code = '';
    public ?int $destination_id = null;

    public array $provinces = [];
    public array $cities = [];
    public array $districts = [];
    public array $subdistricts = [];
    public string $selected_province_id = '';
    public string $selected_city_id = '';
    public string $selected_district_id = '';
    public string $selected_subdistrict_id = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->fetchProvinces();

        $user = Auth::user();
        $defaultAddress = $user->userAddresses()->where('is_default', true)->first();
        
        if ($defaultAddress) {
            $this->address_line = $defaultAddress->address_line ?? '';
            $this->province_name = $defaultAddress->province_name ?? '';
            $this->city_name = $defaultAddress->city_name ?? '';
            $this->district_name = $defaultAddress->district_name ?? '';
            $this->subdistrict_name = $defaultAddress->subdistrict_name ?? '';
            $this->postal_code = $defaultAddress->postal_code ?? '';
            $this->destination_id = $defaultAddress->destination_id;
        }
    }

    /**
     * Fetch Provinces from RajaOngkir
     */
    public function fetchProvinces(): void
    {
        $this->provinces = $this->loadRegions(fn () => app(RajaOngkirService::class)->provinces());
    }

    public function updatedSelectedProvinceId($value): void
    {
        $province = collect($this->provinces)->firstWhere('id', $value);
        $this->province_name = $province['name'] ?? '';

        $this->cities = [];
        $this->districts = [];
        $this->subdistricts = [];
        $this->selected_city_id = '';
        $this->selected_district_id = '';
        $this->selected_subdistrict_id = '';
        $this->city_name = '';
        $this->district_name = '';
        $this->subdistrict_name = '';
        $this->postal_code = '';
        $this->destination_id = null;

        if (blank($value)) return;

        $this->cities = $this->loadRegions(fn () => app(RajaOngkirService::class)->cities($value));
    }

    public function updatedSelectedCityId($value): void
    {
        $city = collect($this->cities)->firstWhere('id', $value);
        $this->city_name = $city['name'] ?? '';
        $this->districts = [];
        $this->subdistricts = [];
        $this->selected_district_id = '';
        $this->selected_subdistrict_id = '';
        $this->district_name = '';
        $this->subdistrict_name = '';
        $this->postal_code = '';
        $this->destination_id = null;

        if (blank($value)) return;

        $this->districts = $this->loadRegions(fn () => app(RajaOngkirService::class)->districts($value));
    }

    public function updatedSelectedDistrictId($value): void
    {
        $district = collect($this->districts)->firstWhere('id', $value);
        $this->district_name = $district['name'] ?? '';
        $this->subdistricts = [];
        $this->selected_subdistrict_id = '';
        $this->postal_code = '';
        $this->subdistrict_name = '';
        $this->destination_id = null;

        if (blank($value)) return;

        $this->subdistricts = $this->loadRegions(fn () => app(RajaOngkirService::class)->subdistricts($value));
    }

    public function updatedSelectedSubdistrictId($value): void
    {
        $subdistrict = collect($this->subdistricts)->firstWhere('id', $value);
        
        if ($subdistrict) {
            $this->subdistrict_name = $subdistrict['name'] ?? '';
            $this->postal_code = $subdistrict['zip_code'] ?? '';
            $this->destination_id = $subdistrict['id'] ?? null;
        } else {
            $this->subdistrict_name = '';
            $this->postal_code = '';
            $this->destination_id = null;
        }
    }

    private function loadRegions(callable $loader): array
    {
        try {
            $this->resetErrorBag('address');
            $regions = $loader();

            return is_array($regions) ? $regions : [];
        } catch (\Throwable $exception) {
            report($exception);
            $message = $exception instanceof RajaOngkirException
                ? $exception->getMessage()
                : 'Data wilayah belum dapat dimuat. Silakan coba kembali atau periksa log aplikasi.';
            $this->addError('address', $message);

            return [];
        }
    }

    /**
     * Update the location information for the currently authenticated user.
     */
    public function updateLocationInformation(): void
    {
        $user = Auth::user();

        $validatedAddress = $this->validate([
            'address_line' => ['required', 'string'],
            'province_name' => ['required', 'string', 'max:255'],
            'city_name' => ['required', 'string', 'max:255'],
            'district_name' => ['required', 'string', 'max:255'],
            'subdistrict_name' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'destination_id' => ['required', 'integer'],
        ]);

        $user->userAddresses()->updateOrCreate(
            ['is_default' => true],
            array_merge($validatedAddress, [
                'label' => 'Alamat Utama',
                'receiver_name' => $user->name,
                'phone' => $user->phone ?? '',
                'destination_label' => $this->subdistrict_name . ', ' . $this->district_name . ', ' . $this->city_name . ', ' . $this->province_name,
            ])
        );

        $this->dispatch('location-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-content">Alamat Pengiriman</h2>
        <p class="mt-1 text-sm text-muted">
            Perbarui alamat lengkap pengiriman untuk keperluan pesanan.
        </p>
    </header>

    <form wire:submit="updateLocationInformation" class="mt-6 space-y-6">
        <x-input-error :messages="$errors->get('address')" />

        <!-- Address Line -->
        <div>
            <x-input-label for="address_line" value="Alamat lengkap" />
            <x-text-input wire:model="address_line" id="address_line" name="address_line" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('address_line')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-ui.searchable-select wire:model.live="selected_province_id" :options="$provinces" label="Provinsi" placeholder="Pilih provinsi" search-placeholder="Cari provinsi..." :clearable="false" />
                <x-input-error class="mt-2" :messages="$errors->get('province_name')" />
            </div>

            <div>
                <x-ui.searchable-select wire:model.live="selected_city_id" :options="$cities" label="Kota/Kabupaten" placeholder="Pilih kota/kabupaten" search-placeholder="Cari kota/kabupaten..." :clearable="false" :disabled="empty($cities)" :instance-key="'profile-city-'.$selected_province_id" />
                <x-input-error class="mt-2" :messages="$errors->get('city_name')" />
            </div>

            <div>
                <x-ui.searchable-select wire:model.live="selected_district_id" :options="$districts" label="Kecamatan" placeholder="Pilih kecamatan" search-placeholder="Cari kecamatan..." :clearable="false" :disabled="empty($districts)" :instance-key="'profile-district-'.$selected_city_id" />
                <x-input-error class="mt-2" :messages="$errors->get('district_name')" />
            </div>

            <div>
                <x-ui.searchable-select wire:model.live="selected_subdistrict_id" :options="$subdistricts" label="Kelurahan/Desa" placeholder="Pilih kelurahan/desa" search-placeholder="Cari kelurahan/desa..." :clearable="false" :disabled="empty($subdistricts)" :instance-key="'profile-subdistrict-'.$selected_district_id" />
                <x-input-error class="mt-2" :messages="$errors->get('subdistrict_name')" />
            </div>

            <!-- Postal Code -->
            <div>
                <p class="text-sm text-muted-foreground mt-2">
                    Kode pos: <span class="font-medium">{{ $postal_code ?: '-' }}</span>
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
            </div>
        </div>

        <!-- Destination ID (Hidden) -->
        <input type="hidden" wire:model="destination_id">

        <div class="ui-form-actions">
            <x-ui.button type="submit">Simpan Alamat</x-ui.button>

            <x-action-message class="me-3" on="location-updated">
                Alamat tersimpan.
            </x-action-message>
        </div>
    </form>
</section>
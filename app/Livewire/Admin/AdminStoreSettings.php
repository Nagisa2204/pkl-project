<?php

namespace App\Livewire\Admin;

use App\Models\StoreSetting;
use App\Services\StoreSettingsService;
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
    public ?string $postal_code = null;
    public ?string $description = null;
    public ?string $operating_hours = null;
    public ?string $website_url = null;
    public array $social_links = [];
    public $logo;
    public $favicon;
    public ?string $logo_path = null;
    public ?string $favicon_path = null;

    public function mount(StoreSettingsService $settings): void
    {
        $this->authorize('admin');
        $store = $settings->get();
        $this->fill($store->only(['store_name', 'email', 'phone', 'whatsapp', 'address', 'province', 'city', 'postal_code', 'description', 'operating_hours', 'website_url', 'logo_path', 'favicon_path']));
        $this->social_links = (array) ($store->social_links ?? []);
    }

    public function save(StoreSettingsService $settings): void
    {
        $this->authorize('admin');
        $data = $this->validate([
            'store_name' => ['required', 'string', 'max:255'], 'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'], 'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'], 'province' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'], 'postal_code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'], 'operating_hours' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'], 'social_links' => ['array'],
            'social_links.*' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:1024'],
        ]);

        $store = StoreSetting::firstOrCreate(['key' => 'default'], ['store_name' => $this->store_name]);
        unset($data['logo'], $data['favicon']);
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
    }

    public function render() { return view('livewire.admin.admin-store-settings'); }
}

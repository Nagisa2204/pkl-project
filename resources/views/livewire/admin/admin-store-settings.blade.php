<div class="mx-auto w-full space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-content">Pengaturan Toko</h1>
        <p class="mt-1 text-sm text-muted">Informasi ini digunakan pada header, footer, checkout, invoice, email, dan
            perhitungan ongkir.</p>
    </div>

    <x-ui.card>
        <h2 class="text-lg font-bold text-content">Informasi Umum</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @foreach ([
        'store_name' => 'Nama toko',
        'email' => 'Email toko',
        'phone' => 'Telepon',
        'whatsapp' => 'WhatsApp',
        'website_url' => 'URL website',
        'store-hours' => 'Jam operasional',
    ] as $field => $label)
                <div>
                    <label class="ui-field-label" for="store-{{ $field }}">{{ $label }}</label>
                    <input id="store-{{ $field }}" wire:model="{{ $field }}" class="ui-field">
                    <x-input-error :messages="$errors->get($field)" />
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <label class="ui-field-label" for="store-description">Deskripsi</label>
            <textarea id="store-description" wire:model="description" rows="3" class="ui-field"></textarea>
            <x-input-error :messages="$errors->get('description')" />
        </div>
    </x-ui.card>

    <x-ui.card>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-content">Alamat Pickup Toko</h2>
                <p class="mt-1 text-sm text-muted">Alamat ini menjadi asal pengiriman untuk setiap perhitungan ongkir.
                </p>
            </div>
            <x-ui.badge variant="info">Origin pengiriman</x-ui.badge>
        </div>

        <div class="mt-4">
            <label class="ui-field-label" for="store-address">Alamat lengkap</label>
            <textarea id="store-address" wire:model="address" rows="3" class="ui-field"
                placeholder="Nama jalan, nomor bangunan, dan patokan"></textarea>
            <x-input-error :messages="$errors->get('address')" />
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <x-ui.searchable-select wire:model.live="selected_province_id" :options="$provinces" label="Provinsi"
                placeholder="Pilih provinsi" search-placeholder="Cari provinsi..." :clearable="false"
                :disabled="empty($provinces)" />
            <x-ui.searchable-select wire:model.live="selected_city_id" :options="$cities" label="Kota/Kabupaten"
                placeholder="Pilih kota/kabupaten" search-placeholder="Cari kota/kabupaten..." :clearable="false"
                :disabled="empty($cities)" :instance-key="'store-city-' . $selected_province_id" />
            <x-ui.searchable-select wire:model.live="selected_district_id" :options="$districts" label="Kecamatan"
                placeholder="Pilih kecamatan" search-placeholder="Cari kecamatan..." :clearable="false"
                :disabled="empty($districts)" :instance-key="'store-district-' . $selected_city_id" />
            <x-ui.searchable-select wire:model.live="selected_subdistrict_id" :options="$subdistricts" label="Kelurahan/Desa"
                placeholder="Pilih kelurahan/desa" search-placeholder="Cari kelurahan/desa..." :clearable="false"
                :disabled="empty($subdistricts)" :instance-key="'store-subdistrict-' . $selected_district_id" />
            <div>
                <label class="ui-field-label" for="store-postal-code">Kode pos</label>
                <input id="store-postal-code" wire:model="postal_code" class="ui-field">
                <x-input-error :messages="$errors->get('postal_code')" />
            </div>
        </div>

        @if ($shipping_origin_label)
            <x-ui.alert variant="info" class="mt-4">
                Origin aktif: {{ $shipping_origin_label }}
            </x-ui.alert>
        @endif

        <x-input-error :messages="$errors->get('shipping_origin')" class="mt-3" />
        @foreach (['selected_province_id', 'selected_city_id', 'selected_district_id', 'selected_subdistrict_id', 'shipping_origin_id'] as $field)
            <x-input-error :messages="$errors->get($field)" />
        @endforeach
    </x-ui.card>

    <x-ui.card>
        <h2 class="text-lg font-bold text-content">Identitas Visual dan Media Sosial</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="ui-field-label" for="store-logo">Upload logo</label>
                <input id="store-logo" type="file" wire:model="logo" class="ui-field p-2">
                <x-input-error :messages="$errors->get('logo')" />
            </div>
            <div>
                <label class="ui-field-label" for="store-favicon">Upload favicon</label>
                <input id="store-favicon" type="file" wire:model="favicon" class="ui-field p-2">
                <x-input-error :messages="$errors->get('favicon')" />
            </div>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            @foreach (['instagram', 'facebook', 'tiktok', 'youtube'] as $social)
                <div>
                    <label class="ui-field-label" for="social-{{ $social }}">{{ ucfirst($social) }}</label>
                    <input id="social-{{ $social }}" wire:model="social_links.{{ $social }}"
                        placeholder="URL {{ ucfirst($social) }}" class="ui-field">
                    <x-input-error :messages="$errors->get('social_links.' . $social)" />
                </div>
            @endforeach
        </div>
    </x-ui.card>

    <div class="ui-form-actions">
        <x-ui.button wire:click="save" wire:loading.attr="disabled" wire:target="save" variant="success">
            <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </x-ui.button>
    </div>
</div>

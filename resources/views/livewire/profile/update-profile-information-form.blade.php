<?php

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\RajaOngkirService;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    
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
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';

        $this->fetchProvinces();

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
        try { $this->provinces = app(RajaOngkirService::class)->provinces(); }
        catch (\Throwable $exception) { report($exception); $this->provinces = []; $this->addError('address', 'Data wilayah tidak dapat dimuat.'); }
    }

    public function updatedSelectedProvinceId($value): void
    {
        $province = collect($this->provinces)->firstWhere('id', $value);
        $this->province_name = $province['name'] ?? '';

        try { $this->cities = app(RajaOngkirService::class)->cities($value); }
        catch (\Throwable $exception) { report($exception); $this->cities = []; }

        $this->selected_city_id = '';
        $this->city_name = '';
        $this->destination_id = null;
    }

    public function updatedSelectedCityId($value): void
    {
        $city = collect($this->cities)->firstWhere('id', $value);
        $this->city_name = $city['name'] ?? '';
        
        try { $this->districts = app(RajaOngkirService::class)->districts($value); }
        catch (\Throwable $exception) { report($exception); $this->districts = []; }

        $this->selected_district_id = '';
        $this->district_name = '';
        $this->destination_id = null;
    }

    public function updatedSelectedDistrictId($value): void
    {
        $district = collect($this->districts)->firstWhere('id', $value);
        $this->district_name = $district['name'] ?? '';

        try { $this->subdistricts = app(RajaOngkirService::class)->subdistricts($value); }
        catch (\Throwable $exception) { report($exception); $this->subdistricts = []; }

        $this->selected_subdistrict_id = '';
        $this->postal_code = '';
        $this->subdistrict_name = '';
        $this->destination_id = null;
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

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validatedUser = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $validatedAddress = $this->validate([
            'address_line' => ['required', 'string'],
            'province_name' => ['required', 'string', 'max:255'],
            'city_name' => ['required', 'string', 'max:255'],
            'district_name' => ['required', 'string', 'max:255'],
            'subdistrict_name' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'destination_id' => ['required', 'integer'],
        ]);

        $user->fill($validatedUser);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $user->userAddresses()->updateOrCreate(
            ['is_default' => true],
            array_merge($validatedAddress, [
                'label' => 'Alamat Utama',
                'receiver_name' => $user->name,
                'phone' => $user->phone ?? '',
                'destination_label' => $this->subdistrict_name . ', ' . $this->district_name . ', ' . $this->city_name . ', ' . $this->province_name,
            ])
        );

        $this->dispatch('profile-updated', name: $user->name);
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile & Address Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information, email address, and shipping address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input wire:model="phone" id="phone" name="phone" type="text" class="mt-1 block w-full" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <hr class="my-4 border-gray-200">
        
        <h3 class="text-md font-medium text-gray-900">{{ __('Shipping Address') }}</h3>

        <!-- Address Line -->
        <div>
            <x-input-label for="address_line" :value="__('Address Line')" />
            <x-text-input wire:model="address_line" id="address_line" name="address_line" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('address_line')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Dropdown Province (RajaOngkir) -->
            <div>
                <x-input-label for="selected_province_id" :value="__('Province')" />
                <select wire:model.live="selected_province_id" id="selected_province_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required>
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('province_name')" />
            </div>

            <!-- Dropdown City (RajaOngkir) -->
            <div>
                <x-input-label for="selected_city_id" :value="__('City / Kabupaten')" />
                <select wire:model.live="selected_city_id" id="selected_city_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required @if(empty($cities)) disabled @endif>
                    <option value="">-- Pilih Kota/Kabupaten --</option>
                    @foreach($cities as $city)
                        <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('city_name')" />
            </div>

            <!-- Dropdown District (RajaOngkir Starter) -->
            <div>
                <x-input-label for="selected_district_id" :value="__('District / Kecamatan')" />
                <select wire:model.live="selected_district_id" id="selected_district_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required @if(empty($districts)) disabled @endif>
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach($districts as $district)
                        <option value="{{ $district['id'] }}">{{ $district['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('district_name')" />
            </div>

            <!-- Dropdown Subdistrict (RajaOngkir Starter) -->
            <div>
                <x-input-label for="selected_subdistrict_id" :value="__('Subdistrict / Kelurahan')" />
                <select wire:model.live="selected_subdistrict_id" id="selected_subdistrict_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required @if(empty($subdistricts)) disabled @endif>
                    <option value="">-- Pilih Kelurahan --</option>
                    @foreach($subdistricts as $subdistrict)
                        <option value="{{ $subdistrict['id'] }}">{{ $subdistrict['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('subdistrict_name')" />
            </div>

            <!-- Postal Code -->
            <div>
                <p class="text-sm text-gray-700 mt-2">
                    {{ __('Postal Code:') }} <span class="font-medium">{{ $postal_code }}</span>
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
            </div>
        </div>

        <!-- Destination ID (Hidden) -->
        <input type="hidden" wire:model="destination_id">

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>

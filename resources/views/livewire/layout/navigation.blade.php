<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-surface border-b border-default sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                    @if ($storeSettings?->logo_path)
                        <img src="{{ Storage::url($storeSettings->logo_path) }}" alt="{{ $storeSettings->store_name }}"
                        class="h-9 w-auto">@else<x-application-logo
                            class="block h-9 w-auto fill-current text-content" />
                    @endif
                    <span class="font-bold text-content">{{ $storeSettings->store_name }}</span>
                </a>
            </div>

            <div
                class="hidden items-center gap-2 rounded-full border border-default bg-surface px-4 py-2 text-sm font-semibold text-muted-foreground transition-all hover:bg-subtle md:inline-flex">
                <a href="{{ route('home') }}" wire:navigate
                    class="flex items-center gap-2 px-4 py-1.5 rounded-full transition-all duration-150
                    {{ request()->routeIs('home')
                        ? '!bg-dark !text-primary-foreground'
                        : 'text-muted-foreground hover:text-content hover:bg-subtle' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-4 h-4 {{ request()->routeIs('home') ? '!text-primary-foreground' : 'text-muted-foreground' }}">
                        <path
                            d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.69Z" />
                        <path
                            d="M12 5.432 4.879 12.552A.75.75 0 0 0 4.5 13.082v6.918c0 1.243 1.007 2.25 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.918a.75.75 0 0 0-.379-.53L12 5.432Z" />
                    </svg>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('product.index') }}" wire:navigate
                    class="flex items-center gap-2 px-4 py-1.5 rounded-full transition-all duration-150
                    {{ request()->routeIs('product.*')
                        ? '!bg-dark !text-primary-foreground'
                        : 'text-muted-foreground hover:text-content hover:bg-subtle' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-4 h-4 {{ request()->routeIs('product.*') ? '!text-primary-foreground' : 'text-muted-foreground' }}">
                        <path
                            d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.25a.75.75 0 0 0 .734-.6l1.5-7.5a.75.75 0 0 0-.734-.9H5.532l-.464-1.74A1.875 1.875 0 0 0 3.636 2.25H2.25Z" />
                    </svg>
                    <span>Katalog</span>
                </a>

                @auth
                    <a href="{{ route('orders.history') }}" wire:navigate
                        class="flex items-center gap-2 px-4 py-1.5 rounded-full transition-all duration-150
                        {{ request()->routeIs('orders.*')
                            ? '!bg-dark !text-primary-foreground'
                            : 'text-muted-foreground hover:text-content hover:bg-subtle' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor"
                            class="w-4 h-4 {{ request()->routeIs('orders.*') ? '!text-primary-foreground' : 'text-muted-foreground' }}">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>History</span>
                    </a>
                @endauth
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-3">

                <a href="{{ route('cart') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-default bg-surface text-muted-foreground font-semibold text-sm hover:bg-subtle transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span>Keranjang</span>

                    <livewire:cart-counter />
                </a>

                @guest
                    <a href="{{ route('login') }}" wire:navigate
                        class="text-sm font-semibold text-muted-foreground hover:text-content px-3 py-2">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" wire:navigate
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-dark text-primary-foreground font-semibold text-sm hover:bg-secondary transition-all">
                        Buat Akun
                    </a>
                @endguest

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-muted bg-surface hover:text-muted-foreground focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                    x-on:profile-updated.window="name = $event.detail.name"></div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.show')" wire:navigate>
                                Profil
                            </x-dropdown-link>

                            @can('admin')
                                <x-dropdown-link :href="route('admin.home')" :active="request()->routeIs('admin.*')" wire:navigate>
                                    Dashboard Admin
                                </x-dropdown-link>
                            @endcan

                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-muted hover:text-muted hover:bg-subtle focus:outline-none focus:bg-subtle focus:text-muted transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 border-t border-default">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')" wire:navigate>
                {{ __('Beranda') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('product.index')" :active="request()->routeIs('product.*')" wire:navigate>
                {{ __('Katalog') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('orders.history')" :active="request()->routeIs('orders.*')" wire:navigate>
                    {{ __('History') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-default">
                <div class="px-4">
                    <div class="font-medium text-base text-content" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                        x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-muted">{{ auth()->user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.show')" wire:navigate>
                        Profil
                    </x-responsive-nav-link>

                    @can('admin')
                        <x-responsive-nav-link :href="route('admin.home')" :active="request()->routeIs('admin.*')" wire:navigate>
                            Dashboard Admin
                        </x-responsive-nav-link>
                    @endcan

                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        @else
            <div class="pt-2 pb-3 space-y-1 border-t border-default px-4">
                <a href="{{ route('login') }}" wire:navigate
                    class="block w-full py-2 text-center text-sm font-semibold text-muted-foreground">Masuk</a>
                <a href="{{ route('register') }}" wire:navigate
                    class="block w-full py-2 text-center text-sm font-semibold text-primary-foreground bg-dark rounded-lg">Buat
                    Akun</a>
            </div>
        @endauth
    </div>
</nav>

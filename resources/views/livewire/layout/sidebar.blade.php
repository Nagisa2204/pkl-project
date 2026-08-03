<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<aside class="flex w-64 shrink-0 flex-col border-r border-gray-200 bg-white">

    <div class="flex h-20 items-center border-b border-gray-200 px-6">
        <a href="{{ route('dashboard') }}" wire:navigate>
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
        </a>
    </div>

    <nav class="flex-1 px-4 py-6">
        <p class="mb-4 px-2 text-xs font-semibold uppercase tracking-widest text-gray-400">
            Main Menu
        </p>
        
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <a href="{{ route('admin.home') }}" wire:navigate class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2.5 transition {{ request()->routeIs('admin.home') ? 'bg-orange-500 text-black border-orange-500' : 'text-gray-700 hover:bg-gray-100' }} ">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5M5.25 9.75V21h13.5V9.75" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.products') }}" wire:navigate class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2.5 transition {{ request()->routeIs('admin.products') ? 'bg-orange-500 text-black border-orange-500' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5L12 3L3.75 7.5L12 12L20.25 7.5ZM3.75 7.5V16.5L12 21M20.25 7.5V16.5L12 21" />
                </svg>
                <span>Products</span>
            </a>

            <a href="{{ route('admin.orders') }}" wire:navigate class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2.5 transition {{ request()->routeIs('admin.orders*') ? 'bg-orange-500 text-black border-orange-500' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12M8.25 17.25h12M3.75 6.75h.008v.008H3.75zm0 5.25h.008v.008H3.75zm0 5.25h.008v.008H3.75z" />
                </svg>
                <span>Orders</span>
            </a>

            <a href="{{ route('admin.accounts') }}" wire:navigate class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2.5 transition {{ request()->routeIs('admin.accounts*') ? 'bg-orange-500 text-black border-orange-500' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Accounts</span>
            </a>
        </div>
    </nav>

    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="min-w-0">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <div class="mt-3 space-y-1">
            <button wire:click="logout" class="w-full text-start">
                <x-responsive-nav-link>
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </button>
        </div>
    </div>
</aside>
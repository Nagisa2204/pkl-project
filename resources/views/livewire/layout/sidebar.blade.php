<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $adminLinks = [
        ['route' => 'admin.home', 'pattern' => 'admin.home', 'label' => 'Dashboard'],
        ['route' => 'admin.products', 'pattern' => 'admin.products*', 'label' => 'Produk'],
        ['route' => 'admin.categories', 'pattern' => 'admin.categories', 'label' => 'Kategori'],
        ['route' => 'admin.orders', 'pattern' => 'admin.orders*', 'label' => 'Pesanan'],
        ['route' => 'admin.accounts', 'pattern' => 'admin.accounts*', 'label' => 'Akun'],
        ['route' => 'admin.settings', 'pattern' => 'admin.settings', 'label' => 'Pengaturan Toko'],
    ];
@endphp

<div class="contents">
    <header class="sticky top-0 z-50 border-b border-default bg-surface lg:hidden" x-data="{ open: false }">
        <div class="flex h-16 items-center justify-between px-4">
            <a href="{{ route('admin.home') }}" class="font-bold text-content" wire:navigate>{{ $storeSettings->store_name }}</a>
            <button type="button" class="ui-btn ui-btn-outline px-3" x-on:click="open = !open" :aria-expanded="open" aria-label="Buka menu admin">☰</button>
        </div>
        <nav x-cloak x-show="open" x-transition class="space-y-1 border-t border-default p-3">
            @foreach($adminLinks as $link)
                <a href="{{ route($link['route']) }}" wire:navigate class="block rounded-ui px-3 py-2 text-sm font-semibold {{ request()->routeIs($link['pattern']) ? 'bg-info-soft text-primary' : 'text-muted-foreground hover:bg-subtle hover:text-content' }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>
    </header>

    <aside class="hidden w-64 shrink-0 flex-col border-r border-default bg-surface lg:flex">
        <div class="flex h-20 items-center border-b border-default px-6">
            <a href="{{ route('admin.home') }}" wire:navigate class="font-bold text-content">{{ $storeSettings->store_name }}</a>
        </div>

        <nav class="flex-1 px-4 py-6">
            <p class="mb-4 px-2 text-xs font-semibold uppercase tracking-widest text-muted">Menu Admin</p>
            <div class="flex flex-col gap-2">
                @foreach($adminLinks as $link)
                    <a href="{{ route($link['route']) }}" wire:navigate class="flex items-center rounded-ui border px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs($link['pattern']) ? 'border-primary bg-info-soft text-primary' : 'border-transparent text-muted-foreground hover:border-default hover:bg-subtle hover:text-content' }}">{{ $link['label'] }}</a>
                @endforeach
            </div>
        </nav>

        <div class="border-t border-default p-4">
            <div class="min-w-0">
                <div class="truncate text-sm font-semibold text-content" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="truncate text-xs text-muted">{{ auth()->user()->email }}</div>
            </div>
            <x-ui.button wire:click="logout" variant="outline" size="sm" class="mt-3 w-full">Keluar</x-ui.button>
        </div>
    </aside>
</div>

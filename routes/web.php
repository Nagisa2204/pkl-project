<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'livewire.welcome');

Route::view('dashboard', 'livewire.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('catalog', 'livewire.product-list')
    ->middleware(['auth', 'verified'])
    ->name('catalog');

Route::view('cart', 'livewire.cart-index')
    ->middleware(['auth', 'verified'])
    ->name('cart');

Route::view('profile', 'livewire.profile')
    ->middleware(['auth'])  
    ->name('profile');

require __DIR__.'/auth.php';

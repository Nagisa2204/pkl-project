<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\ProductList;
use App\Livewire\ProductDetail;
use App\Livewire\CartIndex;
use App\Livewire\Checkout;
use App\Livewire\OrderHistory;
use App\Livewire\OrderDetail;

use App\Livewire\AdminDashboard;
use App\Livewire\AdminOrderHistory;
use App\Livewire\AdminOrderDetail;
use App\Livewire\AdminProduct;

Route::view('/', 'livewire.welcome' );

Route::get('/products', ProductList::class)
     ->name('product.index');

Route::get('/products/{slug}', ProductDetail::class)
     ->name('product.detail');

Route::middleware(['auth'])->group(function () {    
    Route::middleware(['can:admin'])->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/products', AdminProduct::class)->name('products');
        Route::get('/orders', AdminOrderHistory::class)->name('orders');
        Route::get('/orders/{invoice_no}', AdminOrderDetail::class)->name('orders.detail');
    });

    Route::view('dashboard', 'livewire.dashboard')->name('home');
    Route::get('/cart', CartIndex::class)->name('cart');
    Route::get('/checkout', Checkout::class)->name('checkout');
    Route::get('/history', OrderHistory::class)->name('orders.history');
    Route::get('/history/{invoice_no}', OrderDetail::class)->name('orders.detail');
    Route::view('profile', 'livewire.profile')->name('profile.show');
});

require __DIR__.'/auth.php';

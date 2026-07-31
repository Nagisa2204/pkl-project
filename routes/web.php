<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\AdminManageProduct;
use App\Livewire\Admin\AdminProduct;
use App\Livewire\Admin\AdminOrderHistory;
use App\Livewire\Admin\AdminOrderDetail;
use App\Livewire\Admin\AdminManageAccount;

use App\Livewire\ProductList;
use App\Livewire\ProductDetail;
use App\Livewire\CartIndex;
use App\Livewire\Checkout;
use App\Livewire\OrderHistory;
use App\Livewire\OrderDetail;

Route::view('/', 'livewire.welcome' );

Route::get('/products', ProductList::class)
     ->name('product.index');

Route::get('/products/{slug}', ProductDetail::class)
     ->name('product.detail');

Route::middleware(['auth'])->group(function () {    
    Route::middleware(['can:admin'])->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('home');
        Route::get('/products', AdminProduct::class)->name('products');
        Route::get('/products/create', AdminManageProduct::class)->name('products.create');
        Route::get('/products/manage/{product}', AdminManageProduct::class)->name('products.manage');
        Route::get('/orders', AdminOrderHistory::class)->name('orders');
        Route::get('/orders/{invoice_no}', AdminOrderDetail::class)->name('orders.detail');
        Route::get('/accounts', AdminManageAccount::class)->name('accounts');
    });

    Route::view('/dashboard', 'livewire.dashboard')->name('dashboard');
    Route::get('/cart', CartIndex::class)->name('cart');
    Route::get('/checkout', Checkout::class)->name('checkout');
    Route::get('/history', OrderHistory::class)->name('orders.history');
    Route::get('/history/{invoice_no}', OrderDetail::class)->name('orders.detail');
    Route::view('profile', 'livewire.profile')->name('profile.show');
});

require __DIR__.'/auth.php';

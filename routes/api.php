<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CourierController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/midtrans/webhook', [PaymentController::class, 'handlePayment'])
    ->middleware('throttle:midtrans-webhook')
    ->name('midtrans.webhook');

Route::post('/courier', [CourierController::class, 'handleCourier'])
    ->middleware(['auth:sanctum', 'can:admin', 'throttle:30,1']);

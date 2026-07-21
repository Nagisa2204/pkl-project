<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CourierController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/payment', [PaymentController::class, 'handlePayment']);
Route::post('/courier', [CourierController::class, 'handleCourier']);
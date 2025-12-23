<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Commerce\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Version 1
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::prefix('auth/otp')->middleware('throttle:10,1')->group(function () {
        Route::post('request', [OtpController::class, 'requestOtp']);
        Route::post('verify', [OtpController::class, 'verifyOtp']);
    });

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn (Request $request) => $request->user());
        Route::post('/cart/checkout', [PaymentController::class, 'checkout']);
        Route::post('/cart/sync', [CartController::class, 'sync']);
    });
});

// Backward compatibility: Keep old routes without version prefix
Route::prefix('auth/otp')->middleware('throttle:10,1')->group(function () {
    Route::post('request', [OtpController::class, 'requestOtp']);
    Route::post('verify', [OtpController::class, 'verifyOtp']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/cart/checkout', [PaymentController::class, 'checkout']);
    Route::post('/cart/sync', [CartController::class, 'sync']);
});


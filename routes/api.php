<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Commerce\CartController;
use App\Http\Controllers\Api\V1\AiToolController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Version 1
Route::prefix('v1')->name('api.v1.')->group(function () {
    // Auth routes
    Route::prefix('auth/otp')->middleware('throttle:10,1')->group(function () {
        Route::post('request', [OtpController::class, 'requestOtp']);
        Route::post('verify', [OtpController::class, 'verifyOtp']);
    });

    // Public API Routes
    
    // AI Tools
    Route::prefix('ai-tools')->name('ai-tools.')->group(function () {
        Route::get('/', [AiToolController::class, 'index'])->name('index');
        Route::get('/featured', [AiToolController::class, 'featured'])->name('featured');
        Route::get('/popular', [AiToolController::class, 'popular'])->name('popular');
        Route::get('/{slug}', [AiToolController::class, 'show'])->name('show');
        Route::get('/{slug}/related', [AiToolController::class, 'related'])->name('related');
    });

    // Courses
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/featured', [CourseController::class, 'featured'])->name('featured');
        Route::get('/popular', [CourseController::class, 'popular'])->name('popular');
        Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
        Route::get('/{slug}/related', [CourseController::class, 'related'])->name('related');
    });

    // Posts (Blog)
    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/featured', [PostController::class, 'featured'])->name('featured');
        Route::get('/popular', [PostController::class, 'popular'])->name('popular');
        Route::get('/{slug}', [PostController::class, 'show'])->name('show');
        Route::get('/{slug}/related', [PostController::class, 'related'])->name('related');
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


<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

// Routes công khai
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products/export', [ProductController::class, 'export']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

// Routes yêu cầu xác thực
Route::middleware('auth:sanctum')->group(function () {
    // Routes cho người dùng thông thường
    Route::post('/wishlists/check', [WishlistController::class, 'check']);
    Route::apiResource('wishlists', WishlistController::class)->only(['index', 'store', 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('carts', CartController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/carts/checkout', [CartController::class, 'checkout']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

    // Routes dành cho admin (dùng class middleware trực tiếp)
    Route::middleware([\App\Http\Middleware\Role::class.':admin'])->group(function () {
        Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
        Route::get('/orders/all', [OrderController::class, 'allOrders']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function () {
        return auth()->user();
    });

    Route::post('/logout', function () {
        auth()->logout();
        return response()->json(['status' => 'logged out']);
    });

    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::get('/products/{product}', [ProductController::class, 'apiShow']);
    Route::get('/products/{product}/reviews', [ReviewController::class, 'apiIndex']);
    Route::post('/products/{product}/reviews', [ReviewController::class, 'apiStore']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'apiDestroy']);

    Route::get('/categories', [CategoryController::class, 'apiIndex']);

    Route::get('/cart', [CartController::class, 'apiIndex']);
    Route::post('/cart/add', [CartController::class, 'apiAdd']);
    Route::post('/cart/update/{cartItem}', [CartController::class, 'apiUpdate']);
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'apiRemove']);

    Route::get('/orders', [OrderController::class, 'apiIndex']);
    Route::get('/orders/{order}', [OrderController::class, 'apiShow']);

    Route::get('/points', [OrderController::class, 'apiPoints']);
    Route::get('/customer/dashboard', [DashboardController::class, 'apiCustomerDash']);

    Route::get('/wishlist', [WishlistController::class, 'apiIndex']);
    Route::post('/products/{product}/wishlist', [WishlistController::class, 'apiToggle']);
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'apiDestroy']);

    Route::post('/checkout/validate-discount', [OrderController::class, 'apiValidateDiscount']);
    Route::post('/checkout', [OrderController::class, 'apiCheckout']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'apiAdminDash']);
        Route::post('/categories', [CategoryController::class, 'apiStore']);
        Route::put('/categories/{category}', [CategoryController::class, 'apiUpdate']);
        Route::delete('/categories/{category}', [CategoryController::class, 'apiDestroy']);

        Route::post('/products', [ProductController::class, 'apiStore']);
        Route::put('/products/{product}', [ProductController::class, 'apiUpdate']);
        Route::delete('/products/{product}', [ProductController::class, 'apiDestroy']);

        Route::get('/admin/inventory/logs', [InventoryController::class, 'apiLogs']);
        Route::get('/admin/inventory/stock', [InventoryController::class, 'apiStockReport']);

        Route::get('/admin/users', [DashboardController::class, 'apiUsers']);
        Route::post('/admin/users/{user}/toggle', [DashboardController::class, 'apiToggleUser']);
    });

    Route::middleware('role:cashier')->group(function () {
        Route::get('/cashier/dashboard', [DashboardController::class, 'apiCashierDash']);
        Route::get('/cashier/orders', [OrderController::class, 'cashierOrders']);
        Route::post('/cashier/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment']);
    });
});

Route::post('/login', function () {
    return response()->json(['status' => 'login page']);
});

Route::post('/register', function () {
    return response()->json(['status' => 'register page']);
});

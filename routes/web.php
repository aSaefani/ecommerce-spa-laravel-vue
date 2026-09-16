<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::middleware('role:customer')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');

        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/process', [OrderController::class, 'process'])->name('checkout.process');
        Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.my');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('/points', [OrderController::class, 'points'])->name('points');
        Route::post('/products/{product}/wishlist', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    Route::middleware('role:cashier')->group(function () {
        Route::get('/cashier/dashboard', [DashboardController::class, 'cashierDash'])->name('cashier.dashboard');
        Route::get('/cashier/orders', [OrderController::class, 'cashierOrders'])->name('cashier.orders');
        Route::get('/cashier/orders/{order}', [OrderController::class, 'cashierShow'])->name('cashier.orders.show');
        Route::post('/cashier/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('cashier.confirmPayment');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDash'])->name('admin.dashboard');

        Route::prefix('admin')->group(function () {
            Route::resource('categories', CategoryController::class, ['as' => 'admin']);
            Route::get('products', [ProductController::class, 'adminIndex'])->name('admin.products.index');
            Route::resource('products', ProductController::class, ['as' => 'admin', 'except' => 'index']);

            Route::get('inventory/logs', [InventoryController::class, 'index'])->name('admin.inventory.index');
            Route::get('inventory/stock-report', [InventoryController::class, 'stockReport'])->name('admin.inventory.stock');

            Route::get('reports/daily', [ReportController::class, 'daily'])->name('admin.reports.daily');
            Route::get('reports/daily/download', [ReportController::class, 'downloadDaily'])->name('admin.reports.daily.download');

            Route::get('users', [DashboardController::class, 'users'])->name('admin.users');
            Route::post('users/{user}/toggle-status', [DashboardController::class, 'toggleUserStatus'])->name('admin.users.toggle');
        });
    });
});

Route::get('/', function () {
    return view('app');
})->name('home');

Route::post('/payment/callback', [OrderController::class, 'paymentCallback'])->withoutMiddleware('VerifyCsrfToken');

Route::get('/admin/reports/daily/pdf', [ReportController::class, 'downloadDaily'])->name('admin.reports.daily.pdf')->middleware(['auth', 'role:admin']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');


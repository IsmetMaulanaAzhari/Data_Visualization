<?php

use App\Http\Controllers\ApiDashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

// Dashboard (Database)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Categories
Route::resource('categories', CategoryController::class)->except(['show']);

// Products
Route::resource('products', ProductController::class);

// Customers
Route::resource('customers', CustomerController::class);

// Orders
Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

// API Routes (DummyJSON)
Route::prefix('api-data')->name('api.')->group(function () {
    Route::get('/', [ApiDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [ApiDashboardController::class, 'products'])->name('products');
    Route::get('/customers', [ApiDashboardController::class, 'customers'])->name('customers');
    Route::get('/orders', [ApiDashboardController::class, 'orders'])->name('orders');
    Route::get('/categories', [ApiDashboardController::class, 'categories'])->name('categories');
    Route::get('/refresh', [ApiDashboardController::class, 'refreshCache'])->name('refresh');
});

// Weather API Routes (Open-Meteo)
Route::prefix('weather')->name('weather.')->group(function () {
    Route::get('/', [WeatherController::class, 'dashboard'])->name('dashboard');
    Route::get('/cities', [WeatherController::class, 'cities'])->name('cities');
    Route::get('/forecast', [WeatherController::class, 'forecast'])->name('forecast');
    Route::get('/comparison', [WeatherController::class, 'comparison'])->name('comparison');
    Route::get('/refresh', [WeatherController::class, 'refresh'])->name('refresh');
});

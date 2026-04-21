<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Inventory\PurchaseController;
use App\Http\Controllers\Inventory\SaleController;
use App\Http\Controllers\Inventory\StockMovementController;
use App\Http\Controllers\Products\BrandController;
use App\Http\Controllers\Products\CategoryController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Products\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
});

Route::prefix('products')->middleware('jwt.auth')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('products', ProductController::class);
});

Route::prefix('inventory')->middleware('jwt.auth')->group(function () {
    Route::apiResource('purchases', PurchaseController::class)->only(['index', 'store', 'show']);
    Route::apiResource('sales', SaleController::class)->only(['index', 'store', 'show']);
    Route::get('stock-movements', [StockMovementController::class, 'index']);
});

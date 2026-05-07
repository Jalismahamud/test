<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {

    Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::middleware('ensure.business')->group(function () {

            Route::middleware('role:admin,manager,cashier')->group(function () {
                Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
                Route::apiResource('products', ProductController::class);
                Route::apiResource('sales', SaleController::class);
                Route::apiResource('customers', CustomerController::class);
                Route::post('sync/push', [SyncController::class, 'push'])->name('sync.push');
                Route::get('sync/pull', [SyncController::class, 'pull'])->name('sync.pull');
                Route::get('sync/status', [SyncController::class, 'status'])->name('sync.status');
            });

            Route::middleware('role:admin,manager')->group(function () {
                Route::apiResource('categories', CategoryController::class);
                Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
                Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
                Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
            });
        });
    });
});

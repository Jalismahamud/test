<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PosController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'ensure.business'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:admin')
        ->name('users.index');
});

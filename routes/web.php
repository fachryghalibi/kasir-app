<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Boss\CashFlowController;
use App\Http\Controllers\Boss\CashFlowCategoryController;
use App\Http\Controllers\Boss\VendorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard (accessible by both Boss & Employee)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Employee Routes (Kasir) - POS SYSTEM
    |--------------------------------------------------------------------------
    */
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\POSController::class, 'index'])
            ->name('index');
        
        Route::post('/checkout', [\App\Http\Controllers\Employee\POSController::class, 'checkout'])
            ->name('checkout');
        
        Route::get('/history', [\App\Http\Controllers\Employee\TransactionController::class, 'index'])
            ->name('history');
        
        Route::get('/history/{transaction}', [\App\Http\Controllers\Employee\TransactionController::class, 'show'])
            ->name('history.show');

        Route::get('/receipt/{transaction}', [\App\Http\Controllers\Employee\TransactionController::class, 'receipt'])
            ->name('receipt');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Boss Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('boss')->prefix('boss')->name('boss.')->group(function () {
        
        // Product Management
        Route::get('products/search', [\App\Http\Controllers\Boss\ProductController::class, 'search'])
            ->name('products.search');
        Route::resource('products', \App\Http\Controllers\Boss\ProductController::class);
        
        // Category Management
        Route::resource('categories', \App\Http\Controllers\Boss\CategoryController::class);

        // Employee Management
        Route::resource('employees', \App\Http\Controllers\Boss\EmployeeController::class);
        
        // ✨ NEW: Transaction Items Detail (untuk modal di halaman employee detail)
        Route::get('transactions/{transaction}/items', [\App\Http\Controllers\Boss\EmployeeController::class, 'getTransactionItems'])
            ->name('transactions.items');

        // Vendor Management
        Route::get('vendors/search', [\App\Http\Controllers\Boss\VendorController::class, 'search'])
            ->name('vendors.search');
        Route::resource('vendors', VendorController::class);
        
        // Vendor Comparison
        Route::get('vendor-comparison', [\App\Http\Controllers\Boss\VendorComparisonController::class, 'index'])
            ->name('vendor-comparison.index');
        Route::post('vendor-comparison', [\App\Http\Controllers\Boss\VendorComparisonController::class, 'compare'])
            ->name('vendor-comparison.compare');
        
        // Product Tier Prices
        Route::get('products/{product}/tier-prices', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'index'])
            ->name('products.tier-prices.index');
        Route::post('products/{product}/tier-prices', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'store'])
            ->name('products.tier-prices.store');
        Route::delete('tier-prices/{tierPrice}', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'destroy'])
            ->name('tier-prices.destroy');
        
        // Product Vendor Prices
        Route::get('products/{product}/vendor-prices', [\App\Http\Controllers\Boss\ProductVendorPriceController::class, 'index'])
            ->name('products.vendor-prices.index');
        Route::post('products/{product}/vendor-prices', [\App\Http\Controllers\Boss\ProductVendorPriceController::class, 'store'])
            ->name('products.vendor-prices.store');

        /*
        |--------------------------------------------------------------------------
        | CASH FLOW ROUTES - URUTAN SANGAT PENTING!
        |--------------------------------------------------------------------------
        | Custom routes HARUS didefinisikan SEBELUM resource routes
        | Untuk menghindari konflik dengan {cashFlow} parameter
        */
        
        // Cash Flow Categories
        Route::resource('cash-flow-categories', CashFlowCategoryController::class);
        
        // Cash Flow - Custom GET routes (HARUS DI ATAS resource)
        Route::get('cash-flows/pending', [CashFlowController::class, 'pending'])
            ->name('cash-flows.pending');
        Route::get('cash-flows/statistics', [CashFlowController::class, 'statistics'])
            ->name('cash-flows.statistics');
        Route::get('cash-flows/export', [CashFlowController::class, 'export'])
            ->name('cash-flows.export');
        
        // Cash Flow - Approval routes (POST/PATCH)
        Route::post('cash-flows/bulk-approve', [CashFlowController::class, 'bulkApprove'])
            ->name('cash-flows.bulk-approve');
        Route::patch('cash-flows/{cashFlow}/approve', [CashFlowController::class, 'approve'])
            ->name('cash-flows.approve')
            ->whereUuid('cashFlow');
        Route::patch('cash-flows/{cashFlow}/reject', [CashFlowController::class, 'reject'])
            ->name('cash-flows.reject')
            ->whereUuid('cashFlow');
        
        // Cash Flow Resource (HARUS PALING BAWAH dengan constraint)
        Route::resource('cash-flows', CashFlowController::class)
            ->whereUuid('cash_flow');
    });
});
<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Boss\CashFlowController;
use App\Http\Controllers\Boss\CashFlowCategoryController;
use App\Http\Controllers\Boss\VendorController;
use App\Http\Controllers\Boss\CategoryController;
use App\Http\Controllers\Boss\ProductController;
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
        
        /*
        |--------------------------------------------------------------------------
        | PRODUCT MANAGEMENT
        |--------------------------------------------------------------------------
        | Custom routes HARUS didefinisikan SEBELUM resource routes
        */
        
        // Product Search (custom route)
        Route::get('products/search', [ProductController::class, 'search'])
            ->name('products.search');
        
        // Product Resource Routes (CRUD lengkap)
        Route::resource('products', ProductController::class);
        /*
         * Route resource otomatis membuat 7 routes:
         * 
         * GET    /boss/products              -> index()   -> boss.products.index   (Daftar semua produk)
         * GET    /boss/products/create       -> create()  -> boss.products.create  (Form tambah produk)
         * POST   /boss/products              -> store()   -> boss.products.store   (Simpan produk baru)
         * GET    /boss/products/{product}    -> show()    -> boss.products.show    (Detail produk)
         * GET    /boss/products/{product}/edit -> edit()  -> boss.products.edit    (Form edit produk)
         * PUT    /boss/products/{product}    -> update()  -> boss.products.update  (Update produk)
         * PATCH  /boss/products/{product}    -> update()  -> boss.products.update  (Update produk)
         * DELETE /boss/products/{product}    -> destroy() -> boss.products.destroy (Hapus produk)
         */
        
        /*
        |--------------------------------------------------------------------------
        | CATEGORY MANAGEMENT
        |--------------------------------------------------------------------------
        */
        Route::resource('categories', CategoryController::class);
        /*
         * Route resource otomatis membuat 7 routes:
         * 
         * GET    /boss/categories              -> index()   -> boss.categories.index
         * GET    /boss/categories/create       -> create()  -> boss.categories.create
         * POST   /boss/categories              -> store()   -> boss.categories.store
         * GET    /boss/categories/{category}   -> show()    -> boss.categories.show
         * GET    /boss/categories/{category}/edit -> edit() -> boss.categories.edit
         * PUT    /boss/categories/{category}   -> update()  -> boss.categories.update
         * PATCH  /boss/categories/{category}   -> update()  -> boss.categories.update
         * DELETE /boss/categories/{category}   -> destroy() -> boss.categories.destroy
         */

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE MANAGEMENT
        |--------------------------------------------------------------------------
        */
        Route::resource('employees', \App\Http\Controllers\Boss\EmployeeController::class);
        
        // Transaction Items Detail (untuk modal di halaman employee detail)
        Route::get('transactions/{transaction}/items', [\App\Http\Controllers\Boss\EmployeeController::class, 'getTransactionItems'])
            ->name('transactions.items');

        /*
        |--------------------------------------------------------------------------
        | VENDOR MANAGEMENT
        |--------------------------------------------------------------------------
        */
        // Vendor Search (custom route)
        Route::get('vendors/search', [VendorController::class, 'search'])
            ->name('vendors.search');
        
        // Vendor Resource Routes
        Route::resource('vendors', VendorController::class);
        
        // Vendor Comparison
        Route::get('vendor-comparison', [\App\Http\Controllers\Boss\VendorComparisonController::class, 'index'])
            ->name('vendor-comparison.index');
        Route::post('vendor-comparison', [\App\Http\Controllers\Boss\VendorComparisonController::class, 'compare'])
            ->name('vendor-comparison.compare');
        
        /*
        |--------------------------------------------------------------------------
        | PRODUCT TIER PRICES
        |--------------------------------------------------------------------------
        */
        Route::get('products/{product}/tier-prices', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'index'])
            ->name('products.tier-prices.index');
        Route::post('products/{product}/tier-prices', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'store'])
            ->name('products.tier-prices.store');
        Route::delete('tier-prices/{tierPrice}', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'destroy'])
            ->name('tier-prices.destroy');
        
        /*
        |--------------------------------------------------------------------------
        | PRODUCT VENDOR PRICES
        |--------------------------------------------------------------------------
        */
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
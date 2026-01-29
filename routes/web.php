<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
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
    | Employee Routes (Kasir) - POS SYSTEM ✅ AKTIF
    |--------------------------------------------------------------------------
    */
    Route::prefix('pos')->name('pos.')->group(function () {
        // Halaman POS Kasir
        Route::get('/', [\App\Http\Controllers\Employee\POSController::class, 'index'])
            ->name('index');
        
        // Proses Checkout
        Route::post('/checkout', [\App\Http\Controllers\Employee\POSController::class, 'checkout'])
            ->name('checkout');
        
        // Riwayat Transaksi
        Route::get('/history', [\App\Http\Controllers\Employee\TransactionController::class, 'index'])
            ->name('history');
        
        // Detail Transaksi
        Route::get('/history/{transaction}', [\App\Http\Controllers\Employee\TransactionController::class, 'show'])
            ->name('history.show');

        // Print Receipt
        Route::get('/receipt/{transaction}', [\App\Http\Controllers\Employee\TransactionController::class, 'receipt'])
        ->name('receipt');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Boss Only Routes - BELUM DIBUAT (Coming Soon)
    |--------------------------------------------------------------------------
    */
    
    Route::middleware('boss')->prefix('boss')->name('boss.')->group(function () {
    // User Management
    // Route::resource('users', \App\Http\Controllers\Boss\UserController::class);
    
    // Product Management
    Route::get('products/search', [\App\Http\Controllers\Boss\ProductController::class, 'search'])
        ->name('products.search'); // ← TAMBAHAN BARU (HARUS SEBELUM RESOURCE)
    
    Route::resource('products', \App\Http\Controllers\Boss\ProductController::class);
    
    // Category Management
    Route::resource('categories', \App\Http\Controllers\Boss\CategoryController::class);

    Route::resource('vendors', VendorController::class);

    // Vendor Management
    Route::get('vendors/search', [\App\Http\Controllers\Boss\VendorController::class, 'search'])
        ->name('vendors.search'); // ← BONUS: untuk autocomplete vendor nanti (optional)
    
    Route::resource('vendors', \App\Http\Controllers\Boss\VendorController::class);
    
    // Vendor Comparison
    Route::get('vendor-comparison', [\App\Http\Controllers\Boss\VendorComparisonController::class, 'index'])
        ->name('vendor-comparison.index');
    Route::post('vendor-comparison', [\App\Http\Controllers\Boss\VendorComparisonController::class, 'compare'])
        ->name('vendor-comparison.compare');
    
    // Product Tier Prices (akan dibuat di step selanjutnya)
    Route::get('products/{product}/tier-prices', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'index'])
        ->name('products.tier-prices.index');
    Route::post('products/{product}/tier-prices', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'store'])
        ->name('products.tier-prices.store');
    Route::delete('tier-prices/{tierPrice}', [\App\Http\Controllers\Boss\ProductTierPriceController::class, 'destroy'])
        ->name('tier-prices.destroy');
    
    // Product Vendor Prices (akan dibuat di step selanjutnya)
    Route::get('products/{product}/vendor-prices', [\App\Http\Controllers\Boss\ProductVendorPriceController::class, 'index'])
        ->name('products.vendor-prices.index');
    Route::post('products/{product}/vendor-prices', [\App\Http\Controllers\Boss\ProductVendorPriceController::class, 'store'])
        ->name('products.vendor-prices.store');
    
    // Reports
    // Route::get('reports/sales', [\App\Http\Controllers\Boss\ReportController::class, 'sales'])
    //     ->name('reports.sales');
    // Route::get('reports/inventory', [\App\Http\Controllers\Boss\ReportController::class, 'inventory'])
    //     ->name('reports.inventory');
    
    // // Stock Approval
    // Route::get('stock-approvals', [\App\Http\Controllers\Boss\StockApprovalController::class, 'index'])
    //     ->name('stock-approvals.index');
    // Route::post('stock-approvals/{stockLog}/approve', [\App\Http\Controllers\Boss\StockApprovalController::class, 'approve'])
    //     ->name('stock-approvals.approve');
    // Route::post('stock-approvals/{stockLog}/reject', [\App\Http\Controllers\Boss\StockApprovalController::class, 'reject'])
    //     ->name('stock-approvals.reject');
});
    
});
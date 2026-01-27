<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\POSController;
use App\Http\Controllers\Employee\TransactionController;
use App\Http\Controllers\Boss\ProductController;
use App\Http\Controllers\Boss\CategoryController;
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
        // Halaman POS Kasir
        Route::get('/', [POSController::class, 'index'])
            ->name('index');
        
        // Proses Checkout
        Route::post('/checkout', [POSController::class, 'checkout'])
            ->name('checkout');
        
        // Riwayat Transaksi
        Route::get('/history', [TransactionController::class, 'index'])
            ->name('history');
        
        // Detail Transaksi
        Route::get('/history/{transaction}', [TransactionController::class, 'show'])
            ->name('history.show');

        // Print Receipt (PERBAIKAN: Menggunakan {id} bukan {transaction})
        Route::get('/receipt/{id}', [TransactionController::class, 'receipt'])
            ->name('receipt');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Boss Only Routes - Management & Reports
    |--------------------------------------------------------------------------
    */
    Route::middleware('boss')->prefix('boss')->name('boss.')->group(function () {
        
        // Product Management
        Route::resource('products', ProductController::class);
        
        // Category Management
        Route::resource('categories', CategoryController::class);
        
        /*
        |--------------------------------------------------------------------------
        | COMING SOON - Uncomment when ready
        |--------------------------------------------------------------------------
        */
        
        // User Management
        // Route::resource('users', \App\Http\Controllers\Boss\UserController::class);
        
        // Reports
        // Route::prefix('reports')->name('reports.')->group(function () {
        //     Route::get('/sales', [\App\Http\Controllers\Boss\ReportController::class, 'sales'])
        //         ->name('sales');
        //     Route::get('/inventory', [\App\Http\Controllers\Boss\ReportController::class, 'inventory'])
        //         ->name('inventory');
        //     Route::get('/transactions', [\App\Http\Controllers\Boss\ReportController::class, 'transactions'])
        //         ->name('transactions');
        //     Route::get('/profit', [\App\Http\Controllers\Boss\ReportController::class, 'profit'])
        //         ->name('profit');
        // });
        
        // Stock Management & Approval
        // Route::prefix('stock')->name('stock.')->group(function () {
        //     Route::get('/approvals', [\App\Http\Controllers\Boss\StockApprovalController::class, 'index'])
        //         ->name('approvals.index');
        //     Route::post('/approvals/{stockLog}/approve', [\App\Http\Controllers\Boss\StockApprovalController::class, 'approve'])
        //         ->name('approvals.approve');
        //     Route::post('/approvals/{stockLog}/reject', [\App\Http\Controllers\Boss\StockApprovalController::class, 'reject'])
        //         ->name('approvals.reject');
        //     Route::get('/logs', [\App\Http\Controllers\Boss\StockLogController::class, 'index'])
        //         ->name('logs.index');
        // });
        
        // Supplier Management
        // Route::resource('suppliers', \App\Http\Controllers\Boss\SupplierController::class);
        
        // Settings
        // Route::prefix('settings')->name('settings.')->group(function () {
        //     Route::get('/', [\App\Http\Controllers\Boss\SettingController::class, 'index'])
        //         ->name('index');
        //     Route::put('/update', [\App\Http\Controllers\Boss\SettingController::class, 'update'])
        //         ->name('update');
        // });
    });
});
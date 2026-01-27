<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API
Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    });
});

// Protected API
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return response()->json([
            'data' => $request->user(),
        ]);
    });

    // ⚠️ ROUTES DIBAWAH INI AKAN DIBUAT NANTI
    // Uncomment setelah controller dibuat
    
    /*
    Route::prefix('pos')->group(function () {
        Route::get('/products/search', [\App\Http\Controllers\Api\ProductController::class, 'search']);
        Route::get('/products/barcode/{barcode}', [\App\Http\Controllers\Api\ProductController::class, 'getByBarcode']);
        Route::post('/transactions', [\App\Http\Controllers\Api\TransactionController::class, 'store']);
    });

    Route::middleware('boss')->prefix('boss')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\Api\Boss\DashboardController::class, 'stats']);
        Route::get('/reports/sales', [\App\Http\Controllers\Api\Boss\ReportController::class, 'sales']);
    });
    */
});
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class POSController extends Controller
{
    /**
     * Display POS interface
     */
        public function index()
        {
            // Get all active products with category & tier prices
            $products = Product::with(['category', 'tierPrices'])
                ->active()
                ->orderBy('name')
                ->get();
            
            // Get categories for filter
            $categories = \App\Models\Category::active()
                ->orderBy('name')
                ->get();
            
            return view('pos.index', compact('products', 'categories'));
        }

    /**
     * Process checkout
     */
    public function checkout(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash,debit_card,credit_card,qris,transfer',
            'paid_amount' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        try {
            // Process transaction menggunakan service
            $transactionService = app(\App\Services\TransactionService::class);
            $transaction = $transactionService->createTransaction($validated);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'data' => [
                    'transaction' => [
                        'id' => $transaction->id,
                        'uuid' => $transaction->uuid,
                    ],
                    'invoice_number' => $transaction->invoice_number,
                    'total' => $transaction->total,
                    'change' => $transaction->change_amount,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage(),
            ], 422);
        }
    }
}
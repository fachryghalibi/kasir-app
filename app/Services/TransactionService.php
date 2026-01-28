<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    /**
     * Create new transaction with items
     */
    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            // 1. Calculate subtotal first
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            // 2. Calculate discount
            $discountAmount = 0;
            if (!empty($data['discount_type'])) {
                if ($data['discount_type'] === 'percentage') {
                    $discountAmount = ($subtotal * $data['discount_value']) / 100;
                } else {
                    $discountAmount = $data['discount_value'];
                }
            }

            // 3. Calculate tax
            $taxRate = 11; // 11% PPN
            $afterDiscount = $subtotal - $discountAmount;
            $taxAmount = ($afterDiscount * $taxRate) / 100;

            // 4. Calculate total
            $total = $afterDiscount + $taxAmount;

            // 5. Calculate change
            $changeAmount = $data['paid_amount'] - $total;

            if ($changeAmount < 0) {
                throw new \Exception("Jumlah bayar kurang! Total: Rp " . number_format($total, 0, ',', '.'));
            }

            // 6. Create Transaction dengan semua field yang diperlukan
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'payment_method' => $data['payment_method'],
                'paid_amount' => $data['paid_amount'],
                'discount_type' => $data['discount_type'] ?? null,
                'discount_value' => $data['discount_value'] ?? 0,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'total' => $total,
                'change_amount' => $changeAmount,
                'status' => 'completed',
            ]);

            // 7. Process each item
            // 7. Process each item
            foreach ($data['items'] as $item) {
                // Lock product row untuk prevent race condition
                $product = Product::where('id', $item['product_id'])
                    ->with('tierPrices') // Load tier prices
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan");
                }

                // Check stock availability
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup. Tersedia: {$product->stock}");
                }

                // ⭐ FITUR BARU: Hitung harga berdasarkan quantity (tier pricing)
                $pricePerUnit = $product->getPriceByQuantity($item['quantity']);

                // Calculate item subtotal dengan harga tier
                $itemSubtotal = $item['quantity'] * $pricePerUnit;

                // Create transaction item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'price' => $pricePerUnit, // ⭐ Gunakan harga tier
                    'discount_amount' => 0,
                    'subtotal' => $itemSubtotal,
                ]);

                $subtotal += $itemSubtotal;

                // Update stock & create log (sama seperti sebelumnya)
                $stockBefore = $product->stock;
                $product->decrement('stock', $item['quantity']);
                $product->refresh();

                StockLog::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => -$item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->stock,
                    'reference_type' => 'Transaction',
                    'reference_id' => $transaction->id,
                    'notes' => "Penjualan - Invoice: {$transaction->invoice_number}",
                    'user_id' => Auth::id(),
                    'approval_status' => 'approved',
                ]);
            }

            // Reload with relationships
            $transaction->load('items.product');

            return $transaction;
        });
    }
}
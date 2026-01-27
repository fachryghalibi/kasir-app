<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            
            // Transaction relation
            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->onDelete('cascade');
            
            // Product relation
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict'); // Jangan hapus product jika ada transaksi
            
            // Product snapshot saat transaksi
            $table->string('product_name', 200)
                ->comment('Nama produk saat transaksi');
            $table->string('product_sku', 50)
                ->comment('SKU saat transaksi');
            
            // Quantity & Price
            $table->integer('quantity');
            $table->unsignedBigInteger('price')
                ->comment('Harga per unit saat transaksi');
            
            // Discount per item
            $table->unsignedBigInteger('discount_amount')->default(0);
            
            // Subtotal per item
            $table->unsignedBigInteger('subtotal')
                ->comment('quantity × price - discount');
            
            $table->timestamps();
            
            // Indexes
            $table->index('transaction_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
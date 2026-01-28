<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_tier_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('minimum_qty')->comment('Minimal pembelian untuk harga ini');
            $table->unsignedBigInteger('price')->comment('Harga satuan pada tier ini');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index
            $table->index(['product_id', 'minimum_qty']);
            
            // Unique constraint: 1 produk tidak boleh punya 2 tier dengan minimum_qty sama
            $table->unique(['product_id', 'minimum_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tier_prices');
    }
};
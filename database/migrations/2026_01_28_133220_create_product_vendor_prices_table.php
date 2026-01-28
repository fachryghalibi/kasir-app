<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_vendor_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->unsignedBigInteger('purchase_price')->comment('Harga beli dari vendor');
            $table->date('effective_from')->comment('Mulai berlaku');
            $table->date('effective_to')->nullable()->comment('Berakhir (null = masih berlaku)');
            $table->text('notes')->nullable()->comment('Catatan perubahan harga');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index
            $table->index(['product_id', 'vendor_id']);
            $table->index(['effective_from', 'effective_to']);
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_vendor_prices');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Product identification
            $table->string('sku', 50)->unique();
            $table->string('barcode', 50)->unique()->nullable();
            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            
            // Category relation
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('set null');
            
            // Description
            $table->text('description')->nullable();
            
            // Pricing (dalam rupiah, disimpan sebagai integer)
            // Misal: Rp 15.000 = 15000
            $table->unsignedBigInteger('purchase_price')->default(0)
                ->comment('Harga beli dari supplier');
            $table->unsignedBigInteger('selling_price')
                ->comment('Harga jual ke customer');
            
            // Stock management
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5)
                ->comment('Alert jika stock dibawah ini');
            
            // Product details
            $table->string('unit', 20)->default('pcs')
                ->comment('satuan: pcs, kg, liter, dll');
            
            // Images (bisa multiple, disimpan sebagai JSON array)
            $table->json('images')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // Track who created/updated
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes untuk performa query
            $table->index('sku');
            $table->index('barcode');
            $table->index('slug');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('stock');
            $table->fullText(['name', 'description']); // Full-text search
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
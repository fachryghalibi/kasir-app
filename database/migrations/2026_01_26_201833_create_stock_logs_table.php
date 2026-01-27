<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Product relation
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            
            // Type: in (masuk), out (keluar), adjustment (penyesuaian)
            $table->enum('type', ['in', 'out', 'adjustment']);
            
            // Quantity (bisa negatif untuk out)
            $table->integer('quantity');
            
            // Stock sebelum dan sesudah
            $table->integer('stock_before');
            $table->integer('stock_after');
            
            // Reference (misal: transaction_id, purchase_id, dll)
            $table->string('reference_type', 50)->nullable()
                ->comment('Transaction, Purchase, Adjustment');
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Who did this
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Approval (untuk adjustment)
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('approved');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('product_id');
            $table->index('type');
            $table->index('user_id');
            $table->index(['reference_type', 'reference_id']);
            $table->index('approval_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
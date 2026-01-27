<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Invoice number (auto-generated)
            $table->string('invoice_number', 50)->unique();
            
            // Kasir yang melayani
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Customer info (optional, bisa guest)
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_phone', 20)->nullable();
            
            // Transaction totals
            $table->unsignedBigInteger('subtotal')
                ->comment('Total sebelum diskon & pajak');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->string('discount_type', 20)->nullable()
                ->comment('percentage, fixed');
            $table->decimal('discount_value', 10, 2)->nullable();
            
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            
            $table->unsignedBigInteger('total')
                ->comment('Grand total setelah diskon & pajak');
            
            // Payment info
            $table->enum('payment_method', [
                'cash', 
                'debit_card', 
                'credit_card', 
                'qris', 
                'transfer'
            ])->default('cash');
            
            $table->unsignedBigInteger('paid_amount')
                ->comment('Jumlah yang dibayar customer');
            $table->bigInteger('change_amount')->default(0)
                ->comment('Kembalian');
            
            // Status
            $table->enum('status', [
                'pending',
                'completed', 
                'cancelled',
                'refunded'
            ])->default('completed');
            
            // Refund info
            $table->text('refund_reason')->nullable();
            $table->foreignId('refunded_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('refunded_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('invoice_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_method');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
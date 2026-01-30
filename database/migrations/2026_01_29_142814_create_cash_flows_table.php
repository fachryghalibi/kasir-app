<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Type: income (pemasukan) atau expense (pengeluaran)
            $table->enum('type', ['income', 'expense'])->comment('Tipe cash flow');
            
            // Source/Category dari cash flow
            // - 'sale': dari penjualan (otomatis dari Transaction)
            // - 'purchase': dari pembelian (otomatis dari ProductVendorPrice)
            // - 'manual': input manual (iuran, gaji, dll)
            // - 'refund': dari refund transaksi
            // - 'other': lainnya
            $table->enum('source', [
                'sale', 
                'purchase', 
                'manual', 
                'refund', 
                'adjustment',
                'other'
            ])->default('manual');
            
            // Cash Flow Category (hanya untuk manual input)
            $table->foreignId('cash_flow_category_id')
                ->nullable()
                ->constrained('cash_flow_categories')
                ->onDelete('set null')
                ->comment('Kategori manual: Iuran Sampah, Listrik, Gaji, dll');
            
            // Amount (dalam rupiah)
            $table->unsignedBigInteger('amount')->comment('Jumlah uang');
            
            // Description
            $table->text('description')->nullable()->comment('Keterangan cash flow');
            
            // Reference ke model lain (polymorphic)
            // Bisa Transaction, ProductVendorPrice, atau null jika manual
            $table->string('reference_type', 50)->nullable()->comment('Transaction, ProductVendorPrice, Manual');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID dari reference_type');
            
            // Transaction date (tanggal transaksi sebenarnya, bisa beda dengan created_at)
            $table->date('transaction_date')->comment('Tanggal transaksi');
            
            // Payment method (untuk manual input)
            $table->enum('payment_method', [
                'cash', 
                'bank_transfer', 
                'debit_card',
                'credit_card', 
                'qris',
                'other'
            ])->nullable();
            
            // Receipt/Invoice info (untuk manual input)
            $table->string('receipt_number', 100)->nullable()->comment('Nomor kwitansi/invoice');
            $table->string('receipt_file', 255)->nullable()->comment('Path file kwitansi/foto');
            
            // Vendor info (untuk purchase yang manual)
            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('vendors')
                ->onDelete('set null')
                ->comment('Vendor (untuk purchase manual)');
            
            // User yang input/create
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Approval (untuk cash flow besar atau sensitive)
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->comment('Status approval untuk cash flow besar');
            
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes untuk performa query
            $table->index('type');
            $table->index('source');
            $table->index('cash_flow_category_id');
            $table->index(['reference_type', 'reference_id']);
            $table->index('transaction_date');
            $table->index('created_by');
            $table->index('approval_status');
            $table->index('vendor_id');
            
            // Index untuk laporan
            $table->index(['type', 'transaction_date']);
            $table->index(['source', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
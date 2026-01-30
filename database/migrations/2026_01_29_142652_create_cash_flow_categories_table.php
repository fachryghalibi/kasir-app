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
        Schema::create('cash_flow_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Category info
            $table->string('name', 100)->comment('Nama kategori: Iuran Sampah, Listrik, Gaji, dll');
            $table->string('slug', 100)->unique();
            
            // Type: income (pemasukan) atau expense (pengeluaran)
            $table->enum('type', ['income', 'expense'])->comment('Tipe cash flow');
            
            // Description
            $table->text('description')->nullable();
            
            // Icon & Color untuk UI (opsional)
            $table->string('icon', 50)->nullable()->comment('Icon class: fa-trash, fa-bolt, fa-money-bill');
            $table->string('color', 20)->nullable()->comment('Color hex: #FF5733');
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Sort order
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flow_categories');
    }
};
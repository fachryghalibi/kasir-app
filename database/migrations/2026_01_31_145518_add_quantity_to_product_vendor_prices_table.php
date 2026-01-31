<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_vendor_prices', function (Blueprint $table) {
            $table->unsignedInteger('quantity')
                ->default(0)
                ->after('purchase_price')
                ->comment('Jumlah quantity yang dibeli dari vendor ini');
        });
    }

    public function down(): void
    {
        Schema::table('product_vendor_prices', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};